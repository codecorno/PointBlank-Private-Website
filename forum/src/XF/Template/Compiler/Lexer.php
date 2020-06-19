<?php

namespace XF\Template\Compiler;

class Lexer
{
	protected $content;
	protected $endPos;
	protected $offset;
	protected $line;
	protected $tokens;
	protected $stateStack = [];
	protected $stateChanged = false;

	protected $transitions = [
		'plain' => 'statePlain',

		'inVarFull' => 'stateInVarFull',
		'inVarSimple' => 'stateInVarSimple',
		'inExprFull' => 'stateInExprFull',
		'inTag' => 'stateInTag',

		'inParenExpr' => 'stateInParenExpr',
		'inArray' => 'stateInArray',
		'inHash' => 'stateInHash',
		'inDoubleQuote' => 'stateInDoubleQuote'
	];
	protected $startState = 'plain';

	protected $debugTokens = null;

	const LITERAL_REGEX = '[a-zA-Z0-9_][a-zA-Z0-9_-]*(?<!-)';
	const NUMBER_REGEX = '\-?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)';
	const NULL_REGEX = '(?i)null(?=\W)';
	const BOOLEAN_REGEX = '(?i)true(?=\W)|false(?=\W)';
	const WS = '\s+';

	protected $operators = [
		'instanceof' => 'INSTANCEOF',
		'*' => 'MULTIPLY',
		'/' => 'DIVIDE',
		'%' => 'MOD',
		'-' => 'MINUS',
		'+' => 'PLUS',
		'.' => 'CONCAT',
		'?:' => 'TERNARY_SHORT',
		'?' => 'TERNARY_IF',
		':' => 'TERNARY_ELSE',
		'>=' => 'GTEQ',
		'<=' => 'LTEQ',
		'>' => 'GT',
		'<' => 'LT',
		'===' => 'ID',
		'==' => 'EQ',
		'!==' => 'NID',
		'!=' => 'NE',
		'!' => 'BANG',
		'&&' => 'AND',
		'||' => 'OR',
		'and' => 'AND',
		'or' => 'OR',
		'is not' => 'IS_NOT',
		'is' => 'IS',
		//'in',
	];
	protected $operatorRegex;

	protected $doubleQuoteDecodeHtml = false;

	public function __construct()
	{
		if (!$this->operatorRegex)
		{
			$operators = [];
			foreach (array_keys($this->operators) AS $op)
			{
				$lastChar = substr($op, -1);
				$op = preg_quote($op, '#');
				if (preg_match('#^[a-zA-Z0-9_]#', $lastChar))
				{
					$op .= '(?=\W)';
				}
				$operators[] = $op;
			}
			$this->operatorRegex = '(?i)' . implode('|', $operators);
		}
	}

	public function tokenize($content)
	{
		$this->content = strval($content);
		$this->endPos = strlen($this->content) - 1;
		$this->offset = 0;
		$this->line = 1;
		$this->tokens = [];
		$this->stateStack = [$this->startState];
		$state = $this->startState;

		do
		{
			$stateMethod = $this->transitions[$state];
			$this->$stateMethod();

			if ($this->stateChanged)
			{
				if (!$this->stateStack)
				{
					throw new \LogicException("State stack ended up empty");
				}
				$state = end($this->stateStack);
				$this->stateChanged = false;

				if (!isset($this->transitions[$state]))
				{
					throw new \LogicException("Unknown lexer state $state");
				}
			}
		}
		while ($this->offset <= $this->endPos);

		if ($state != $this->startState)
		{
			throw $this->getSyntaxErrorException();
		}

		return $this->tokens;
	}

	public function getTokens()
	{
		return $this->tokens;
	}

	protected function statePlain()
	{
		$match = $this->findMatch([
			'var' => '\\{\\$',
			'expr' => '\\{\\{',
			'comment' => ['<xf:comment>(.*?)</xf:comment>', 1],
			'extensionPoint' => '<!--\[XF:[a-zA-Z0-9_:/-]+\]-->',
			'plain' => ['<xf:plain>(.*?)</xf:plain>', 1],
			'tagOpen' => '<xf:',
			'tagClose' => '</xf:'
		], false);
		if (!$match)
		{
			$this->emitAndMove(Parser::T_PLAIN, substr($this->content, $this->offset));
			return;
		}

		if (strlen($match[2]))
		{
			$this->emitAndMove(Parser::T_PLAIN, $match[2]);
		}

		switch ($match[0])
		{
			case 'var': $this->varFullStart($match[1][0]); break;
			case 'expr': $this->exprFullStart($match[1][0]); break;
			case 'comment': case 'extensionPoint': $this->moveOver($match[1][0]); break;
			case 'plain': $this->emit(Parser::T_PLAIN, $match[1][1]); $this->moveOver($match[1][0]); break;
			case 'tagOpen': $this->tagOpenStart($match[1][0]); break;
			case 'tagClose': $this->tagCloseStart($match[1][0]); break;
			default: throw new \LogicException("Matched regex but in an unexpected way");
		}
	}

	protected function findMatch(array $choices, $atStart = true)
	{
		$regexParts = [];
		$offsets = [];
		$initialOffset = 1;
		$offsetId = $initialOffset;

		foreach ($choices AS $name => $data)
		{
			if (is_array($data))
			{
				$part = $data[0];
				$subPatterns = $data[1];
			}
			else
			{
				$part = $data;
				$subPatterns = 0;
			}

			$regexParts[] = "($part)";

			$offsets[$offsetId] = [$name, $subPatterns];
			$offsetId += $subPatterns + 1;
		}

		$regexParts = str_replace('#', '\\#', implode('|', $regexParts));

		if ($atStart)
		{
			$regex = '#\G(?:' . $regexParts . ')#s';
			$regexModifiers = 0;
		}
		else
		{
			$regex = '#(?:' . $regexParts . ')#s';
			$regexModifiers = PREG_OFFSET_CAPTURE;
		}

		$matched = preg_match($regex, $this->content, $match, $regexModifiers, $this->offset);

		if (!$matched)
		{
			return false;
		}

		$before = '';
		if (!$atStart)
		{
			if ($match[0][1] > $this->offset)
			{
				$before = substr($this->content, $this->offset, $match[0][1] - $this->offset);
			}

			foreach ($match AS &$subPattern)
			{
				$subPattern = $subPattern[0];
			}
		}

		$totalOffsets = count($match);
		$patterns = [];
		$matchName = false;

		for ($i = $initialOffset; $i < $totalOffsets; )
		{
			if (!isset($offsets[$i]))
			{
				$i++;
				continue;
			}

			$offsetInfo = $offsets[$i];

			if (!strlen($match[$i]))
			{
				$i += $offsetInfo[1] + 1;
				continue;
			}

			$matchName  = $offsetInfo[0];
			$patterns[] = $match[$i];
			for ($j = 1; $j <= $offsetInfo[1]; $j++)
			{
				$patterns[] = $match[$i + $j];
			}
			break;
		}

		if (!$matchName)
		{
			throw new \LogicException("Regex matched but couldn't find an offset with a named match");
		}

		if ($atStart)
		{
			return [$matchName, $patterns];
		}
		else
		{
			return [$matchName, $patterns, $before];
		}
	}

	protected function startMatches($option)
	{
		$regex = '#\G(?:' . str_replace('#', '\\#', $option) . ')#s';
		$matched = preg_match($regex, $this->content, $match, 0, $this->offset);

		return $matched ? $match : false;
	}

	protected function varFullStart($string)
	{
		$this->emitAndMove(Parser::T_VAR_START, $string);
		$this->pushState('inVarFull');
	}

	protected function stateInVarFull()
	{
		if (!$this->lexVarGeneric())
		{
			$match = $this->startMatches('\\}');
			if ($match)
			{
				$this->emitAndMove(Parser::T_VAR_END, $match[0]);
				$this->popState();
			}
			else
			{
				throw $this->getSyntaxErrorException();
			}
		}
	}

	protected function varSimpleStart($string)
	{
		$this->emitAndMove(Parser::T_VAR_START, $string);
		$this->pushState('inVarSimple');
	}

	protected function stateInVarSimple()
	{
		if (!$this->lexVarGeneric())
		{
			// move out of the simple var context
			$this->emitAndMove(Parser::T_VAR_END, '');
			$this->popState();
		}
	}

	protected function lexVarGeneric()
	{
		$match = $this->findMatch([
			'split' => '\\.',
			'object' => '->',
			'varFull' => '\\{\\$',
			'exprFull' => '\\{\\{',
			'filter' => '\\|',
			'parenStart' => '\\(',
			'literal' => self::LITERAL_REGEX,
		]);
		if (!$match)
		{
			return false;
		}

		switch ($match[0])
		{
			case 'split': $this->emitAndMove(Parser::T_VAR_DIM, $match[1][0]); break;
			case 'object': $this->emitAndMove(Parser::T_VAR_OBJECT, $match[1][0]); break;
			case 'filter': $this->emitAndMove(Parser::T_FILTER, $match[1][0]); break;
			case 'literal': $this->emitAndMove(Parser::T_LITERAL, $match[1][0]); break;

			case 'varFull': $this->varFullStart($match[1][0]); break;
			case 'exprFull': $this->exprFullStart($match[1][0]); break;
			case 'parenStart': $this->parenStart($match[1][0]); break;

			default: throw new \LogicException("Matched regex but in an unexpected way");
		}

		return true;
	}

	protected function parenStart($value)
	{
		$this->emitAndMove(Parser::T_PAREN_START, $value);
		$this->pushState('inParenExpr');
	}

	protected function exprFullStart($string)
	{
		$this->emitAndMove(Parser::T_EXPR_START, $string);
		$this->pushState('inExprFull');
	}

	protected function stateInExprFull()
	{
		if (!$this->lexExpression())
		{
			$match = $this->startMatches('\\}\\}');
			if ($match)
			{
				$this->emitAndMove(Parser::T_EXPR_END, $match[0]);
				$this->popState();
			}
			else
			{
				throw $this->getSyntaxErrorException();
			}
		}
	}

	protected function parenExprStart($string)
	{
		$this->emitAndMove(Parser::T_PAREN_START, $string);
		$this->pushState('inParenExpr');
	}

	protected function stateInParenExpr()
	{
		if (!$this->lexExpression())
		{
			$match = $this->findMatch([
				'parenEnd' => '\\)',
				'argSep' => ',',
			]);
			if (!$match)
			{
				throw $this->getSyntaxErrorException();
			}

			switch ($match[0])
			{
				case 'parenEnd':
					$this->emitAndMove(Parser::T_PAREN_END, $match[1][0]);
					$this->popState();
					break;

				case 'argSep':
					$this->emitAndMove(Parser::T_ARG_SEP, $match[1][0]);
					break;

				default: throw new \LogicException("Matched regex but in an unexpected way");
			}
		}
	}

	protected function lexExpression()
	{
		$this->skipWhiteSpace();

		$match = $this->findMatch([
			'varFull' => '\\{\\$',
			'varSimple' => '\\$',
			'exprFull' => '\\{\\{',
			'parenStart' => '\\(',
			'arrayStart' => '\\[',
			'hashStart' => '\\{',
			'doubleQuoteStart' => '"',
			'placeholder' => ['##(\d+)', 1],
			'singleQuoted' => ["'([^'\\\\]*(?:\\\\.[^'\\\\]*)*)'", 1],
			'number' => self::NUMBER_REGEX,
			'operator' => $this->operatorRegex,
			'boolean' => self::BOOLEAN_REGEX,
			'null' => self::NULL_REGEX,
			'literal' => self::LITERAL_REGEX,
			'filter' => '\\|',
		]);
		if (!$match)
		{
			return false;
		}

		switch ($match[0])
		{
			case 'varFull': $this->varFullStart($match[1][0]); break;
			case 'varSimple': $this->varSimpleStart($match[1][0]); break;
			case 'exprFull': $this->exprFullStart($match[1][0]); break;
			case 'parenStart': $this->parenStart($match[1][0]); break;
			case 'arrayStart': $this->arrayStart($match[1][0]); break;
			case 'hashStart': $this->hashStart($match[1][0]); break;
			case 'doubleQuoteStart': $this->doubleQuoteStart($match[1][0]); break;
			case 'filter': $this->emitAndMove(Parser::T_FILTER, $match[1][0]); break;
			case 'placeholder': $this->emit(Parser::T_PLACEHOLDER, $match[1][1]); $this->moveOver($match[1][0]); break;
			case 'number': $this->emitAndMove(Parser::T_NUMBER, $match[1][0]); break;
			case 'operator': $this->emitOperator($match[1][0]); break;
			case 'boolean': $this->emitAndMove(Parser::T_BOOLEAN, $match[1][0]); break;
			case 'null': $this->emitAndMove(Parser::T_NULL, $match[1][0]); break;
			case 'literal': $this->emitAndMove(Parser::T_LITERAL, $match[1][0]); break;
			case 'singleQuoted':
				$this->emit(Parser::T_STRING, strtr($match[1][1], [
					'\\\'' => '\'',
					'\\\\' => '\\'
				]));
				$this->moveOver($match[1][0]);
				break;

			default: throw new \LogicException("Matched regex but in an unexpected way");
		}

		return true;
	}

	protected function emitOperator($operator)
	{
		$opLookup = strtolower($operator);
		if (isset($this->operators[$opLookup]))
		{
			$this->emitAndMove(
				constant(__NAMESPACE__ . '\\Parser::T_OP_' . $this->operators[$opLookup]),
				$operator
			);
		}
		else
		{
			throw new \LogicException("Operator $operator not found");
		}
	}

	protected function tagOpenStart($string)
	{
		$this->emitAndMove(Parser::T_TAG_OPEN_START, $string);
		$this->pushState('inTag');
	}

	protected function tagCloseStart($string)
	{
		$this->emitAndMove(Parser::T_TAG_CLOSE_START, $string);
		$this->pushState('inTag');
	}

	protected function stateInTag()
	{
		$this->skipWhiteSpace();

		$match = $this->findMatch([
			'attr' => '=',
			'quote' => '"',
			'endClose' => '/>',
			'end' => '>',
			'literal' => self::LITERAL_REGEX
		]);
		if (!$match)
		{
			throw $this->getSyntaxErrorException();
		}

		$this->doubleQuoteDecodeHtml = true;

		switch ($match[0])
		{
			case 'attr': $this->emitAndMove(Parser::T_TAG_ATTRIBUTE_START, $match[1][0]); break;
			case 'quote': $this->doubleQuoteStart($match[1][0]); break;

			case 'endClose':
				$this->emitAndMove(Parser::T_TAG_END_CLOSE, $match[1][0]);
				$this->popState();
				$this->doubleQuoteDecodeHtml = false;
				break;

			case 'end':
				$this->emitAndMove(Parser::T_TAG_END, $match[1][0]);
				$this->popState();
				$this->doubleQuoteDecodeHtml = false;
				break;

			case 'literal': $this->emitAndMove(Parser::T_LITERAL, $match[1][0]); break;

			default: throw new \LogicException("Matched regex but in an unexpected way");
		}
	}

	protected function arrayStart($string)
	{
		$this->emitAndMove(Parser::T_ARRAY_START, $string);
		$this->pushState('inArray');
	}

	protected function stateInArray()
	{
		if (!$this->lexExpression())
		{
			$match = $this->findMatch([
				'arrayEnd' => '\\]',
				'argSep' => ',',
			]);
			if (!$match)
			{
				throw $this->getSyntaxErrorException();
			}

			switch ($match[0])
			{
				case 'arrayEnd':
					$this->emitAndMove(Parser::T_ARRAY_END, $match[1][0]);
					$this->popState();
					break;

				case 'argSep':
					$this->emitAndMove(Parser::T_ARG_SEP, $match[1][0]);
					break;

				default: throw new \LogicException("Matched regex but in an unexpected way");
			}
		}
	}

	protected function hashStart($string)
	{
		$this->emitAndMove(Parser::T_HASH_START, $string);
		$this->pushState('inHash');
	}

	protected function stateInHash()
	{
		if (!$this->lexExpression())
		{
			$match = $this->findMatch([
				'hashEnd' => '\\}',
				'hashSep' => ':',
				'argSep' => ',',
			]);
			if (!$match)
			{
				throw $this->getSyntaxErrorException();
			}

			switch ($match[0])
			{
				case 'hashEnd':
					$this->emitAndMove(Parser::T_HASH_END, $match[1][0]);
					$this->popState();
					break;

				case 'hashSep':
					$this->emitAndMove(Parser::T_HASH_SEP, $match[1][0]);
					break;

				case 'argSep':
					$this->emitAndMove(Parser::T_ARG_SEP, $match[1][0]);
					break;

				default: throw new \LogicException("Matched regex but in an unexpected way");
			}
		}
	}

	protected function doubleQuoteStart($string)
	{
		$this->emitAndMove(Parser::T_DOUBLE_QUOTE, $string);
		$this->pushState('inDoubleQuote');
	}

	protected function stateInDoubleQuote()
	{
		$match = $this->findMatch([
			'escape' => ['\\\\([^a-zA-Z0-9])', 1],
			'quote' => '"',
			'varFull' => '\\{\\$',
			'exprFull' => '\\{\\{',
		], false);
		if (!$match)
		{
			throw $this->getSyntaxErrorException();
		}

		if (strlen($match[2]))
		{
			$output = $match[2];
			if ($this->doubleQuoteDecodeHtml)
			{
				$output = htmlspecialchars_decode($output);
			}
			$this->emit(Parser::T_STRING, $output);
			$this->moveOver($match[2]);
		}

		switch ($match[0])
		{
			case 'escape':
				$this->emit(Parser::T_STRING, $match[1][1]);
				$this->moveOver($match[1][0]);
				break;

			case 'quote':
				$this->emitAndMove(Parser::T_DOUBLE_QUOTE, $match[1][0]);
				$this->popState();
				break;

			case 'varFull':
				$this->varFullStart($match[1][0]);
				break;

			case 'exprFull':
				$this->exprFullStart($match[1][0]);
				break;

			default:
				throw new \LogicException("Matched regex but in an unexpected way");
		}
	}

	public function getDebugInfo()
	{
		if (!is_array($this->debugTokens))
		{
			$reflection = new \ReflectionClass(__NAMESPACE__ . '\Parser');

			$tokenLookup = [];
			foreach ($reflection->getConstants() AS $name => $value)
			{
				if (substr($name, 0, 2) == 'T_')
				{
					$tokenLookup[$value] = $name;
				}
			}

			$this->debugTokens = $tokenLookup;
		}

		return $this->debugTokens;
	}

	public function getTokenName($token)
	{
		if ($token === false)
		{
			return false;
		}

		if (is_array($token))
		{
			$token = $token[0];
		}

		$lookup = $this->getDebugInfo();
		return isset($lookup[$token]) ? $lookup[$token] : $token;
	}

	public function getTokenNames($tokens)
	{
		if (!is_array($tokens))
		{
			$tokens = func_get_args();
		}

		$lookup = $this->getDebugInfo();

		$expected = [];
		foreach ($tokens AS $tokenValue)
		{
			$expected[$tokenValue] = isset($lookup[$tokenValue]) ? $lookup[$tokenValue] : $tokenValue;
		}

		return $expected;
	}

	public function printable(array $tokens)
	{
		$tokenLookup = $this->getDebugInfo();

		$output = [];
		foreach ($tokens AS $token)
		{
			$tokenName = isset($tokenLookup[$token[0]]) ? $tokenLookup[$token[0]] : $token[0];

			if ($token[1] !== null)
			{
				$output[] = "$tokenName - '$token[1]'";
			}
			else
			{
				$output[] = $tokenName;
			}
		}

		return implode("\n", $output);
	}

	protected function pushState($state)
	{
		$this->stateChanged = true;
		$this->stateStack[] = $state;
	}

	protected function popState()
	{
		$this->stateChanged = true;
		return array_pop($this->stateStack);
	}

	protected function moveOver($value)
	{
		$this->offset += strlen($value);
		$this->line += substr_count($value, "\n");
	}

	protected function emitAndMove($token, $value)
	{
		$this->emit($token, $value);
		$this->moveOver($value);
	}

	protected function emit($token, $value)
	{
		$this->tokens[] = [$token, $value, $this->line];
	}

	protected function skipWhiteSpace($required = false)
	{
		if (preg_match('#\G' . self::WS . '#', $this->content, $match, 0, $this->offset))
		{
			$this->moveOver($match[0]);
			return true;
		}
		else
		{
			if ($required)
			{
				throw $this->getSyntaxErrorException();
			}
			return false;
		}
	}

	protected function getSyntaxErrorException()
	{
		return new Exception(\XF::string([
			\XF::phrase('line_x', ['line' => $this->line]), ': ',
			\XF::phrase('syntax_error')
		]));
	}
}