<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Compiler\TreeNode
 */
-> import('Compiler.TreeNode')

/**
 * \Hoa\Compiler\Exception
 */
-> import('Compiler.Exception.~')

/**
 * \Hoa\Compiler\Exception\IllegalToken
 */
-> import('Compiler.Exception.IllegalToken')

/**
 * \Hoa\Compiler\Exception\UnrecognizedToken
 */
-> import('Compiler.Exception.UnrecognizedToken')

/**
 * \Hoa\Compiler\Exception\Rule
 */
-> import('Compiler.Exception.Rule');

}

namespace Hoa\Compiler {

/**
 * Class \Hoa\Compiler\Llk.
 *
 * Provide a generic LL(k) parser.
 *
 * @author     Frédéric Dadeau <frederic.dadeau@lifc.univ-fcomte.fr>
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Frédéric Dadeau, Ivan Enderlin.
 * @license    New BSD License
 */

class Llk {

    /**
     * List of tokens: name => regex.
     * Attention, it must be defined in precedence order.
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_tokens           = null;

    /**
     * Rules: name => rule expression.
     * Rules adopt the following grammar.
     *             rule  ::=  choice <EOF>
     *           choice  ::=  concat ( "|" choice ) *
     *           concat  ::=  repetition ( concat ) *
     *       repetition  ::=  simple ( repeatOP ) ?  ( NODE_ID ) ?
     *         repeatOP  ::=  "{" INTEGER "," INTEGER "}"
     *                     |  "{" "," INTEGER "}"
     *                     |  "{" INTEGER "," "}"
     *                     |  "?"                    equiv. "{0,1}"
     *                     |  "+"                    equiv. "{1,}"
     *                     |  "*"                    equiv. "{0,}"
     *           simple  ::=  "(" rule ")"
     *                     |  SKIPPED_TOKEN          will not appear in the tree
     *                     |  KEPT_TOKEN             will be kept in the tree
     *                     |  RULE_NAME
     *    SKIPPED_TOKEN  ::=  "::[a-zA-Z_][a-zA-Z0-9_$]*(\[[0-9]+\])?::"
     *       KEPT_TOKEN  ::=  "<[a-zA-Z_][a-zA-Z0-9_$]*(\[[0-9]+\])?>"
     *        RULE_NAME  ::=  "[a-zA-Z_][a-zA-Z0-9_$]*()"
     *          INTEGER  ::=  "[0-9]+"
     *          NODE_ID  ::=  "#[a-zA-Z][a-zA-Z0-9]+"
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_rules            = null;

    /**
     * Whether we will print debug outputs.
     *
     * @var \Hoa\Compiler\Llk bool
     */
    public $debug                = false;

    /**
     * Current state of the analyzer.
     * Do not use this from a public context :-).
     *
     * @var \Hoa\Compiler\Llk int
     */
    public $_currentState        = 0;

    /**
     * Error state.
     *
     * @var \Hoa\Compiler\Llk int
     */
    protected $_errorState       = 0;

    /**
     * Lexer state.
     *
     * @var \Hoa\Compiler\Llk string
     */
    protected $_lexerState       = null;

    /**
     * Current sequence beeing analyzed.
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_tokenSequence    = null;

    /**
     * Management of token values using an associative:
     *     array(int(1) => array(token, array(int(2), value)))
     * where
     *     • int(1) represents unique rule ID (incremented each time a rule is
     *              applied);
     *     • token  is the token used;
     *     • int(2) represents the token # for the rule;
     *     • value  is the value of the token.
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_rulesToken       = array();

    /**
     * See previous attribute.
     *
     * @var \Hoa\Compiler\Llk int
     */
    protected $_rulesID          = 0;

    /**
     * Set of dynamically created functions.
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_createdFunctions = array();

    /**
     * Code of dynamically created function (for debug use only).
     *
     * @var \Hoa\Compiler\Llk array
     */
    protected $_functionsCode    = array();



    /**
     * Construct the parser.
     *
     * @access  public
     * @param   array  $tokens    Tokens.
     * @param   array  $rules     Rules.
     * @param   bool   $debug     Debug mode.
     * @return  void
     */
    public function __construct( Array $tokens, Array $rules, $debug = false ) {

        foreach($tokens as $context => &$token)
            if(!isset($token['skip']))
                $token['skip'] = null;

        $this->_tokens = $tokens;
        $this->_rules  = $rules;
        $this->debug   = $debug;
        $this->analyzeRules($rules);

        return;
    }

    /**
     * Load parser from a file that contains the grammar.
     * Example:
     *     %skip  space     \s
     *
     *     %token word      [a-zA-Z]+
     *     %token number    [0-9]+(\.[0-9]+)?
     *     %token open_par  \(
     *     %token close_par \)
     *     %token equal     =
     *     %token plus      \+
     *     %token minus     \-
     *     %token divide    \/
     *     %token times     \*
     *
     *     #equation:
     *         formula() ::equal:: <number>
     *
     *     formula:
     *         factor()
     *         (
     *             ::plus::  formula() #addition
     *           | ::minus:: formula() #substraction
     *         )?
     *
     *     factor:
     *         operand()
     *         (
     *             ::times::  factor() #product
     *           | ::divide:: factor() #division
     *         )?
     *
     *     operand:
     *           <word>
     *         | ::minus::? <number> #number
     *         | ::open_par:: formula() ::close_par::
     *
     * Use tabs or spaces, it does not matter.
     * Instructions follow the form: %<instruction>. Only %skip and %token are
     * supported.
     * Rules follow the form: <rule name>:<new line>[<space><rule><new line>]*.
     * Contexts are useful to set specific skips and tokens. We give a full
     * example with context + unification (for fun) to parse <a>b</a>:
     *     %skip   space         \s
     *     %token  lt             <        ->  in_tag
     *     %token  inner          [^<]*
     *
     *     %skip   in_tag:space   \s
     *     %token  in_tag:slash   /
     *     %token  in_tag:tagname [^>]+
     *     %token  in_tag:gt      >        ->  default
     *
     *     #foo:
     *         ::lt:: <tagname[0]> ::gt::
     *         <inner>
     *         ::lt:: ::slash:: ::tagname[0]:: ::gt::
     *
     * @access  public
     * @param   \Hoa\Stream\IStream\In  $stream    Stream that contains the
     *                                             grammar.
     * @return  \Hoa\Compiler\Llk
     * @throw   \Hoa\Compiler\Exception
     */
    public static function load ( \Hoa\Stream\IStream\In $stream,
                                  $debug = false ) {

        $pp     = $stream->readAll();
        $lines  = explode("\n", $pp);
        $tokens = array('default' => array());
        $rules  = array();

        for($i = 0, $m = count($lines); $i < $m; ++$i) {

            $line = $lines[$i];

            if(0 === strlen($line) || '//' == substr($line, 0, 2))
                continue;

            if('%' == $line[0]) {

                if(0 !== preg_match(
                    '#^%skip\s+(?:([^:]+):)?([^\s]+)\s+(.*)$#',
                    $line,
                    $matches)) {

                    if(empty($matches[1]))
                        $matches[1] = 'default';

                    if(!isset($tokens[$matches[1]]))
                        $tokens[$matches[1]] = array();

                    if(!isset($tokens[$matches[1]]['skip']))
                        $tokens[$matches[1]]['skip'] = $matches[3];
                    else
                        $tokens[$matches[1]]['skip'] =
                            '(?:' . $matches[3] . ')|' .
                            $tokens[$matches[1]]['skip'];
                }

                elseif(0 !== preg_match(
                    '#^%token\s+(?:([^:]+):)?([^\s]+)\s+(.*?)(?:\s+->\s+(.*))?$#',
                    $line,
                    $matches)) {

                    if(empty($matches[1]))
                        $matches[1] = 'default';

                    if(isset($matches[4]) && !empty($matches[4]))
                        $matches[2] = $matches[2] . ':' . $matches[4];

                    if(!isset($tokens[$matches[1]]))
                        $tokens[$matches[1]] = array();

                    $tokens[$matches[1]][$matches[2]] = $matches[3];
                }

                else
                    throw new Exception(
                        'Unrecognized instructions:' . "\n" .
                        '    %s' . "\n" . 'in file %s at line %d.',
                        0, array($line, $stream->getStreamName(), $i + 1));

                continue;
            }

            $ruleName = substr($line, 0, -1);
            $rule     = null;
            ++$i;

            while(   $i < $m
                  && isset($lines[$i][0])
                  && (' '  == $lines[$i][0]
                  ||  "\t" == $lines[$i][0]
                  ||  '//' == substr($lines[$i], 0, 2))) {

                if('//' == substr($lines[$i], 0, 2)) {

                    ++$i;

                    continue;
                }

                $rule .= ' ' . trim($lines[$i++]);
            }

            if(isset($lines[$i][0]))
                --$i;

            $rules[$ruleName] = $rule;
        }

        return new self($tokens, $rules, $debug);
    }

    /**
     * Text processor: analyses the text in parameter and possibly builds a tree.
     *
     * @access  public
     * @param   string  $text    Text to analyse.
     * @param   string  $rule    Master rule (default: first of the rules).
     * @param   bool    $tree    Whether a tree should be built when text is
     *                           validated.
     * @return  mixed
     */
    public function parse ( $text, $rule = null, $tree = true ) {

        // Lexing.
        $this->_tokenSequence = $this->lexMe($text, $this->_tokens);
        $this->_currentState  = 0;

        // Reset of the token analyses map
        $this->resetRules();

        // If no rule is defined or if an incorrect rules is specified, select
        // first rule of the rules array.
        if(false === array_key_exists($rule, $this->_rules)) {

            reset($this->_rules);
            $rule = key($this->_rules);
        }

        if('#' == $rule[0])
            $rule = substr($rule, 1);

        // Invocation of the corresponding function.
        $f = $this->getFunctionForRule($rule);
        $r = $f($this, '', $tree);

        if(null === $r || 'EOF' !== $this->getCurrentToken()) {

            $offset = $this->_tokenSequence[$this->_errorState]['offset'];

            throw new Exception\IllegalToken(
                'Illegal token "%s" at line 1 and column %d:' .
                "\n" . '%s' . "\n" . str_repeat(' ', $offset) . '↑',
                0, array(
                    $this->_tokenSequence[$this->_errorState]['value'],
                    $offset + 1,
                    $text
                ), 1, $offset);
        }

        return $r;
    }

    /**
     * Text tokenizer: splits the text in parameter in an ordered array of
     * tokens.
     *
     * @access  protected
     * @param   string  $text      Text to tokenize.
     * @param   array   $tokens    Tokens to be returned.
     * @return  array
     */
     protected function lexMe ( $text, Array $tokens ) {

        $_text             = $text;
        $offset            = 0;
        $tokenized         = array();
        $this->_lexerState = 'default';

        while(0 < strlen($text)) {

            $nextToken = $this->nextToken($text, $tokens);

            if(null === $nextToken)
                throw new Exception\UnrecognizedToken(
                    'Unrecognized token "%s" at line 1 and column %d:' .
                    "\n" . '%s' . "\n" . str_repeat(' ', $offset) . '↑',
                    2, array($this->getCurrentToken('value'), $offset + 1, $_text),
                    1, $offset
                );

            if($nextToken['keep']) {

                $nextToken['offset'] = $offset;
                $tokenized[]         = $nextToken;
            }

            $offset += $nextToken['length'];
            $text    = substr($text, $nextToken['length']);
        }

        $tokenized[] = array(
            'token'  => 'EOF',
            'value'  => 'EOF',
            'length' => 0,
            'offset' => $offset,
            'keep'   => true
        );

        return $tokenized;
    }

    /**
     * Compute the next token recognized at the beginning of the string.
     *
     * @access  protected
     * @param   string  $text      Text to tokenize.
     * @param   array   $tokens    Tokens to be returned.
     * @return  array
     * @throw   UnrecognizedToken
     */
    protected function nextToken ( $text, Array $tokens ) {

        $tokenArray = $tokens[$this->_lexerState];

        foreach($tokenArray as $fullLexeme => $regexp) {

            if(false !== strpos($fullLexeme,':')) {

                $tab       = explode(':', $fullLexeme);
                $lexeme    = $tab[0];
                $nextState = $tab[1];
            }
            else {

                $lexeme    = $fullLexeme;
                $nextState = $this->_lexerState;
            }

            if(   $lexeme !== 'skip'
               && null    !== $out = $this->matchesLexem($text, $lexeme, $regexp)) {

                $out['keep']       = true;
                $this->_lexerState = $nextState;

                return $out;
            }
        }

        if(empty($tokenArray['skip']))
            return;

        $out = $this->matchesLexem($text, $lexeme, $tokenArray['skip']);

        if(null !== $out) {

            $out['keep'] = false;

            return $out;
        }

        return null;
    }

    /**
     * Check if a given lexem is matched at the beginning of the text.
     *
     * @access  protected
     * @param   string  $text      Text in which the lexem has to be found.
     * @param   string  $lexem     Name of the lexem.
     * @param   string  $regexp    Regular expression describing the lexem.
     * @return  array
     */
    protected function matchesLexem ( $text, $lexem, $regexp ) {

        $regexp = str_replace('#', '\#', $regexp);

        if(   0 !== preg_match('#' . $regexp . '#', $text, $matches)
           && 0 <   count($matches)
           && 0 === strpos($text, $matches[0]))
            return array(
                'token'  => $lexem,
                'value'  => $matches[0],
                'length' => strlen($matches[0])
            );

        return null;
    }

    /**
     * Build the analyzer of the rules (does not analyze the rules).
     *
     * @access  protected
     * @param   array  $rules    Rule to be analyzed.
     * @return  void
     */
    private function analyzeRules ( Array $rules ) {

        // Error case: no rule specified.
        if(empty($rules))
            throw new Exception\Rule('No rules specified!', 2);

        // Definition of grammar tokens.
        $tokens = array('default' => array(
            'skip'          => '\s',
            'plus'          => '\+',
            'star'          => '\*',
            'question'      => '\?',
            'open_bracket'  => '{',
            'close_bracket' => '}',
            'comma'         => ',',
            'open_par'      => '\(',
            'close_par'     => '\)',
            'choice_op'     => '\|',
            'skipped_token' => '::[a-zA-Z_][a-zA-Z0-9_$]*(\[[0-9]+\])?::',
            'kept_token'    => '<[a-zA-Z_][a-zA-Z0-9_$]*(\[[0-9]+\])?\>',
            'rule'          => '[a-zA-Z_][a-zA-Z0-9_$]*\(\)',
            'number'        => '[0-9]+',
            'node'          => '#[a-zA-Z][a-zA-Z0-9]+'
        ));

        // Re-initialization of on-the-fly declared functions.
        $this->_createdFunctions = array();
        $this->_functionsCode    = array();
        $debug                   = $this->debug;
        $this->debug             = false;

        // Treatment of the rules.
        foreach($rules as $key => $value) {

            // Lexing.
            $this->_tokenSequence = $this->lexMe($value, $tokens);
            $this->_currentState  = 0;

            // If key starts with #, builds a node.
            if('#' === $key[0]) {

                $nodeName  = $key;
                $buildNode = true;
                $key       = substr($key, 1);
            }
            else {

                $nodeName  = '#';
                $buildNode = false;
            }

            // Parsing of the rule.
            $r = $this->rule();

            // Error if parsing failed.
            if(null === $r)
                throw new Exception\Rule(
                    'Error while parsing rule %s.', 3, $key);

            // If parsing succeeded, creation of the function calling the main
            // rule application.
            $args  = '$p, $ind=\'\', $tree = false';
            $fname = substr($r, 1);
            $code  = '$sav = $p->_currentState;' . "\n" .
                     '$p->_incrementRule(); ' . "\n\n" .
                     'if(true === $p->debug)' . "\n" .
                     '    echo $ind, \'enter: ' . $key . '\', "\n";' . "\n\n" .
                     '$node = true == $tree' . "\n" .
                     '            ? new \Hoa\Compiler\TreeNode(\'' . $nodeName . '\')' . "\n" .
                     '            : null;' . "\n" .
                     '$f    = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                     '$r    = $f($p, $ind, $node);' . "\n\n" .
                     'if(null === $r) {' . "\n\n" .
                     '    if(true === $p->debug)' . "\n" .
                     '        echo $ind, \'failed: ' . $key . '\', "\n";' . "\n\n" .
                     '    $p->_currentState = $sav;' . "\n\n" .
                     '    $p->_decrementRule(); ' . "\n" .
                     '    return null;' . "\n" .
                     '}' . "\n\n" .
                     '$p->_decrementRule(); ' . "\n" .
                     'if(true === $p->debug)' . "\n" .
                     '    echo $ind, \'exit: ' . $key . '\', "\n";' . "\n\n" .
                     'if(true == $tree && \'#\' == $node->getId())' . "\n" .
                     '    return (0 < $node->getChildrenNumber())' . "\n" .
                     '               ? $node->getChild(0)' . "\n" .
                     '               : true;' . "\n\n" .
                     'return true == $tree ? $node : true;';
            $funct = create_function($args, $code);

            $this->registerFunction($funct, $code);
            $this->_rules[$key] = $funct;
        }

        if(true === $this->debug) {

            foreach($this->_functionsCode as $f => $c)
                echo $f, '() { ', $c, '}', "\n\n";

            foreach($this->_rules as $name => $f)
                echo $name, " => ", $f, "\n";
        }

        $this->debug = $debug;

        return;
    }

    /**
     * Implementation of:
     *     rule  ::=  concat
     *
     * @access  protected
     * @param   string  $ind    Indentation for debug.
     * @return  function
     */
    protected function rule ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind, 'enter: rule', "\n";

        $r = $this->choice(' > ' . $ind);

        if(null === $r) {

            if(true === $this->debug)
                echo $ind, 'failed: rule', "\n";

            return null;
        }

        if(true === $this->debug)
            echo $ind, 'exit: rule', "\n";

        return $r;
    }

    /**
     * Implementation of:
     *     choice  ::=  repetition ( "|" repetition ) *
     *
     * @access  protected
     * @param   string  $ind    Indentation for debug.
     * @return  function
     */
    protected function choice ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind, 'enter: choice', "\n";

        $r = $this->concat(' > ' . $ind);

        if(null === $r) {

            if(true === $this->debug)
                echo $ind, 'failed: choice', "\n";

            return null;
        }

        // Building of the function to perform the choice.
        $fname  = substr($r, 1);
        $args   = '$p, $ind, $node = null';
        $code   = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                  '$r = $f($p, $ind, $node);' . "\n\n" .
                  'if(null !== $r)' . "\n" .
                  '    return true;' . "\n\n";
        $others = false;

        // part ... ( "|" concat ) *
        while('choice_op' == $this->getCurrentToken()) {

            $this->consumeToken($ind);
            $others = true;
            $r      = $this->concat(' > ' . $ind);

            if(null === $r) {

                if(true === $this->debug)
                    echo $ind, 'failed: choice', "\n";

                return null;
            }

            $fname  = substr($r, 1);
            $code  .= '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                      '$r = $f($p, $ind, $node);' . "\n\n" .
                      'if(null !== $r)' . "\n" .
                      '    return true;' . "\n\n";
        }

        if(false === $others) {

            if(true === $this->debug)
                echo $ind, 'exit: choice', "\n";

            return $r;
        }

        // End of generated function code.
        $code  .= 'return null;';
        $funct  = create_function($args, $code);
        $this->registerFunction($funct, $code);

        if(true === $this->debug)
            echo $ind, 'exit: choice', "\n";

        return $funct;
    }

    /**
     * Implementation of:
     *     concat  ::=  repetition ( concat ) *
     *
     * @access  protected
     * @param   string  $ind    Indentation for debug.
     * @return  function
     */
    protected function concat ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind, 'enter: concat', "\n";

        $r = $this->repetition(' > ' . $ind);

        if(null === $r) {

            if(true === $this->debug)
                echo $ind, 'failed: concat', "\n";

            return null;
        }

        // Building of the function to perform the choice.
        $fname  = substr($r, 1);
        $args   = '$p, $ind=\'\', $node = null';
        $code   = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                  '$r = $f($p, $ind, $node);' . "\n\n" .
                  'if(null === $r)' . "\n" .
                  '    return null;' . "\n\n";
        $others = false;

        // part ... ( repetition ) *
        while(null !== $r1 = $this->repetition(' > ' . $ind)) {

            $fname   = substr($r1, 1);
            $code   .= '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                       '$r = $f($p, $ind, $node);' . "\n\n" .
                       'if(null === $r)' . "\n" .
                       '    return null;' . "\n\n";
            $others  = true;
        }

        if(false === $others) {

            if(true === $this->debug)
                echo $ind, 'exit: concat', "\n";

            return $r;
        }

        // End of generated function code.
        $code  .= 'return true;';
        $funct  = create_function($args, $code);
        $this->registerFunction($funct, $code);

        if(true === $this->debug)
            echo $ind, 'exit: concat', "\n";

        return $funct;
    }

    /**
     * Implementation of:
     *     repetition  ::=  simple ( repeatOP ) ?
     *
     * @access  protected
     * @param   string  $ind    Indentation for debug.
     * @return  function
     */
    protected function repetition ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind, 'enter: repetition', "\n";

        $r = $this->simple(' > ' . $ind);

        if(null === $r) {

            if(true === $this->debug)
                echo $ind, 'failed: repetition', "\n";

            return null;
        }

        $fname = substr($r, 1);
        $code  = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                 '$r = $f($p, $ind, $node);' . "\n" .
                 'if(null === $r)' . "\n" .
                 '    return null;' . "\n\n";

        switch($this->getCurrentToken()) {

            // repeatOP ::= "?"
            case 'question':
                $rep  = $this->consumeToken($ind);
                $code = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                        '$r = $f($p, $ind, $node);';
              break;

            // repeatOP ::= "+"
            case 'plus':
                $rep  = $this->consumeToken($ind);
                $code = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                        '$r = $f($p, $ind, $node);' . "\n" .
                        'if(null === $r)' . "\n" .
                        '    return null;' . "\n\n" .
                        'while(null !== $r = $f($p, $ind, $node));' . "\n\n";
              break;

            // repeatOP ::= "*"
            case 'star':
                $rep  = $this->consumeToken($ind);
                $code = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n" .
                        'while(null !== $r = $f($p, $ind, $node));' . "\n\n";
              break;

            // repeatOP = "{" NUMBER ? "," NUMBER ? "}"
            // (at least one NUMBER should be present)
            case 'open_bracket':
                $rep = $this->consumeToken($ind);

                // Optional first number.
                if('number' == $this->getCurrentToken()) {

                    $min = $this->getCurrentToken('value');
                    $this->consumeToken($ind);
                }
                else
                    $min = 0;

                // Comma.
                if('comma' != $this->getCurrentToken())
                    return null;

                $this->consumeToken($ind);

                // Optional second number.
                if('number' == $this->getCurrentToken()) {

                    $max = $this->getCurrentToken('value');
                    $this->consumeToken($ind);

                    if($max < $min)
                        throw new Exception\Rule(
                            'Upper bound of iteration must be greater of equal ' .
                            'to lower bound', 4);
                }
                else
                    $max = -1;

                // Last bracket.
                if('close_bracket' != $this->getCurrentToken())
                    return null;

                $this->consumeToken($ind);
                $code = '$f = $p->getRegisteredFunction(\'' . $fname . '\');' . "\n";

                for($i = 0; $i < $min; ++$i)
                    $code .= '$r = $f($p, $ind, $node);' . "\n" .
                             'if(null === $r)' . "\n" .
                             '    return null;' . "\n\n";

                $code .= '$i = ' . $min . ';' . "\n" .
                         'while(' . (-1 != $max ? '$i < ' . $max . ' && ' : '') .
                         'null !== $r = $f($p, $ind, $node))' . "\n" .
                         '    ++$i;' . "\n\n";

                if(-1 != $max)
                    $code .= 'if($i > ' . $max . ')' . "\n" .
                             '    return null;' . "\n\n";

              break;
        }

        // (NODE_ID) ?
        if('node' == $this->getCurrentToken()) {

            $nodeId  = $this->getCurrentToken('value');
            $code   .= 'if(null !== $node)' . "\n" .
                       '    $node->setId(\'' . $nodeId . '\');' . "\n\n";
            $rep     = $this->consumeToken($ind);
        }

        if(true === $this->debug)
            echo $ind, 'exit: repetition', "\n";

        if(!isset($rep))
            return $r;

        // Termination of the code.
        $args   = '$p, $ind = \'\', $node = null';
        $code  .= 'return true;';
        $funct  = create_function($args, $code);
        $this->registerFunction($funct, $code);

        return $funct;
    }

    /**
     * Implementation of:
     *     simple  ::=  "(" rule ")"
     *               |  TOKEN
     *               |  RULE_NAME
     *
     * @access  protected
     * @param   string  $ind    Indentation for debug.
     * @return  function
     */
    protected function simple ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind, 'enter: simple', "\n";

        // case "(" rule ")"
        if('open_par' == $this->getCurrentToken()) {

            $this->consumeToken($ind);
            $r = $this->rule(' > ' . $ind);

            if(null === $r) {

                if(true === $this->debug)
                    echo $ind, 'failed: simple', "\n";

                return null;
            }

            if('close_par' != $this->getCurrentToken()) {

                if(true === $this->debug)
                    echo $ind, 'failed: simple', "\n";

                return null;
            }

            $this->consumeToken($ind);

            if(true === $this->debug)
                echo $ind, 'exit: simple', "\n";

            return $r;
        }

        // case SKIPPED_TOKEN
        if('skipped_token' == $this->getCurrentToken()) {

            $tokValue = substr($this->getCurrentToken('value'), 2, -2);

            if(']' == substr($tokValue, -1)) {

                $id       = substr(
                    $tokValue,
                    strpos($tokValue, '[') + 1,
                    strlen($tokValue) - strpos($tokValue, ']')
                );
                $tokValue = substr($tokValue, 0, strpos($tokValue, '['));
            }
            else
                $id = -1;

            if(false === $this->checkTokenExistence($tokValue, $this->_tokens))
                throw new Exception\Rule(
                    'Specified token %s not declared in tokens.',
                    5, $tokValue);

            // Building of the function to check the token.
            $args = '$p, $ind = \'\', $node = null';
            $code = 'if(\'' . $tokValue . '\' != $p->getCurrentToken()) {' . "\n\n" .
                    '    if(true === $p->debug)' . "\n" .
                    '        echo \' > \', $ind, \'Unexpected \', ' .
                    '$p->getCurrentToken(), \', expected ' . $tokValue . '\', "\n";' . "\n\n" .
                    '    return null;' . "\n" .
                    '}' . "\n\n";

            if(0 <= $id)
                $code .= 'if(true !== $p->_verifyCurrentToken(' . $id . ')) {' . "\n\n" .
                         '    if(true === $p->debug)' . "\n" .
                         '        echo \' > \', $ind, \'Unexpected value \', ' .
                         '$p->getCurrentToken(\'value\'), \', expected value \', ' .
                         '$p->getExpectedCurrentTokenValue(' . $id . '), ' . '"\n";' . "\n\n" .
                         '    return null; ' . "\n" .
                         '}' . "\n\n";

            $code .= '$p->consumeToken(\' > \' . $ind);' . "\n\n" .
                     'return true;';

            $funct = create_function($args, $code);
            $this->registerFunction($funct, $code);

            // Finishing normal execution.
            $this->consumeToken($ind);

            if(true === $this->debug)
                echo $ind, 'exit: simple', "\n";

            return $funct;
        }

        // case KEPT_TOKEN
        if('kept_token' == $this->getCurrentToken()) {

            $tokValue = substr($this->getCurrentToken('value'), 1, -1);

            if(']' == substr($tokValue, -1)) {

                $id       = substr(
                    $tokValue,
                    strpos($tokValue, '[') + 1,
                    strlen($tokValue) - strpos($tokValue, ']')
                );
                $tokValue = substr($tokValue, 0, strpos($tokValue, '['));
            }
            else
                $id = -1;

            if(false === $this->checkTokenExistence($tokValue, $this->_tokens))
                throw new Exception\Rule(
                    'Specified token %s not declared in tokens.',
                    6, $tokValue);

            // Building of the function to check the token.
            $args  = '$p, $ind = \'\', $node = null';
            $code  = 'if(\'' . $tokValue . '\' != $p->getCurrentToken()) {' . "\n\n" .
                     '    if(true === $p->debug)' . "\n" .
                     '        echo \' > \', $ind, \'Unexpected \', ' .
                     '$p->getCurrentToken(), \', expected ' . $tokValue . '\', "\n";' . "\n\n" .
                     '   return null;' . "\n" .
                     '}' . "\n\n";

            if(0 <= $id)
                $code .= 'if(true !== $p->_verifyCurrentToken(' . $id . ')) {' . "\n\n" .
                         '    if(true === $p->debug)' . "\n" .
                         '        echo \' > \', $ind, \'Unexpected value \', ' .
                         '$p->getCurrentToken(\'value\'), \', expected value \', ' .
                         '$p->getExpectedCurrentTokenValue(' . $id . '), "\n";' . "\n\n" .
                         '    return null;' . "\n" .
                         '}' . "\n\n";

            $code .= 'if(null !== $node) {' . "\n\n" .
                     '    $t = array(' . "\n" .
                     '        \'token\' => $p->getCurrentToken(\'token\'),' . "\n" .
                     '        \'value\' => $p->getCurrentToken(\'value\')' . "\n" .
                     '    );' . "\n" .
                     '    $node->addChild(new \Hoa\Compiler\TreeNode(\'token\', $t));' . "\n" .
                     '}' . "\n\n" .
                     '$p->consumeToken(\' > \' . $ind);' . "\n\n" .
                     'return true;';
            $funct = create_function($args, $code);
            $this->registerFunction($funct, $code);

            // Finishing normal execution.
            $this->consumeToken($ind);

            if(true === $this->debug)
                echo $ind, 'exit: simple', "\n";

            return $funct;
        }

        // case RULE
        if('rule' == $this->getCurrentToken()) {

            $ruleValue = substr($this->getCurrentToken('value'), 0, -2);

            if(   false === array_key_exists(      $ruleValue, $this->_rules)
               && false === array_key_exists('#' . $ruleValue, $this->_rules))
                throw new Exception\Rule(
                    'Specified rule %s not declared in rules.',
                    7, $ruleValue);

            // Building of the function to call to check the rule.
            $args = '$p,$ind=\'\',$node=NULL';
            $code = '$f = $p->getFunctionForRule(\'' . $ruleValue . '\');' . "\n" .
                    '$r = $f($p, \' > \' . $ind, null !== $node);' . "\n\n" .
                    'if(null === $r)' . "\n" .
                    '    return null;' . "\n\n" .
                    'if(null !== $node && true !== $node)' . "\n" .
                    '    $node->addChild($r);' . "\n\n" .
                    'return true;';
            $funct = create_function($args, $code);
            $this->registerFunction($funct, $code);

            // Finishing normal execution.
            $this->consumeToken($ind);

            if(true === $this->debug)
                echo $ind, 'exit: simple', "\n";

            return $funct;
        }

        if(true === $this->debug)
            echo $ind, 'failed: simple', "\n";

        return null;
    }

    /**
     * Registration of on-the-fly created functions.
     *
     * @access  protected
     * @param   function  $function    Function.
     * @param   string    $code        Code (debug only).
     * @return  void
     */
    protected function registerFunction ( $function, $code = '' ) {

        $name                           = substr($function, 1);
        $this->_createdFunctions[$name] = $function;
        $this->_functionsCode[$name]    = $code;

        return;
    }

    /**
     * Consultation of on-the-fly created functions.
     *
     * @access  public
     * @param   string  $name    Function name.
     * @return  function
     */
    public function getRegisteredFunction ( $name ) {

        return $this->_createdFunctions[$name];
    }

    /**
     * Return the function corresponding to a rule name.
     *
     * @access  public
     * @param   string  $rule    Rule name.
     * @return  function
     */
    public function getFunctionForRule ( $rule ) {

        return $this->_rules[$rule];
    }

    /**
     * Get current token informations.
     *
     * @access  public
     * @param   string  $kind    Kind of informations: token, value, length,
     *                           or keep.
     * @return  string
     */
    public function getCurrentToken ( $kind = 'token' ) {

        return $this->_tokenSequence[$this->_currentState][$kind];
    }


    /**
     * Consume the current token and move to the next one.
     *
     * @access  public
     * @param   string  $ind    Indentation for debug.
     * @return  int
     */
    public function consumeToken ( $ind = '' ) {

        if(true === $this->debug)
            echo $ind,
                 'consumed token: ',
                 $this->_tokenSequence[$this->_currentState]['token'],
                 ' ("',
                 $this->_tokenSequence[$this->_currentState]['value'],
                 '")',
                 "\n";

        return $this->_errorState = ++$this->_currentState;
    }

    /**
     * Increments the rule ID.
     *
     * @access  public
     * @return  void
     */
    public function _incrementRule ( ) {

        ++$this->_rulesID;

        return;
    }

    /**
     * Decrements the rule ID.
     *
     * @access  public
     * @return  void
     */
    public function _decrementRule ( ) {

        unset($this->_rulesToken[$this->_rulesID--]);

        return;
    }

    /**
     * Resets the rule ID and mapping.
     *
     * @access  protected
     * @return  void
     */
    protected function resetRules ( ) {

        unset($this->_rulesToken);
        $this->_rulesToken = array();
        $this->_rulesID    = 0;

        return;
    }

    /**
     * Verify if the current token is correct.
     * Two options: if it does not exist in the map, the entry is created,
     * otherwise it is checked.
     *
     * @access  public
     * @param   int  $id    The ID for the current token value.
     * @return  bool
     */
    public function _verifyCurrentToken ( $id ) {

        $value = $this->getExpectedCurrentTokenValue($id);

        if(null === $value) {

            if(!isset($this->_rulesToken[$this->_rulesID]))
                $this->_rulesToken[$this->_rulesID] = array();

            $current = $this->getCurrentToken();

            if(!isset($this->_rulesToken[$this->_rulesID][$current]))
                $this->_rulesToken[$this->_rulesID][$current] = array();

            $this->_rulesToken[$this->_rulesID][$current][$id] =
                $this->getCurrentToken('value');

            return true;
        }

        return $value == $this->getCurrentToken('value') ? true : null;
    }

    /**
     * Retrieve the value of an expected token, w.r.t. a recorded snapshot.
     *
     * @access  public
     * @param   int  $id    The ID for the current token value.
     * @return  string
     */
    public function getExpectedCurrentTokenValue ( $id ) {

        if(isset($this->_rulesToken[$this->_rulesID])) {

            $tab     = $this->_rulesToken[$this->_rulesID];
            $current = $this->getCurrentToken();

            if(isset($tab[$current])) {

                $tab2 = $tab[$current];

                if(isset($tab2[$id]))
                    return $tab2[$id];
            }
        }

        return null;
    }

    /**
     * Check the existence of a token inside a token declaration.
     *
     * @access  public
     * @param   string  $token         The token name.
     * @param   array   $tokenArray    Token declaration: array(context =>
     *                                 array(token name => regexp)).
     * @return  bool
     */
    public function checkTokenExistence ( $token, $tokenArray ) {

        foreach($tokenArray as $tokens)
            foreach($tokens as $tokName => $tokValue)
                if(false !== strpos($tokName, ':')) {

                    $tab = explode(':',$tokName);

                    if($token == $tab[0])
                        return true;
                }
                elseif($tokName == $token)
                    return true;

        return false;
    }
}

}
