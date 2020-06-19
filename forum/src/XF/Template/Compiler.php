<?php

namespace XF\Template;

use XF\Template\Compiler\Ast;
use XF\Template\Compiler\CodeScope;
use XF\Template\Compiler\Exception;
use XF\Template\Compiler\Func\AbstractFn;
use XF\Template\Compiler\Lexer;
use XF\Template\Compiler\Parser;
use XF\Template\Compiler\Syntax\AbstractSyntax;
use XF\Template\Compiler\Syntax\Expression;
use XF\Template\Compiler\Syntax\Str;
use XF\Template\Compiler\Syntax\Variable;
use XF\Template\Compiler\Tag\AbstractTag;

class Compiler
{
	public $finalVarName = '$__finalCompiled';
	public $variableContainer = '$__vars';
	public $templaterVariable = '$__templater';
	public $macroArgumentsVariable = '$__arguments';

	public $defaultContext = [
		'escape' => true
	];

	/**
	 * @var AbstractTag[]
	 */
	protected $tags = [];

	/**
	 * @var AbstractFn[]
	 */
	protected $functions = [];

	/**
	 * @var null|\XF\Language
	 */
	protected $language = null;

	/** @var CodeScope */
	protected $codeScope;

	protected $macros;

	protected $defaultTags = [
		'ad' => 'Ad',
		'avatar' => 'Avatar',
		'breadcrumb' => 'Breadcrumb',
		'button' => 'Button',
		'callback' => 'Callback',
		'captcha' => 'Captcha',
		'captcharow' => 'CaptchaRow',
		'checkbox' => 'CheckBoxRow',
		'checkboxrow' => 'CheckBoxRow',
		'copyright' => 'Copyright',
		'codeeditor' => 'CodeEditorRow',
		'codeeditorrow' => 'CodeEditorRow',
		'corejs' => 'CoreJs',
		'csrf' => 'Csrf',
		'css' => 'Css',
		'datalist' => 'DataList',
		'datarow' => 'DataRow',
		'date' => 'Date',
		'dateinput' => 'DateInputRow',
		'dateinputrow' => 'DateInputRow',
		'description' => 'Description',
		'editor' => 'EditorRow',
		'editorrow' => 'EditorRow',
		'fa' => 'FontAwesome',
		'foreach' => 'ForeachTag',
		'form' => 'Form',
		'formrow' => 'FormRow',
		'h1' => 'H1',
		'head' => 'Head',
		'hiddenval' => 'HiddenVal',
		'if' => 'IfTag',
		'include' => 'IncludeTag',
		'inforow' => 'InfoRow',
		'js' => 'Js',
		'likes' => 'Likes',
		'macro' => 'Macro',
		'mustache' => 'Mustache',
		'numberbox' => 'NumberBoxRow',
		'numberboxrow' => 'NumberBoxRow',
		'page' => 'Page',
		'pageaction' => 'PageAction',
		'pagenav' => 'PageNav',
		'passwordbox' => 'PasswordBoxRow',
		'passwordboxrow' => 'PasswordBoxRow',
		'prefixinput' => 'PrefixInputRow',
		'prefixinputrow' => 'PrefixInputRow',
		'radio' => 'RadioRow',
		'radiorow' => 'RadioRow',
		'react' => 'React',
		'reaction' => 'Reaction',
		'reactions' => 'Reactions',
		'redirect' => 'RedirectInput',
		'select' => 'SelectRow',
		'selectrow' => 'SelectRow',
		'set' => 'Set',
		'showignored' => 'ShowIgnored',
		'sidebar' => 'Sidebar',
		'sidenav' => 'SideNav',
		'submitrow' => 'SubmitRow',
		'telbox' => 'TelBoxRow',
		'telboxrow' => 'TelBoxRow',
		'textarea' => 'TextAreaRow',
		'textarearow' => 'TextAreaRow',
		'textbox' => 'TextBoxRow',
		'textboxrow' => 'TextBoxRow',
		'title' => 'Title',
		'tokeninput' => 'TokenInputRow',
		'tokeninputrow' => 'TokenInputRow',
		'trim' => 'Trim',
		'upload' => 'UploadRow',
		'uploadrow' => 'UploadRow',
		'useractivity' => 'UserActivity',
		'userblurb' => 'UserBlurb',
		'userbanners' => 'UserBanners',
		'username' => 'Username',
		'usertitle' => 'UserTitle',
		'widget' => 'Widget',
		'widgetpos' => 'WidgetPos',
		'wrap' => 'Wrap'
	];

	protected $defaultFunctions = [
		'empty' => 'EmptyFn',
		'include' => 'IncludeFn',
		'phrase' => 'Phrase',
		'preescaped' => 'PreEscaped',
		'vars' => 'Vars'
	];

	public function __construct(array $tags = [], array $functions = [], $withDefault = true)
	{
		if ($withDefault)
		{
			$this->setDefaultTags();
			$this->setDefaultFunctions();
		}

		$this->setTags($tags);
		$this->setFunctions($functions);
	}

	public function setLanguage(\XF\Language $language)
	{
		$this->language = $language;
	}

	public function resetLanguage()
	{
		$this->language = null;
	}

	/**
	 * @return null|\XF\Language
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	public function compile($string, \XF\Language $language = null)
	{
		return $this->compileAst($this->compileToAst($string), $language);
	}

	/**
	 * @param string $string
	 * @param array $placeholders
	 *
	 * @return null|Ast
	 */
	public function compileToAst($string, array $placeholders = [])
	{
		$lexer = new Lexer();
		$parser = new Parser();
		$parser->placeholders = $placeholders;
		$tokens = $lexer->tokenize($string);
		foreach ($tokens AS $token)
		{
			$parser->doParse($token[0], $token[1]);
			$parser->line = $token[2];
		}
		$parser->doParse(0, 0);

		return $parser->ast;
	}

	public function stringAst($string)
	{
		return new Ast([
			new Str($string, 1)
		]);
	}

	public function reset()
	{
		$this->codeScope = new CodeScope($this->finalVarName, $this);
		$this->macros = [];

		foreach ($this->tags AS $tag)
		{
			$tag->reset();
		}
		foreach ($this->functions AS $function)
		{
			$function->reset();
		}
	}

	public function compileAst(Ast $ast, \XF\Language $language = null)
	{
		$this->reset();

		$oldLanguage = $this->language;
		if ($language)
		{
			$this->language = $language;
		}

		$this->traverseBlockChildren($ast->children, $this->defaultContext);
		$code = $this->getCompletedTemplateCode();

		$this->language = $oldLanguage;

		return $code;
	}

	/**
	 * @param AbstractSyntax[] $children
	 * @param array $context
	 *
	 * @return $this
	 */
	public function traverseBlockChildren(array $children, array $context)
	{
		foreach ($children AS $child)
		{
			$this->inline($child->compile($this, $context, false));
		}

		return $this;
	}

	/**
	 * @param AbstractSyntax[] $list
	 * @param array $context
	 *
	 * @return string
	 */
	public function compileInlineList(array $list, array $context)
	{
		$output = [];

		foreach ($list AS $item)
		{
			$code = $item->compile($this, $context, true);
			if (is_string($code) && $code != '')
			{
				$output[] = $code;
			}
		}

		if ($output)
		{
			return $this->simplifyInlineCode(implode(' . ', $output));
		}
		else
		{
			return "''";
		}
	}

	public function compileToArraySyntax($syntax, $name, array $context)
	{
		if (is_array($syntax))
		{
			$compiled = $this->compileInlineList($syntax, $context);
		}
		else if ($syntax instanceof AbstractSyntax)
		{
			$compiled = $syntax->compile($this, $context, true);
		}
		else
		{
			throw new \InvalidArgumentException("Syntax argument must be AbstractSyntax object or array");
		}

		return "\n" . $this->indent() . "\t"
			. $this->getStringCode($name) . ' => ' . $compiled . ",";
	}

	public function forceToExpression(AbstractSyntax $input)
	{
		$originalInput = $input;

		if ($input instanceof Compiler\Syntax\Quoted)
		{
			$input = $input->parts;
		}
		else if ($input instanceof Compiler\Syntax\Str)
		{
			$input = $input->content;
		}
		else if ($input instanceof Compiler\Syntax\AbstractSyntax)
		{
			return $input;
		}

		$input = (array)$input;

		$placeholders = [];
		$placeholderId = 0;

		$expression = '{{ ';
		foreach ($input AS $part)
		{
			if (is_string($part))
			{
				$expression .= $part;
			}
			else if ($part instanceof Compiler\Syntax\Str)
			{
				$expression .= $part->content;
			}
			else
			{
				$expression .= " ##$placeholderId ";
				$placeholders[$placeholderId] = $part;
				$placeholderId++;
			}
		}
		$expression .= ' }}';

		try
		{
			/** @var AbstractSyntax $output */
			$output = $this->compileToAst($expression, $placeholders)->children[0];
			if ($output instanceof Expression)
			{
				/** @var Expression $output */
				$output = $output->expression;
			}
			$output->line = $originalInput->line;
			return $output;
		}
		catch (Exception $e)
		{
			throw $originalInput->exception(\XF::phrase('expected_valid_expression'));
		}
	}

	public function compileForcedExpression(AbstractSyntax $input, array $context)
	{
		$context['escape'] = false;
		return $this->forceToExpression($input)->compile($this, $context, true);
	}

	/**
	 * @param AbstractSyntax $syntax
	 *
	 * @return Variable
	 *
	 * @throws Compiler\Exception
	 */
	public function requireSimpleVariable(AbstractSyntax $syntax)
	{
		if ($syntax instanceof Variable)
		{
			if ($syntax->isSimple())
			{
				return $syntax;
			}
		}
		else if ($syntax instanceof Str)
		{
			if (strlen($syntax->content))
			{
				$parts = explode('.', $syntax->content);
				$name = array_shift($parts);
				if (preg_match('#^\$' . Lexer::LITERAL_REGEX . '$#siU', $name))
				{
					$name = substr($name, 1);
					$dimensions = [];
					$matched = true;

					foreach ($parts AS $part)
					{
						if (preg_match('#^' . Lexer::LITERAL_REGEX . '$#siU', $part))
						{
							$dimensions[] = ['array', new Str($part, $syntax->line)];
						}
						else
						{
							$matched = false;
							break;
						}
					}

					if ($matched)
					{
						return new Variable($name, $dimensions, [], $syntax->line);
					}
				}
			}
		}

		throw new Exception(\XF::string([
			\XF::phrase('line_x', ['line' => $syntax->line]), ': ',
			\XF::phrase('expected_simple_variable_reference_but_did_not_receive_one')
		]));
	}

	public function compileSimpleVariable(AbstractSyntax $input, array $context)
	{
		return $this->requireSimpleVariable($input)->compile($this, $context, true);
	}

	public function getStringCode($string)
	{
		return "'" . addcslashes($string, "\\'") . "'";
	}

	public function simplifyInlineCode($code)
	{
		//$code = preg_replace('#(?<!\\\\)\' \. \'#', '', $code);

		return $code;
	}

	public function defineMacro($name, $functionCode)
	{
		$nameString = $this->getStringCode($name);
		$this->macros[$name] = "{$nameString} => function({$this->templaterVariable}, array {$this->macroArgumentsVariable}, array {$this->variableContainer})
{
	{$functionCode}
},";
	}

	public function getMacros()
	{
		return $this->macros;
	}

	protected function getCompletedTemplateCode()
	{
		$output = implode("\n", $this->getOutput());
		$macros = implode("\n", $this->macros);

		return "return array('macros' => array({$macros}), 'code' => function({$this->templaterVariable}, array {$this->variableContainer})
{
	{$this->finalVarName} = '';
{$output}
	return {$this->finalVarName};
});";
	}

	public function getCodeScope()
	{
		return $this->codeScope;
	}

	public function setCodeScope(CodeScope $codeScope)
	{
		$this->codeScope = $codeScope;
	}

	public function getOutput()
	{
		return $this->codeScope->getOutput();
	}

	public function write($code)
	{
		$this->codeScope->write($code);
		return $this;
	}

	public function inline($code)
	{
		$this->codeScope->inline($code);
		return $this;
	}

	public function currentVar()
	{
		return $this->codeScope->currentVar();
	}

	public function pushTempVar($init = true)
	{
		return $this->codeScope->pushTempVar($init);
	}

	public function pushVar($var)
	{
		$this->codeScope->pushVar($var);
		return $this;
	}

	public function popVar()
	{
		return $this->codeScope->popVar();
	}

	public function getTempVar()
	{
		return $this->codeScope->getTempVar();
	}

	public function indent()
	{
		return $this->codeScope->indent();
	}

	public function pushIndent()
	{
		$this->codeScope->pushIndent();
		return $this;
	}

	public function popIndent()
	{
		$this->codeScope->popIndent();
		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return AbstractTag|false
	 */
	public function getTag($name)
	{
		return isset($this->tags[$name]) ? $this->tags[$name] : false;
	}

	public function setDefaultTags()
	{
		return $this->setTags($this->defaultTags);
	}

	public function setTags(array $tags)
	{
		foreach ($tags AS $name => $tag)
		{
			$this->setTag($name, $tag);
		}

		return $this;
	}

	public function setTag($name, $tag)
	{
		if (is_string($tag))
		{
			$class = $tag[0] == '\\' ? $tag : __NAMESPACE__ . '\\Compiler\\Tag\\' . $tag;
			$tag = new $class($name);
		}
		if (!($tag instanceof AbstractTag))
		{
			throw new \InvalidArgumentException("Tag must be a class name or object that is an instance of AbstractTag");
		}

		$this->tags[$name] = $tag;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return AbstractFn|false
	 */
	public function getFunction($name)
	{
		$name = strtolower($name);
		return isset($this->functions[$name]) ? $this->functions[$name] : false;
	}

	public function setDefaultFunctions()
	{
		return $this->setFunctions($this->defaultFunctions);
	}

	public function setFunctions(array $functions)
	{
		foreach ($functions AS $name => $function)
		{
			$this->setFunction($name, $function);
		}

		return $this;
	}

	public function setFunction($name, $function)
	{
		$name = strtolower($name);

		if (is_string($function))
		{
			$class = $function[0] == '\\' ? $function : __NAMESPACE__ . '\\Compiler\\Func\\' . $function;
			$function = new $class($name);
		}
		if (!($function instanceof AbstractFn))
		{
			throw new \InvalidArgumentException("Function must be a class name or object that is an instance of AbstractFn");
		}

		$this->functions[$name] = $function;

		return $this;
	}
}