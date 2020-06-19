<?php

namespace XF\Template\Compiler;
use XF\Template\Compiler\Syntax;

/* Driver template for the PHP_Parser_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class Parser_yyToken implements \ArrayAccess
{
    public $string = '';
    public $metadata = [];

    function __construct($s, $m = [])
    {
        if ($s instanceof Parser_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof Parser_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof Parser_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof Parser_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class Parser_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here

// declare_class is output here
#line 2 "Parser.y"
class Parser #line 102 "Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 9 "Parser.y"

	public $line = 1;

	/**
	 * @var Ast|null
	 */
	public $ast;

	public $placeholders = [];
#line 117 "Parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const T_OP_OR                          =  1;
    const T_OP_AND                         =  2;
    const T_OP_TERNARY_IF                  =  3;
    const T_OP_TERNARY_ELSE                =  4;
    const T_OP_TERNARY_SHORT               =  5;
    const T_OP_EQ                          =  6;
    const T_OP_NE                          =  7;
    const T_OP_ID                          =  8;
    const T_OP_NID                         =  9;
    const T_OP_GT                          = 10;
    const T_OP_GTEQ                        = 11;
    const T_OP_LT                          = 12;
    const T_OP_LTEQ                        = 13;
    const T_OP_IS                          = 14;
    const T_OP_IS_NOT                      = 15;
    const T_OP_MINUS                       = 16;
    const T_OP_PLUS                        = 17;
    const T_OP_CONCAT                      = 18;
    const T_OP_MULTIPLY                    = 19;
    const T_OP_DIVIDE                      = 20;
    const T_OP_MOD                         = 21;
    const T_OP_U_MINUS                     = 22;
    const T_OP_BANG                        = 23;
    const T_OP_INSTANCEOF                  = 24;
    const T_PLAIN                          = 25;
    const T_VAR_START                      = 26;
    const T_LITERAL                        = 27;
    const T_VAR_END                        = 28;
    const T_VAR_DIM                        = 29;
    const T_VAR_OBJECT                     = 30;
    const T_EXPR_START                     = 31;
    const T_EXPR_END                       = 32;
    const T_TAG_OPEN_START                 = 33;
    const T_TAG_END_CLOSE                  = 34;
    const T_TAG_END                        = 35;
    const T_TAG_CLOSE_START                = 36;
    const T_TAG_ATTRIBUTE_START            = 37;
    const T_BOOLEAN                        = 38;
    const T_NULL                           = 39;
    const T_NUMBER                         = 40;
    const T_STRING                         = 41;
    const T_PAREN_START                    = 42;
    const T_PAREN_END                      = 43;
    const T_PLACEHOLDER                    = 44;
    const T_FILTER                         = 45;
    const T_ARG_SEP                        = 46;
    const T_ARRAY_START                    = 47;
    const T_ARRAY_END                      = 48;
    const T_HASH_START                     = 49;
    const T_HASH_END                       = 50;
    const T_HASH_SEP                       = 51;
    const T_DOUBLE_QUOTE                   = 52;
    const YY_NO_ACTION = 162;
    const YY_ACCEPT_ACTION = 161;
    const YY_ERROR_ACTION = 160;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 355;
static public $yy_action = [
 /*     0 */     7,    7,   14,   12,    8,    5,    5,    5,    5,    5,
 /*    10 */     5,    5,    5,   59,   54,    6,    6,    6,    6,    6,
 /*    20 */     6,   62,   21,    7,   10,    7,    7,   14,    9,    8,
 /*    30 */     5,    5,    5,    5,    5,    5,    5,    5,   59,   54,
 /*    40 */     6,    6,    6,    6,    6,    6,   56,   28,    7,   24,
 /*    50 */    12,   11,    7,    7,   14,   31,    8,    5,    5,    5,
 /*    60 */     5,    5,    5,    5,    5,   59,   54,    6,    6,    6,
 /*    70 */     6,    6,    6,   87,   88,    7,    6,    6,    6,    6,
 /*    80 */     6,    6,    4,   90,    7,    7,    7,   14,   32,    8,
 /*    90 */     5,    5,    5,    5,    5,    5,    5,    5,   59,   54,
 /*   100 */     6,    6,    6,    6,    6,    6,    7,   14,    7,    8,
 /*   110 */     5,    5,    5,    5,    5,    5,    5,    5,   59,   54,
 /*   120 */     6,    6,    6,    6,    6,    6,    7,   64,    7,    7,
 /*   130 */     7,   14,   60,    8,    5,    5,    5,    5,    5,    5,
 /*   140 */     5,    5,   59,   54,    6,    6,    6,    6,    6,    6,
 /*   150 */    87,   75,    7,    5,    5,    5,    5,    5,    5,    5,
 /*   160 */     5,   59,   54,    6,    6,    6,    6,    6,    6,   16,
 /*   170 */    57,    7,   87,   85,   76,   48,   15,   73,   33,   56,
 /*   180 */    27,   10,   34,  100,   74,   30,   89,   81,   16,   49,
 /*   190 */    92,   65,   79,   70,   68,   13,   76,   84,   56,   27,
 /*   200 */     2,   46,    1,   22,   37,   32,   74,   30,   89,   81,
 /*   210 */    65,   79,   70,   68,   13,   76,   78,   82,   83,    2,
 /*   220 */    69,    1,   76,   40,   32,   74,   30,   89,   81,   50,
 /*   230 */    34,   76,   74,   30,   89,   81,   86,   76,   91,   40,
 /*   240 */    29,   74,   30,   89,   81,   47,   76,   74,   80,   89,
 /*   250 */    81,   55,   76,   61,   38,   67,   74,   30,   89,   81,
 /*   260 */    42,   76,   74,   30,   89,   81,   26,   76,   25,   58,
 /*   270 */    61,   74,   30,   89,   81,   35,   76,   74,   30,   89,
 /*   280 */    81,   23,   76,   20,   39,   71,   74,   30,   89,   81,
 /*   290 */    36,   76,   74,   30,   89,   81,    3,  135,   76,   43,
 /*   300 */    95,   74,   30,   89,   81,   72,   44,   17,   74,   30,
 /*   310 */    89,   81,  135,   76,   93,   56,   19,   53,  135,   76,
 /*   320 */    11,   41,   52,   74,   30,   89,   81,   45,   56,   74,
 /*   330 */    30,   89,   81,   11,   93,   56,   63,   77,   87,   94,
 /*   340 */    11,   66,   52,   98,  135,   51,    6,    6,    6,  161,
 /*   350 */    18,    7,   99,   96,   97,
];
    static public $yy_lookahead = [
 /*     0 */     1,    2,    3,    4,    5,    6,    7,    8,    9,   10,
 /*    10 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*    20 */    21,   43,   27,   24,   46,    1,    2,    3,    4,    5,
 /*    30 */     6,    7,    8,    9,   10,   11,   12,   13,   14,   15,
 /*    40 */    16,   17,   18,   19,   20,   21,   26,   27,   24,   27,
 /*    50 */    51,   31,    1,    2,    3,   27,    5,    6,    7,    8,
 /*    60 */     9,   10,   11,   12,   13,   14,   15,   16,   17,   18,
 /*    70 */    19,   20,   21,   62,   63,   24,   16,   17,   18,   19,
 /*    80 */    20,   21,   42,   32,   24,    1,    2,    3,   52,    5,
 /*    90 */     6,    7,    8,    9,   10,   11,   12,   13,   14,   15,
 /*   100 */    16,   17,   18,   19,   20,   21,    2,    3,   24,    5,
 /*   110 */     6,    7,    8,    9,   10,   11,   12,   13,   14,   15,
 /*   120 */    16,   17,   18,   19,   20,   21,   24,   43,   24,    1,
 /*   130 */     2,    3,   27,    5,    6,    7,    8,    9,   10,   11,
 /*   140 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*   150 */    62,   63,   24,    6,    7,    8,    9,   10,   11,   12,
 /*   160 */    13,   14,   15,   16,   17,   18,   19,   20,   21,   16,
 /*   170 */    27,   24,   62,   63,   56,   60,   23,   34,   35,   26,
 /*   180 */    27,   46,   64,   48,   66,   67,   68,   69,   16,   71,
 /*   190 */    72,   38,   39,   40,   41,   42,   56,   44,   26,   27,
 /*   200 */    47,   65,   49,   27,   64,   52,   66,   67,   68,   69,
 /*   210 */    38,   39,   40,   41,   42,   56,   56,   57,   58,   47,
 /*   220 */    35,   49,   56,   64,   52,   66,   67,   68,   69,   70,
 /*   230 */    64,   56,   66,   67,   68,   69,   28,   56,   72,   64,
 /*   240 */    27,   66,   67,   68,   69,   70,   56,   66,   67,   68,
 /*   250 */    69,   60,   56,   45,   64,   62,   66,   67,   68,   69,
 /*   260 */    64,   56,   66,   67,   68,   69,   37,   56,   59,   64,
 /*   270 */    45,   66,   67,   68,   69,   64,   56,   66,   67,   68,
 /*   280 */    69,   27,   56,   73,   64,   66,   66,   67,   68,   69,
 /*   290 */    64,   56,   66,   67,   68,   69,   46,   74,   56,   64,
 /*   300 */    50,   66,   67,   68,   69,   62,   64,   55,   66,   67,
 /*   310 */    68,   69,   74,   56,   25,   26,   29,   30,   74,   56,
 /*   320 */    31,   64,   33,   66,   67,   68,   69,   64,   26,   66,
 /*   330 */    67,   68,   69,   31,   25,   26,   56,   57,   62,   63,
 /*   340 */    31,   61,   33,   41,   74,   36,   19,   20,   21,   54,
 /*   350 */    55,   24,   56,   57,   52,
	];
    const YY_SHIFT_USE_DFLT = -23;
    const YY_SHIFT_MAX = 61;
    static public $yy_shift_ofst = [
 /*     0 */   -23,  153,  153,  153,  153,  153,  153,  153,  153,  153,
 /*    10 */   153,  153,  153,  153,  153,  153,  172,  309,  289,   20,
 /*    20 */   302,   40,   40,   40,   40,  287,   36,   40,   40,  -23,
 /*    30 */   -23,  -23,  -23,  -23,   -1,   24,   51,   84,  128,  128,
 /*    40 */   128,  104,  147,  147,   60,  327,  143,  -22,  208,  250,
 /*    50 */   135,  105,   28,   22,  254,  225,  213,  229,  102,  176,
 /*    60 */   185,   -5,
	];
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 33;
    static public $yy_reduce_ofst = [
 /*     0 */   295,  118,  159,  166,  175,  242,  263,  257,  235,  196,
 /*    10 */   190,  226,  220,  140,  211,  205,  181,  160,  160,  280,
 /*    20 */   296,  276,   11,  110,   88,  115,  219,  243,  193,  209,
 /*    30 */   191,  136,  210,  252,
	];
    static public $yyExpectedTokens = [
        /* 0 */ [],
        /* 1 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 2 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 3 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 4 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 5 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 6 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 7 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 8 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 9 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 10 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 11 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 12 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 13 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 14 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 15 */ [16, 23, 26, 27, 38, 39, 40, 41, 42, 44, 47, 49, 52,],
        /* 16 */ [16, 26, 27, 38, 39, 40, 41, 42, 47, 49, 52,],
        /* 17 */ [25, 26, 31, 33, 36,],
        /* 18 */ [25, 26, 31, 33,],
        /* 19 */ [26, 27, 31,],
        /* 20 */ [26, 31, 41, 52,],
        /* 21 */ [42,],
        /* 22 */ [42,],
        /* 23 */ [42,],
        /* 24 */ [42,],
        /* 25 */ [29, 30,],
        /* 26 */ [52,],
        /* 27 */ [42,],
        /* 28 */ [42,],
        /* 29 */ [],
        /* 30 */ [],
        /* 31 */ [],
        /* 32 */ [],
        /* 33 */ [],
        /* 34 */ [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24, 51,],
        /* 35 */ [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 36 */ [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24, 32,],
        /* 37 */ [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24, 43,],
        /* 38 */ [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 39 */ [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 40 */ [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 41 */ [2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 42 */ [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 43 */ [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24,],
        /* 44 */ [16, 17, 18, 19, 20, 21, 24,],
        /* 45 */ [19, 20, 21, 24,],
        /* 46 */ [27, 34, 35,],
        /* 47 */ [43, 46,],
        /* 48 */ [28, 45,],
        /* 49 */ [46, 50,],
        /* 50 */ [46, 48,],
        /* 51 */ [27,],
        /* 52 */ [27,],
        /* 53 */ [27,],
        /* 54 */ [27,],
        /* 55 */ [45,],
        /* 56 */ [27,],
        /* 57 */ [37,],
        /* 58 */ [24,],
        /* 59 */ [27,],
        /* 60 */ [35,],
        /* 61 */ [27,],
        /* 62 */ [],
        /* 63 */ [],
        /* 64 */ [],
        /* 65 */ [],
        /* 66 */ [],
        /* 67 */ [],
        /* 68 */ [],
        /* 69 */ [],
        /* 70 */ [],
        /* 71 */ [],
        /* 72 */ [],
        /* 73 */ [],
        /* 74 */ [],
        /* 75 */ [],
        /* 76 */ [],
        /* 77 */ [],
        /* 78 */ [],
        /* 79 */ [],
        /* 80 */ [],
        /* 81 */ [],
        /* 82 */ [],
        /* 83 */ [],
        /* 84 */ [],
        /* 85 */ [],
        /* 86 */ [],
        /* 87 */ [],
        /* 88 */ [],
        /* 89 */ [],
        /* 90 */ [],
        /* 91 */ [],
        /* 92 */ [],
        /* 93 */ [],
        /* 94 */ [],
        /* 95 */ [],
        /* 96 */ [],
        /* 97 */ [],
        /* 98 */ [],
        /* 99 */ [],
        /* 100 */ [],
	];
    static public $yy_default = [
 /*     0 */   106,  153,  146,  160,  146,  160,  160,  160,  160,  160,
 /*    10 */   160,  160,  160,  160,  160,  160,  160,  160,  101,  160,
 /*    20 */   160,  148,  148,  148,  148,  142,  160,  160,  112,  111,
 /*    30 */   142,  119,  159,  106,  160,  160,  160,  160,  144,  154,
 /*    40 */   145,  131,  138,  137,  132,  135,  160,  160,  160,  160,
 /*    50 */   160,  160,  160,  160,  160,  139,  160,  160,  136,  160,
 /*    60 */   160,  160,  143,  113,  127,  120,  108,  109,  123,  117,
 /*    70 */   122,  118,  124,  116,  126,  110,  125,  114,  103,  121,
 /*    80 */   130,  129,  104,  105,  140,  134,  107,  147,  133,  128,
 /*    90 */   115,  151,  152,  102,  141,  150,  158,  155,  156,  157,
 /*   100 */   149,
	];
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 75;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 101;
    const YYNRULE = 59;
    const YYERRORSYMBOL = 53;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = [
	];
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx = -1;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = [];  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = [
  '$',             'OP_OR',         'OP_AND',        'OP_TERNARY_IF',
  'OP_TERNARY_ELSE',  'OP_TERNARY_SHORT',  'OP_EQ',         'OP_NE',       
  'OP_ID',         'OP_NID',        'OP_GT',         'OP_GTEQ',     
  'OP_LT',         'OP_LTEQ',       'OP_IS',         'OP_IS_NOT',   
  'OP_MINUS',      'OP_PLUS',       'OP_CONCAT',     'OP_MULTIPLY', 
  'OP_DIVIDE',     'OP_MOD',        'OP_U_MINUS',    'OP_BANG',     
  'OP_INSTANCEOF',  'PLAIN',         'VAR_START',     'LITERAL',     
  'VAR_END',       'VAR_DIM',       'VAR_OBJECT',    'EXPR_START',  
  'EXPR_END',      'TAG_OPEN_START',  'TAG_END_CLOSE',  'TAG_END',     
  'TAG_CLOSE_START',  'TAG_ATTRIBUTE_START',  'BOOLEAN',       'NULL',        
  'NUMBER',        'STRING',        'PAREN_START',   'PAREN_END',   
  'PLACEHOLDER',   'FILTER',        'ARG_SEP',       'ARRAY_START', 
  'ARRAY_END',     'HASH_START',    'HASH_END',      'HASH_SEP',    
  'DOUBLE_QUOTE',  'error',         'start',         'begin',       
  'var',           'expression_full',  'tag',           'var_extras',  
  'filters',       'var_array_key',  'function_args',  'optional_args',
  'expression',    'tag_attributes',  'double_quoted',  'expression_part',
  'array',         'hash',          'comma_expression_optional',  'hash_parts',  
  'hash_part',     'double_quote_inner',
	];

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = [
 /*   0 */ "start ::= begin",
 /*   1 */ "begin ::= begin PLAIN",
 /*   2 */ "begin ::= begin var",
 /*   3 */ "begin ::= begin expression_full",
 /*   4 */ "begin ::= begin tag",
 /*   5 */ "begin ::=",
 /*   6 */ "var ::= VAR_START LITERAL var_extras filters VAR_END",
 /*   7 */ "var_extras ::= var_extras VAR_DIM var_array_key",
 /*   8 */ "var_extras ::= var_extras VAR_DIM LITERAL function_args",
 /*   9 */ "var_extras ::= var_extras VAR_OBJECT LITERAL optional_args",
 /*  10 */ "var_extras ::=",
 /*  11 */ "var_array_key ::= LITERAL",
 /*  12 */ "var_array_key ::= var",
 /*  13 */ "var_array_key ::= expression_full",
 /*  14 */ "expression_full ::= EXPR_START expression EXPR_END",
 /*  15 */ "tag ::= TAG_OPEN_START LITERAL tag_attributes TAG_END_CLOSE",
 /*  16 */ "tag ::= TAG_OPEN_START LITERAL tag_attributes TAG_END begin TAG_CLOSE_START LITERAL TAG_END",
 /*  17 */ "tag_attributes ::= tag_attributes LITERAL TAG_ATTRIBUTE_START double_quoted",
 /*  18 */ "tag_attributes ::=",
 /*  19 */ "expression_part ::= BOOLEAN",
 /*  20 */ "expression_part ::= NULL",
 /*  21 */ "expression_part ::= NUMBER",
 /*  22 */ "expression_part ::= STRING",
 /*  23 */ "expression_part ::= LITERAL function_args",
 /*  24 */ "expression_part ::= var",
 /*  25 */ "expression_part ::= double_quoted",
 /*  26 */ "expression_part ::= PAREN_START expression PAREN_END",
 /*  27 */ "expression_part ::= array",
 /*  28 */ "expression_part ::= hash",
 /*  29 */ "expression_part ::= OP_MINUS expression_part",
 /*  30 */ "expression ::= expression OP_OR|OP_AND|OP_INSTANCEOF expression",
 /*  31 */ "expression ::= expression OP_EQ|OP_NE|OP_ID|OP_NID|OP_GT|OP_GTEQ|OP_LT|OP_LTEQ expression",
 /*  32 */ "expression ::= expression OP_IS LITERAL optional_args",
 /*  33 */ "expression ::= expression OP_IS_NOT LITERAL optional_args",
 /*  34 */ "expression ::= expression OP_MINUS|OP_PLUS|OP_MULTIPLY|OP_DIVIDE|OP_MOD|OP_CONCAT expression",
 /*  35 */ "expression ::= OP_BANG expression",
 /*  36 */ "expression ::= expression OP_TERNARY_SHORT expression",
 /*  37 */ "expression ::= expression OP_TERNARY_IF expression OP_TERNARY_ELSE expression",
 /*  38 */ "expression ::= expression_part filters",
 /*  39 */ "expression ::= PLACEHOLDER",
 /*  40 */ "filters ::= filters FILTER LITERAL optional_args",
 /*  41 */ "filters ::=",
 /*  42 */ "function_args ::= PAREN_START comma_expression_optional PAREN_END",
 /*  43 */ "comma_expression_optional ::= comma_expression_optional ARG_SEP expression",
 /*  44 */ "comma_expression_optional ::= expression",
 /*  45 */ "comma_expression_optional ::=",
 /*  46 */ "optional_args ::= function_args",
 /*  47 */ "optional_args ::=",
 /*  48 */ "array ::= ARRAY_START comma_expression_optional ARRAY_END",
 /*  49 */ "hash ::= HASH_START hash_parts HASH_END",
 /*  50 */ "hash_parts ::= hash_parts ARG_SEP hash_part",
 /*  51 */ "hash_parts ::= hash_part",
 /*  52 */ "hash_parts ::=",
 /*  53 */ "hash_part ::= expression HASH_SEP|OP_TERNARY_ELSE expression",
 /*  54 */ "double_quoted ::= DOUBLE_QUOTE double_quote_inner DOUBLE_QUOTE",
 /*  55 */ "double_quote_inner ::= double_quote_inner STRING",
 /*  56 */ "double_quote_inner ::= double_quote_inner var",
 /*  57 */ "double_quote_inner ::= double_quote_inner expression_full",
 /*  58 */ "double_quote_inner ::=",
	];

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param Parser_yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Parser_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Parser_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new Parser_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = [
  ['lhs' => 54, 'rhs' => 1],
  ['lhs' => 55, 'rhs' => 2],
  ['lhs' => 55, 'rhs' => 2],
  ['lhs' => 55, 'rhs' => 2],
  ['lhs' => 55, 'rhs' => 2],
  ['lhs' => 55, 'rhs' => 0],
  ['lhs' => 56, 'rhs' => 5],
  ['lhs' => 59, 'rhs' => 3],
  ['lhs' => 59, 'rhs' => 4],
  ['lhs' => 59, 'rhs' => 4],
  ['lhs' => 59, 'rhs' => 0],
  ['lhs' => 61, 'rhs' => 1],
  ['lhs' => 61, 'rhs' => 1],
  ['lhs' => 61, 'rhs' => 1],
  ['lhs' => 57, 'rhs' => 3],
  ['lhs' => 58, 'rhs' => 4],
  ['lhs' => 58, 'rhs' => 8],
  ['lhs' => 65, 'rhs' => 4],
  ['lhs' => 65, 'rhs' => 0],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 2],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 3],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 1],
  ['lhs' => 67, 'rhs' => 2],
  ['lhs' => 64, 'rhs' => 3],
  ['lhs' => 64, 'rhs' => 3],
  ['lhs' => 64, 'rhs' => 4],
  ['lhs' => 64, 'rhs' => 4],
  ['lhs' => 64, 'rhs' => 3],
  ['lhs' => 64, 'rhs' => 2],
  ['lhs' => 64, 'rhs' => 3],
  ['lhs' => 64, 'rhs' => 5],
  ['lhs' => 64, 'rhs' => 2],
  ['lhs' => 64, 'rhs' => 1],
  ['lhs' => 60, 'rhs' => 4],
  ['lhs' => 60, 'rhs' => 0],
  ['lhs' => 62, 'rhs' => 3],
  ['lhs' => 70, 'rhs' => 3],
  ['lhs' => 70, 'rhs' => 1],
  ['lhs' => 70, 'rhs' => 0],
  ['lhs' => 63, 'rhs' => 1],
  ['lhs' => 63, 'rhs' => 0],
  ['lhs' => 68, 'rhs' => 3],
  ['lhs' => 69, 'rhs' => 3],
  ['lhs' => 71, 'rhs' => 3],
  ['lhs' => 71, 'rhs' => 1],
  ['lhs' => 71, 'rhs' => 0],
  ['lhs' => 72, 'rhs' => 3],
  ['lhs' => 66, 'rhs' => 3],
  ['lhs' => 73, 'rhs' => 2],
  ['lhs' => 73, 'rhs' => 2],
  ['lhs' => 73, 'rhs' => 2],
  ['lhs' => 73, 'rhs' => 0],
	];

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = [
        0 => 0,
        1 => 1,
        55 => 1,
        2 => 2,
        3 => 2,
        4 => 2,
        56 => 2,
        57 => 2,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        11 => 11,
        22 => 11,
        12 => 12,
        13 => 12,
        24 => 12,
        27 => 12,
        28 => 12,
        46 => 12,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        19 => 19,
        20 => 20,
        21 => 21,
        23 => 23,
        25 => 25,
        26 => 26,
        29 => 29,
        35 => 29,
        30 => 30,
        31 => 30,
        34 => 30,
        32 => 32,
        33 => 33,
        36 => 36,
        37 => 37,
        38 => 38,
        39 => 39,
        40 => 40,
        42 => 42,
        43 => 43,
        50 => 43,
        44 => 44,
        51 => 44,
        48 => 48,
        49 => 49,
        53 => 53,
        54 => 54,
	];
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 30 "Parser.y"
    function yy_r0(){
	$this->ast = new Ast($this->yystack[$this->yyidx + 0]->minor ?: []);
    }
#line 1128 "Parser.php"
#line 34 "Parser.y"
    function yy_r1(){
	$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor ?: [];
	$this->_retvalue[] = new Syntax\Str($this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1134 "Parser.php"
#line 38 "Parser.y"
    function yy_r2(){
	$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor ?: [];
	$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1140 "Parser.php"
#line 52 "Parser.y"
    function yy_r6(){
	$res = new Syntax\Variable($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -2]->minor ?: [], $this->yystack[$this->yyidx + -1]->minor ?: [], $this->line);
	$this->_retvalue = $res;
    }
#line 1146 "Parser.php"
#line 57 "Parser.y"
    function yy_r7(){
	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor ?: [];
	$this->_retvalue[] = ['array', $this->yystack[$this->yyidx + 0]->minor];
    }
#line 1152 "Parser.php"
#line 61 "Parser.y"
    function yy_r8(){
	$this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ?: [];
	$this->_retvalue[] = ['function', new Syntax\Func($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: [], $this->line)];
    }
#line 1158 "Parser.php"
#line 65 "Parser.y"
    function yy_r9(){
	$this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ?: [];
	if (is_array($this->yystack[$this->yyidx + 0]->minor))
	{
		$this->_retvalue[] = ['function', new Syntax\Func($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: [], $this->line)];
	}
	else
	{
		$this->_retvalue[] = ['object', new Syntax\Str($this->yystack[$this->yyidx + -1]->minor, $this->line)];
	}
    }
#line 1171 "Parser.php"
#line 78 "Parser.y"
    function yy_r11(){
	$this->_retvalue = new Syntax\Str($this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1176 "Parser.php"
#line 81 "Parser.y"
    function yy_r12(){
	$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1181 "Parser.php"
#line 88 "Parser.y"
    function yy_r14(){
	$this->_retvalue = new Syntax\Expression($this->yystack[$this->yyidx + -1]->minor, $this->line);
    }
#line 1186 "Parser.php"
#line 93 "Parser.y"
    function yy_r15(){
	$this->_retvalue = new Syntax\Tag($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor ?: [], [], $this->line, true);
    }
#line 1191 "Parser.php"
#line 96 "Parser.y"
    function yy_r16(){
	if ($this->yystack[$this->yyidx + -6]->minor != $this->yystack[$this->yyidx + -1]->minor)
	{
		$closing = $this->yystack[$this->yyidx + -1]->minor;
		$opening = $this->yystack[$this->yyidx + -6]->minor;
		throw new Exception(\XF::string([
			\XF::phrase('line_x', ['line' => $this->line]), ': ', \XF::phrase('template_tags_not_well_formed', ['closing' => $closing, 'opening' => $opening])
		]));
	}
	$this->_retvalue = new Syntax\Tag($this->yystack[$this->yyidx + -6]->minor, $this->yystack[$this->yyidx + -5]->minor ?: [], $this->yystack[$this->yyidx + -3]->minor ?: [], $this->line, false);
    }
#line 1204 "Parser.php"
#line 108 "Parser.y"
    function yy_r17(){
	if (!$this->yystack[$this->yyidx + 0]->minor->parts)
	{
		$attr = new Syntax\Str('', $this->line);
	}
	else if (count($this->yystack[$this->yyidx + 0]->minor->parts) == 1)
	{
		$attr = reset($this->yystack[$this->yyidx + 0]->minor->parts);
	}
	else
	{
		$attr = $this->yystack[$this->yyidx + 0]->minor;
	}
	$this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ?: [];
	$this->_retvalue[$this->yystack[$this->yyidx + -2]->minor] = $attr;
    }
#line 1222 "Parser.php"
#line 126 "Parser.y"
    function yy_r19(){
	$this->_retvalue = new Syntax\Boolean(strtolower($this->yystack[$this->yyidx + 0]->minor) == 'true', $this->line);
    }
#line 1227 "Parser.php"
#line 129 "Parser.y"
    function yy_r20(){
	$this->_retvalue = new Syntax\NullValue($this->line);
    }
#line 1232 "Parser.php"
#line 132 "Parser.y"
    function yy_r21(){
	$this->_retvalue = new Syntax\Number($this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1237 "Parser.php"
#line 138 "Parser.y"
    function yy_r23(){
	$this->_retvalue = new Syntax\Func($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: [], $this->line);
    }
#line 1242 "Parser.php"
#line 144 "Parser.y"
    function yy_r25(){
	if (!$this->yystack[$this->yyidx + 0]->minor->parts)
	{
		$this->_retvalue = new Syntax\Str('', $this->line);
	}
	else if (count($this->yystack[$this->yyidx + 0]->minor->parts) == 1)
	{
		$this->_retvalue = reset($this->yystack[$this->yyidx + 0]->minor->parts);
	}
	else
	{
		$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
	}
    }
#line 1258 "Parser.php"
#line 158 "Parser.y"
    function yy_r26(){
	$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1263 "Parser.php"
#line 167 "Parser.y"
    function yy_r29(){
	$this->_retvalue = new Syntax\UnaryOperator($this->yystack[$this->yyidx + -1]->major, $this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1268 "Parser.php"
#line 171 "Parser.y"
    function yy_r30(){
	$this->_retvalue = new Syntax\BinaryOperator($this->yystack[$this->yyidx + -1]->major, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1273 "Parser.php"
#line 177 "Parser.y"
    function yy_r32(){
	$this->_retvalue = new Syntax\Is($this->yystack[$this->yyidx + -3]->minor, true, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: [], $this->line);
    }
#line 1278 "Parser.php"
#line 180 "Parser.y"
    function yy_r33(){
	$this->_retvalue = new Syntax\Is($this->yystack[$this->yyidx + -3]->minor, false, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: [], $this->line);
    }
#line 1283 "Parser.php"
#line 189 "Parser.y"
    function yy_r36(){
	$this->_retvalue = new Syntax\TernaryShortOperator($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1288 "Parser.php"
#line 192 "Parser.y"
    function yy_r37(){
	$this->_retvalue = new Syntax\TernaryOperator($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor, $this->line);
    }
#line 1293 "Parser.php"
#line 195 "Parser.y"
    function yy_r38(){
	if ($this->yystack[$this->yyidx + 0]->minor)
	{
		$this->_retvalue = new Syntax\FilterChain($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor, $this->line);
	}
	else
	{
		$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
	}
    }
#line 1305 "Parser.php"
#line 205 "Parser.y"
    function yy_r39(){
	if (!isset($this->placeholders[$this->yystack[$this->yyidx + 0]->minor]))
	{
		throw new \Exception("Unknown placeholder used, this should never happen");
	}

	$this->_retvalue = $this->placeholders[$this->yystack[$this->yyidx + 0]->minor];
    }
#line 1315 "Parser.php"
#line 214 "Parser.y"
    function yy_r40(){
	$this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ?: [];
	$this->_retvalue[] = [$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor ?: []];
    }
#line 1321 "Parser.php"
#line 220 "Parser.y"
    function yy_r42(){
	$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor ?: [];
    }
#line 1326 "Parser.php"
#line 224 "Parser.y"
    function yy_r43(){
	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor ?: [];
	$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1332 "Parser.php"
#line 228 "Parser.y"
    function yy_r44(){
	$this->_retvalue = [$this->yystack[$this->yyidx + 0]->minor];
    }
#line 1337 "Parser.php"
#line 238 "Parser.y"
    function yy_r48(){
	$this->_retvalue = new Syntax\ArrayExpression($this->yystack[$this->yyidx + -1]->minor ?: [], $this->line);
    }
#line 1342 "Parser.php"
#line 242 "Parser.y"
    function yy_r49(){
	$this->_retvalue = new Syntax\Hash($this->yystack[$this->yyidx + -1]->minor ?: [], $this->line);
    }
#line 1347 "Parser.php"
#line 255 "Parser.y"
    function yy_r53(){
	$this->_retvalue = [0 => $this->yystack[$this->yyidx + -2]->minor, 1 => $this->yystack[$this->yyidx + 0]->minor];
    }
#line 1352 "Parser.php"
#line 259 "Parser.y"
    function yy_r54(){
	$this->_retvalue = new Syntax\Quoted($this->yystack[$this->yyidx + -1]->minor ?: [], $this->line);
    }
#line 1357 "Parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //Parser_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new Parser_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 4 "Parser.y"

	throw new Exception(\XF::string([
		\XF::phrase('line_x', ['line' => $this->line]), ': ', \XF::phrase('syntax_error')
	]));
#line 1475 "Parser.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     * @param mixed ...           any extra arguments that should be passed to handlers
     *
     * @return void
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new Parser_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = [];
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(
                self::$yyTraceFILE,
                "%sInput %s\n",
                self::$yyTracePrompt,
                self::$yyTokenName[$yymajor]
            );
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL
                && !$this->yy_is_expected_token($yymajor)
            ) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(
                        self::$yyTraceFILE,
                        "%sSyntax Error!\n",
                        self::$yyTracePrompt
                    );
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ) {
                        if (self::$yyTraceFILE) {
                            fprintf(
                                self::$yyTraceFILE,
                                "%sDiscard input token %s\n",
                                self::$yyTracePrompt,
                                self::$yyTokenName[$yymajor]
                            );
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0
                            && $yymx != self::YYERRORSYMBOL
                            && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
