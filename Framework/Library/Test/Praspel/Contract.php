<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel_Contract
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Test_Praspel_Exception
 */
import('Test.Praspel.Exception');

/**
 * Hoa_Test_Praspel
 */
import('Test.Praspel.~');

/**
 * Hoa_Test_Praspel_Clause_Ensures
 */
import('Test.Praspel.Clause.Ensures');

/**
 * Hoa_Test_Praspel_Clause_Invariant
 */
import('Test.Praspel.Clause.Invariant');

/**
 * Hoa_Test_Praspel_Clause_Predicate
 */
import('Test.Praspel.Clause.Predicate');

/**
 * Hoa_Test_Praspel_Clause_Requires
 */
import('Test.Praspel.Clause.Requires');

/**
 * Hoa_Test_Praspel_Clause_Throwable
 */
import('Test.Praspel.Clause.Throwable');

/**
 * Hoa_Test_Praspel_Domain
 */
import('Test.Praspel.Domain');

/**
 * Hoa_Test_Praspel_Call
 */
import('Test.Praspel.Call');

/**
 * Hoa_Log
 */
import('Log.~');

/**
 * Class Hoa_Test_Praspel.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Praspel
 */

class Hoa_Test_Praspel_Contract {

    /**
     * Collection of clauses.
     *
     * @var Hoa_Test_Praspel_Contract array
     */
    protected $_clauses      = array();

    /**
     * The call object.
     *
     * @var Hoa_Test_Praspel_Call object
     */
    protected $_call         = null;

    /**
     * Log channel.
     *
     * @var Hoa_Log object
     */
    protected $_log          = null;

    /**
     * Class name that contains the method.
     *
     * @var Hoa_Test_Praspel_Contract string
     */
    protected $_class        = null;

    /**
     * Method name that is tested.
     *
     * @var Hoa_Test_Praspel_Contract string
     */
    protected $_method       = null;

    /**
     * File where the method is.
     *
     * @var Hoa_Test_Praspel_Contract string
     */
    protected $_file         = null;

    /**
     * Line where the method starts.
     *
     * @var Hoa_Test_Praspel_Contract int
     */
    protected $_startLine    = null;

    /**
     * Line where the method ends.
     *
     * @var Hoa_Test_Praspel_Contract int
     */
    protected $_endLine      = null;

    /**
     * Depth of the contract when calling it dynamically.
     *
     * @var Hoa_Test_Praspel_Contract int
     */
    protected static $_depth = -1;



    /**
     * Constructor. Create a default “requires” clause.
     *
     * @access  public
     * @param   string  $class        Class that contains the method.
     * @param   string  $method       Method name that is tested.
     * @param   string  $file         File where the method is.
     * @param   int     $startLine    Line where the method starts.
     * @param   int     $endLine      Line where the method ends.
     * @return  void
     */
    public function __construct ( $class = null, $method = null, $file = null,
                                  $startLine = -1, $endLine = -1 ) {

        $this->_class     = $class;
        $this->_method    = $method;
        $this->_file      = $file;
        $this->_startLine = $startLine;
        $this->_endLine   = $endLine;
        $this->_log       = Hoa_Log::getChannel(
            Hoa_Test_Praspel::LOG_CHANNEL
        );

        $this->clause('requires');
        $this->clause('ensures');

        return;
    }

    /**
     * Add a clause.
     *
     * @access  public
     * @param   string  $name    Clause name.
     * @return  Hoa_Test_Praspel_Clause
     * @throws  Hoa_Test_Praspel_Exception
     */
    public function clause ( $name ) {

        if(true === $this->clauseExists($name))
            return $this->_clauses[$name];

        $clause = null;

        switch($name) {

            case 'ensures':
                $clause = new Hoa_Test_Praspel_Clause_Ensures($this);
              break;

            case 'invariant':
                $clause = new Hoa_Test_Praspel_Clause_Invariant($this);
              break;

            case 'predicate':
                throw new Hoa_Test_Praspel_Exception(
                    'The predicate clause is not yet supported.', 0);
              break;

            case 'requires':
                $clause = new Hoa_Test_Praspel_Clause_Requires($this);
              break;

            case 'throwable':
                $clause = new Hoa_Test_Praspel_Clause_Throwable($this);
              break;

            default:
                throw new Hoa_Test_Praspel_Exception(
                    'Unknown clause %s.', 0, $name);
        }

        return $this->_clauses[$name] = $clause;
    }

    /**
     * Create a domain.
     *
     * @access  public
     * @param   string  $name    Domain name.
     * @param   ...     ...      Domain arguments.
     * @return  Hoa_Test_Urg_Type_Interface_Type
     */
    public function domain ( $name ) {

        $arguments = func_get_args();
        array_shift($arguments);
        $domain    = new Hoa_Test_Praspel_Domain($name, $arguments);

        return $domain->getDomain();
    }

    /**
     * Call a method.
     *
     * @access  public
     * @param   object  &$convict       Object where method is.
     * @param   string  $magicCaller    Magic caller name.
     * @param   string  $class          Class name.
     * @param   string  $method         Method name.
     * @return  void
     */
    public function call ( &$convict, $magicCaller, $class, $method ) {

        $old         = $this->_call;
        $this->_call = new Hoa_Test_Praspel_Call(
            $this,
            $convict,
            $magicCaller,
            $class,
            $method
        );

        return;
    }

    /**
     * Verify the pre-condition.
     *
     * @access  public
     * @param   mixed   ...    Parameters passed to the method.
     * @return  bool
     */
    public function verifyPreCondition ( ) {

        $args     = func_get_args();
        $i        = 0;
        $out      = true;
        $requires = $this->getClause('requires');
        $log      = array(
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $args,
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => $this->getDepth()
        );

        foreach($requires->getVariables() as $variable) {

            $o = false;

            foreach($variable->getDomains() as $domain)
                $o = $o || $domain->predicate($args[$i]);

            $out = $out && $o;

            if(false === $out) {

                $this->getLog()->log(
                    'The pre-condition failed.',
                    Hoa_Log::TEST,
                    $log
                );

                return FAILED;
            }

            ++$i;
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'The pre-condition succeed.',
            Hoa_Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Verify the post-condition.
     *
     * @access  public
     * @param   mixed   $result    Result of the method-call.
     * @param   mixed   ...        Parameters passed to the method.
     * @return  bool
     */
    public function verifyPostCondition ( $result ) {

        $args    = func_get_args();
        $result  = array_shift($args);
        $i       = 0;
        $out     = true;
        $ensures = $this->getClause('ensures');
        $log      = array(
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $args,
            'result'    => $result,
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED,
            'depth'     => $this->getDepth()
        );

        foreach($ensures->getVariables() as $variable) {

            if('\result' == $variable->getName())
                $arg = $result;
            else
                $arg = $args[$i++];

            $o = false;

            foreach($variable->getDomains() as $domain)
                $o = $o || $domain->predicate($arg);

            $out = $out && $o;

            if(false === $out) {

                $this->getLog()->log(
                    'The post-condition failed.',
                    Hoa_Log::TEST,
                    $log
                );

                return FAILED;
            }
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log(
            'The post-condition succeed.',
            Hoa_Log::TEST,
            $log
        );

        return SUCCEED;
    }

    /**
     * Increment 1 to the depth.
     *
     * @access  public
     * @return  int
     */
    public function setDepthInc ( ) {

        return ++self::$_depth;
    }

    /**
     * Decrement 1 to the depth.
     *
     * @access  public
     * @return  int
     */
    public function setDepthDec ( ) {

        return --self::$_depth;
    }

    /**
     * Get the current depth.
     *
     * @access  public
     * @return  int
     */
    public function getDepth ( ) {

        return self::$_depth;
    }

    /**
     * Verify clauses.
     *
     * @access  public
     * @return  void
     */
    public function verify ( ) {

        $requires  = $this->getClause('requires');
        $ensures   = $this->getClause('ensures');
        $call      = $this->getCall();

        $log       = array(
            'class'     => $this->getClass(),
            'method'    => $this->getMethod(),
            'arguments' => $call->getValues(),
            'result'    => $call->getResult(),
            'file'      => $this->getFile(),
            'startLine' => $this->getStartLine(),
            'endLine'   => $this->getEndLine(),
            'status'    => FAILED
        );

        if(true === $call->hasException()) {

            $exception = get_class($call->getException());

            if(false === $this->clauseExists('throwable')) {

                $this->getLog()->log(
                    'An exception (' . $exception .
                    ') occured and no @throwable was declared.',
                    Hoa_Log::TEST,
                    $log
                );

                return;
            }

            $throwable = $this->getClause('throwable');

            if(false === $throwable->exceptionExists($exception)) {

                $this->getLog()->log(
                    'The exception ' . $exception .
                    ' was thrown but not declared in the @throwable clause.',
                    Hoa_Log::TEST,
                    $log
                );

                return;
            }

            $log['status'] = SUCCEED;
            $this->getLog()->log(
                'The exception ' . $exception .
                ' was thrown and it is normal.',
                Hoa_Log::TEST,
                $log
            );

            return;
        }

        foreach($ensures->getVariables() as $e => $variable) {

            if('\result' == $variable->getName()) {

                $handle = $variable->getChoosenDomain()->predicate(
                    $call->getResult()
                );

                if(false === $handle) {

                    $this->getLog()->log(
                        'Returned ' . $call->getResult(),
                        Hoa_Log::TEST,
                        $log
                    );

                    return;
                }
            }
        }

        $log['status'] = SUCCEED;
        $this->getLog()->log('Bingo!', Hoa_Log::TEST, $log);

        return;
    }

    /**
     * Check if a clause already exists or not.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  bool
     */
    public function clauseExists ( $name ) {

        return isset($this->_clauses[$name]);
    }

    /**
     * Get a specific clause.
     *
     * @access  public
     * @param   string     $name    Clause name.
     * @return  Hoa_Test_Praspel_Clause
     * @throw   Hoa_Test_Praspel_Exception
     */
    public function getClause ( $name ) {

        if(false === $this->clauseExists($name))
            throw new Hoa_Test_Praspel_Exception(
                'Clause %s is not defined.', 1, $name);

        return $this->_clauses[$name];
    }

    /**
     * Get all clauses.
     *
     * @access  public
     * @return  array
     */
    public function getClauses ( ) {

        return $this->_clauses;
    }

    /**
     * Get the call.
     *
     * @access  public
     * @return  Hoa_Test_Praspel_Call
     */
    public function getCall ( ) {

        return $this->_call;
    }

    /**
     * Get class name that contains the method.
     *
     * @access  public
     * @return  string
     */
    public function getClass ( ) {

        return $this->_class;
    }

    /**
     * Get method name that is tested.
     *
     * @access  public
     * @return  string
     */
    public function getMethod ( ) {

        return $this->_method;
    }

    /**
     * Get file name where the method is.
     *
     * @access  public
     * @return  string
     */
    public function getFile ( ) {

        return $this->_file;
    }

    /**
     * Get line where the method starts.
     *
     * @access  public
     * @return  int
     */
    public function getStartLine ( ) {

        return $this->_startLine;
    }

    /**
     * Get line where the method ends.
     *
     * @access  public
     * @return  int
     */
    public function getEndLine ( ) {

        return $this->_endLine;
    }

    /**
     * Get log.
     *
     * @access  public
     * @return  Hoa_Log
     */
    public function getLog ( ) {

        return $this->_log;
    }

    /**
     * Get contract ID.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->getClass() . '::' . $this->getMethod();
    }

    /**
     * Transform this object model into Praspel.
     *
     * @access  public
     * @return  string
     */
    public function __toPraspel ( ) {

        $out = null;

        foreach($this->getClauses() as $i => $clause)
            $out .= $clause->__toPraspel();

        return $out;
    }

    /**
     * Transform this object model into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = '$contract  = new ' . get_class($this) . '(' . "\n" .
               '    $class,' . "\n" .
               '    $method,' . "\n" .
               '    $file,' . "\n" .
               '    $startLine,' . "\n" .
               '    $endLine' . "\n" .
               ');' . "\n\n";

        foreach($this->getClauses() as $i => $clause)
            $out .= $clause->__toString() . "\n";

        return $out;
    }
}