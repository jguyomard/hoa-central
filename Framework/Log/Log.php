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
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Log
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Log_Exception
 */
import('Log.Exception');

/**
 * Hoa_Log_Backtrace
 */
import('Log.Backtrace');

/**
 * Hoa_Tree_Visitor_Dump
 */
import('Tree.Visitor.Dump');

/**
 * Hoa_Stream
 */
import('Stream.~');

/**
 * Hoa_Stream_Io_Out
 */
import('Stream.Io.Out');

/**
 * Class Hoa_Log.
 *
 * .
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Log
 */

class Hoa_Log {

    /**
     * Priority: emergency, system is unusable.
     * (Note: priorities from “The BSD Syslog Protocol” (RFC 3164, 4.1.1 PRI
     * Part).
     *
     * @const int
     */
    const EMERGENCY         =  0;

    /**
     * Priority: alert, action must be taken immediately.
     *
     * @const int
     */
    const ALERT             =  1;

    /**
     * Priority: critical, critical conditions.
     *
     * @const int
     */
    const CRITICAL          =  2;

    /**
     * Priority: error, error conditions.
     *
     * @const int
     */
    const ERROR             =  4;

    /**
     * Priority: warning, warning conditions.
     *
     * @const int
     */
    const WARNING           =  8;

    /**
     * Priority: notice, normal but significant condition.
     *
     * @const int
     */
    const NOTICE            = 16;

    /**
     * Priority: informational messages.
     *
     * @const int
     */
    const INFORMATIONAL     = 32;

    /**
     * Priority: debut-level messages.
     *
     * @const int
     */
    const DEBUG             = 64;

    /**
     * Stack index: timestamp.
     *
     * @const int
     */
    const STACK_TIMESTAMP   = 0;

    /**
     * Stack index: message.
     *
     * @const int
     */
    const STACK_MESSAGE     = 1;

    /**
     * Stack index: priority.
     *
     * @const int
     */
    const STACK_PRIORITY    = 2;

    /**
     * Stack index: memory.
     *
     * @const int
     */
    const STACK_MEMORY      = 3;

    /**
     * Stack index: memory peak.
     *
     * @const int
     */
    const STACK_MEMORY_PEAK = 4;

    /**
     * Multiton.
     *
     * @var Hoa_Log array
     */
    private static $_instances = null;

    /**
     * Current singleton index.
     *
     * @var Hoa_Log string
     */
    private static $_currentId = null;

    /**
     * Logs stack.
     *
     * @var Hoa_Log array
     */
    protected $_stack          = array();

    /**
     * Backtrace.
     *
     * @var Hoa_Log_Backtrace object
     */
    protected $_backtrace      = null;

    /**
     * Output stream array.
     *
     * @var Hoa_Log array
     */
    protected $_output         = array();

    /**
     * Filters (combination of priorities constants, null means all).
     *
     * @var Hoa_Log int
     */
    protected $_filter         = null;



    /**
     * Build a new log system.
     *
     * @access  private
     * @param   Hoa_Stream  $stream    Output stream (can be null).
     * @return  void
     */
    private function __construct ( $stream = null ) {

        $this->addOutputStreams($stream);
        $this->_backtrace = new Hoa_Log_Backtrace();
    }

    /**
     * Make a multiton.
     *
     * @access  public
     * @param   string      $id        Singleton ID.
     * @param   Hoa_Stream  $stream    Output stream (can be null).
     * @return  Hoa_Log
     * @throw   Hoa_Log_Exception
     */
    public static function getInstance ( $id = null, $stream = null ) {

        if(null === self::$_currentId && null === $id)
            throw new Hoa_Log_Exception(
                'Must precise a singleton index once.', 0);

        if(!isset(self::$_instances[$id]))
            self::$_instances[$id] = new self($stream);

        if(null !== $id)
            self::$_currentId = $id;

        return self::$_instances[self::$_currentId];
    }

    /**
     * Add many output streams.
     *
     * @access  public
     * @param   array   $streams    Array of output streams.
     * @return  array
     */
    public function addOutputStreams ( $streams ) {

        if(!is_array($streams))
            $streams = array($streams);

        foreach($streams as $i => $stream)
            $this->addOutputStream($stream);

        return $this->getOutputStack();
    }

    /**
     * Add an output stream. Must be a Hoa_Stream object but must also implement
     * the Hoa_Stream_Io_Out interface.
     *
     * @access  public
     * @param   Hoa_Stream  $stream    A stream (must implement the
     *                                 Hoa_Stream_Io_Out).
     * @return  array
     */
    public function addOutputStream ( Hoa_Stream $stream = null ) {

        if(null === $stream)
            return;

        if(!($stream instanceof Hoa_Stream_Io_Out))
            throw new Hoa_Log_Exception(
                'Stream log must implement the Hoa_Stream_Io_Out interface.', 0);

        if(false === $stream->isOpened())
            throw new Hoa_Log_Exception(
                'Stream log is not opened, maybe it failed.', 1);

        $this->_output[$stream->__toString()] = $stream;

        return $this->getOutputStack();
    }

    /**
     * Log a message with a type.
     *
     * @access  public
     * @param   string  $message    The log message.
     * @param   int     $type       Type of message (please, see the class
     *                              constants).
     * @return  void
     */
    public function log ( $message, $type = self::DEBUG ) {

        $this->_stack[] = array(
            self::STACK_TIMESTAMP   => microtime(true),
            self::STACK_MESSAGE     => $message,
            self::STACK_PRIORITY    => $type,
            self::STACK_MEMORY      => memory_get_usage(),
            self::STACK_MEMORY_PEAK => memory_get_peak_usage()
        );

        foreach($this->getOutputStack() as $i => $output)
            $output->writeAll($message . "\n");

        if($type & self::DEBUG)
            $this->_backtrace->debug();

        return;
    }

    /**
     * Get output stack.
     *
     * @access  protected
     * @return  array
     */
    protected function getOutputStack ( ) {

        return $this->_output;
    }

    /**
     * Get the backtrace tree.
     *
     * @access  public
     * @return  array
     */
    public function getBacktrace ( ) {

        return $this->_backtrace;
    }

    /**
     * Get the log stack.
     *
     * @access  public
     * @return  array
     */
    public function getLogStack ( ) {

        return $this->_stack;
    }

    /**
     * Transform a log type into a string.
     *
     * @access  public
     * @param   int     $type    Log type (please, see the class constants).
     * @return  string
     */
    public function typeAsString ( $type ) {

        switch($type) {

            case self::EMERGENCY:
                return 'EMERGENCY';
              break;

            case self::ALERT:
                return 'ALERT';
              break;

            case self::CRITICAL:
                return 'CRITICAL';
              break;

            case self::ERROR:
                return 'ERROR';
              break;

            case self::WARNING:
                return 'WARNING';
              break;

            case self::NOTICE:
                return 'NOTICE';
              break;

            case self::INFORMATIONAL:
                return 'INFORMATIONAL';
              break;

            case self::DEBUG:
                return 'DEBUG';
              break;

            default:
                return 'unknown';
        }
    }

    /**
     * Transform the log into a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->getBacktrace()->__toString();
    }
}


/**
 * Alias of Hoa_Log::getInstance()->log().
 *
 * @access  public
 * @param   string  $message    The log message.
 * @param   int     $type       Type of message (please, see the class
 *                              constants).
 * @return  void
 */
function hlog ( $message, $type = Hoa_Log::DEBUG ) {

    return Hoa_Log::getInstance()->log($message, $type);
}
