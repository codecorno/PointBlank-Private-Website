%name Parser_
%declare_class {class Parser }
%token_prefix T_
%syntax_error {
	throw new Exception(\XF::string([
		\XF::phrase('line_x', ['line' => $this->line]), ': ', \XF::phrase('syntax_error')
	]));
}
%include_class {
	public $line = 1;

	/**
	 * @var Ast|null
	 */
	public $ast;

	public $placeholders = [];
}

%left OP_OR .
%left OP_AND .
%left OP_TERNARY_IF OP_TERNARY_ELSE OP_TERNARY_SHORT .
%left OP_EQ OP_NE OP_ID OP_NID OP_GT OP_GTEQ OP_LT OP_LTEQ OP_IS OP_IS_NOT .
%left OP_MINUS OP_PLUS OP_CONCAT .
%left OP_MULTIPLY OP_DIVIDE OP_MOD .
%right OP_U_MINUS .
%right OP_BANG .
%nonassoc OP_INSTANCEOF .

start ::= begin(A). {
	$this->ast = new Ast(A ?: []);
}

begin(RES) ::= begin(A) PLAIN(B). {
	RES = A ?: [];
	RES[] = new Syntax\Str(B, $this->line);
}
begin(RES) ::= begin(A) var(B). {
	RES = A ?: [];
	RES[] = B;
}
begin(RES) ::= begin(A) expression_full(B). {
	RES = A ?: [];
	RES[] = B;
}
begin(RES) ::= begin(A) tag(B). {
	RES = A ?: [];
	RES[] = B;
}
begin ::= .

var(RES) ::= VAR_START LITERAL(A) var_extras(B) filters(C) VAR_END. {
	$res = new Syntax\Variable(A, B ?: [], C ?: [], $this->line);
	RES = $res;
}

var_extras(RES) ::= var_extras(A) VAR_DIM var_array_key(B). {
	RES = A ?: [];
	RES[] = ['array', B];
}
var_extras(RES) ::= var_extras(A) VAR_DIM LITERAL(B) function_args(C). {
	RES = A ?: [];
	RES[] = ['function', new Syntax\Func(B, C ?: [], $this->line)];
}
var_extras(RES) ::= var_extras(A) VAR_OBJECT LITERAL(B) optional_args(C). {
	RES = A ?: [];
	if (is_array(C))
	{
		RES[] = ['function', new Syntax\Func(B, C ?: [], $this->line)];
	}
	else
	{
		RES[] = ['object', new Syntax\Str(B, $this->line)];
	}
}
var_extras ::= .

var_array_key(RES) ::= LITERAL(A). {
	RES = new Syntax\Str(A, $this->line);
}
var_array_key(RES) ::= var(A). {
	RES = A;
}
var_array_key(RES) ::= expression_full(A). {
	RES = A;
}

expression_full(RES) ::= EXPR_START expression(A) EXPR_END. {
	RES = new Syntax\Expression(A, $this->line);
}

tag(RES) ::= TAG_OPEN_START LITERAL(A) tag_attributes(B) TAG_END_CLOSE.
{
	RES = new Syntax\Tag(A, B ?: [], [], $this->line, true);
}
tag(RES) ::= TAG_OPEN_START LITERAL(A) tag_attributes(B) TAG_END begin(C) TAG_CLOSE_START LITERAL(D) TAG_END. {
	if (A != D)
	{
		$closing = D;
		$opening = A;
		throw new Exception(\XF::string([
			\XF::phrase('line_x', ['line' => $this->line]), ': ', \XF::phrase('template_tags_not_well_formed', ['closing' => $closing, 'opening' => $opening])
		]));
	}
	RES = new Syntax\Tag(A, B ?: [], C ?: [], $this->line, false);
}

tag_attributes(RES) ::= tag_attributes(A) LITERAL(B) TAG_ATTRIBUTE_START double_quoted(C). {
	if (!C->parts)
	{
		$attr = new Syntax\Str('', $this->line);
	}
	else if (count(C->parts) == 1)
	{
		$attr = reset(C->parts);
	}
	else
	{
		$attr = C;
	}
	RES = A ?: [];
	RES[B] = $attr;
}
tag_attributes ::= .

expression_part(RES) ::= BOOLEAN(A). {
	RES = new Syntax\Boolean(strtolower(A) == 'true', $this->line);
}
expression_part(RES) ::= NULL. {
	RES = new Syntax\NullValue($this->line);
}
expression_part(RES) ::= NUMBER(A). {
	RES = new Syntax\Number(A, $this->line);
}
expression_part(RES) ::= STRING(A). {
	RES = new Syntax\Str(A, $this->line);
}
expression_part(RES) ::= LITERAL(A) function_args(B). {
	RES = new Syntax\Func(A, B ?: [], $this->line);
}
expression_part(RES) ::= var(A). {
	RES = A;
}
expression_part(RES) ::= double_quoted(A). {
	if (!A->parts)
	{
		RES = new Syntax\Str('', $this->line);
	}
	else if (count(A->parts) == 1)
	{
		RES = reset(A->parts);
	}
	else
	{
		RES = A;
	}
}
expression_part(RES) ::= PAREN_START expression(A) PAREN_END. {
	RES = A;
}
expression_part(RES) ::= [A] . {
	RES = A;
}
expression_part(RES) ::= hash(A). {
	RES = A;
}
expression_part(RES) ::= OP_MINUS(A) expression_part(B). [OP_U_MINUS] {
	RES = new Syntax\UnaryOperator(@A, B, $this->line);
}

expression(RES) ::= expression(A) OP_OR|OP_AND|OP_INSTANCEOF(B) expression(C). {
	RES = new Syntax\BinaryOperator(@B, A, C, $this->line);
}
expression(RES) ::= expression(A) OP_EQ|OP_NE|OP_ID|OP_NID|OP_GT|OP_GTEQ|OP_LT|OP_LTEQ(B) expression(C). {
	RES = new Syntax\BinaryOperator(@B, A, C, $this->line);
}
expression(RES) ::= expression(A) OP_IS LITERAL(C) optional_args(D). {
	RES = new Syntax\Is(A, true, C, D ?: [], $this->line);
}
expression(RES) ::= expression(A) OP_IS_NOT LITERAL(C) optional_args(D). {
	RES = new Syntax\Is(A, false, C, D ?: [], $this->line);
}
expression(RES) ::= expression(A) OP_MINUS|OP_PLUS|OP_MULTIPLY|OP_DIVIDE|OP_MOD|OP_CONCAT(B) expression(C). {
	RES = new Syntax\BinaryOperator(@B, A, C, $this->line);
}
expression(RES) ::= OP_BANG(A) expression(B). {
	RES = new Syntax\UnaryOperator(@A, B, $this->line);
}
expression(RES) ::= expression(A) OP_TERNARY_SHORT expression(B). {
	RES = new Syntax\TernaryShortOperator(A, B, $this->line);
}
expression(RES) ::= expression(A) OP_TERNARY_IF expression(B) OP_TERNARY_ELSE expression(C). {
	RES = new Syntax\TernaryOperator(A, B, C, $this->line);
}
expression(RES) ::= expression_part(A) filters(B). {
	if (B)
	{
		RES = new Syntax\FilterChain(A, B, $this->line);
	}
	else
	{
		RES = A;
	}
}
expression(RES) ::= PLACEHOLDER(A). {
	if (!isset($this->placeholders[A]))
	{
		throw new \Exception("Unknown placeholder used, this should never happen");
	}

	RES = $this->placeholders[A];
}

filters(RES) ::= filters(A) FILTER LITERAL(B) optional_args(C). {
	RES = A ?: [];
	RES[] = [B, C ?: []];
}
filters ::= .

function_args(RES) ::= PAREN_START comma_expression_optional(A) PAREN_END. {
	RES = A ?: [];
}

comma_expression_optional(RES) ::= comma_expression_optional(A) ARG_SEP expression(B). {
	RES = A ?: [];
	RES[] = B;
}
comma_expression_optional(RES) ::= expression(A). {
	RES = [A];
}
comma_expression_optional ::= .

optional_args(RES) ::= function_args(A). {
	RES = A;
}
optional_args ::= .

[RES] ::= ARRAY_START comma_expression_optional(A) ARRAY_END. {
	RES = new Syntax\ArrayExpression(A ?: [], $this->line);
}

hash(RES) ::= HASH_START hash_parts(A) HASH_END. {
	RES = new Syntax\Hash(A ?: [], $this->line);
}

hash_parts(RES) ::= hash_parts(A) ARG_SEP hash_part(B). {
	RES = A ?: [];
	RES[] = B;
}
hash_parts(RES) ::= hash_part(A). {
	RES = [A];
}
hash_parts ::= .

hash_part(RES) ::= expression(A) HASH_SEP|OP_TERNARY_ELSE expression(B). {
	RES = [0 => A, 1 => B];
}

double_quoted(RES) ::= DOUBLE_QUOTE double_quote_inner(A) DOUBLE_QUOTE. {
	RES = new Syntax\Quoted(A ?: [], $this->line);
}

double_quote_inner(RES) ::= double_quote_inner(A) STRING(B). {
	RES = A ?: [];
	RES[] = new Syntax\Str(B, $this->line);
}
double_quote_inner(RES) ::= double_quote_inner(A) var(B). {
	RES = A ?: [];
	RES[] = B;
}
double_quote_inner(RES) ::= double_quote_inner(A) expression_full(B). {
	RES = A ?: [];
	RES[] = B;
}
double_quote_inner ::= .