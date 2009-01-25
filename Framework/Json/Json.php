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
 * @package     Hoa_Json
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Json_Exception
 */
import('Json.Exception');

/**
 * Hoa_StdClass
 */
import('StdClass.~');

/**
 * Class Hoa_Json.
 *
 * Manipule JSON.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Json
 */

class Hoa_Json extends Hoa_StdClass {

    /**
     * Error : no error has occured.
     *
     * @const int
     */
    const ERROR_NONE           = 0;

    /**
     * Error : the maximum stack depth has been exceeded.
     *
     * @const int
     */
    const ERROR_DEPTH          = 1;

    /**
     * Error : state mismatch (in parser).
     * It is not officially in documentation, but it is declared
     * in /php5.3-dev/ext/json/JSON_parser.h (dev = 200901251130).
     *
     * @const int
     */
    const ERROR_STATE_MISMATCH = 2;

    /**
     * Error : Control character error, possibly incorrectly encoded.
     *
     * @const int
     */
    const ERROR_CTRL_CHAR      = 3;

    /**
     * Error : syntax error.
     *
     * @const int
     */
    const ERROR_SYNTAX         = 4;



    /**
     * Convert a JSON tree into a Hoa_StdClass class.
     *
     * @access  public
     * @param   string  $json    JSON string.
     * @return  void
     * @throw   Hoa_Json_Exception
     */
    public function __construct ( $json = '' ) {

        if(false === function_exists('json_decode'))
            if(false === version_compare(phpversion(), '5.2.0', '>'))
                throw new Hoa_Json_Exception(
                    'JSON extension is available since PHP 5.2.0.', 0);
            else
                throw new Hoa_Json_Exception(
                    'JSON extension is disabled.', 1);

        $json = json_decode($json, true);

        if(false === $this->hasError())
            throw new Hoa_Json_Exception(
                $this->getLastError(), 2);

        parent::__construct($json);
    }

    /**
     * Check if an error ocurred when parsing JSON string.
     *
     * @access  public
     * @return  bool
     */
    public function hasError ( ) {

        if(false === version_compare(phpversion(), '5.3.0', '>'))
            return true; // cannot find if an error has occured.

        return json_last_error() != self::ERROR_NONE;
    }

    /**
     * Get last error message.
     *
     * @access  public
     * @return  string
     */
    public function getLastError ( ) {

        if(false === version_compare(phpversion(), '5.3.0', '>'))
            return '';

        $out = null;

        switch(json_last_error()) {

            case self::ERROR_NONE:
                $out = 'No error has occured.';
              break;

            case self::ERROR_DEPT:
                $out = 'The maximum stack depth has been exceeded.';
              break;

            case self::ERROR_STATE_MISMATCH:
                $out = 'State mismatch (in parser).';
              break;

            case self::ERROR_CTRL_CHAR:
                $out = 'Control character error, possibly incorrectly encoded.';
              break;

            case self::ERROR_SYNTAX:
                $out = 'Syntax error.';
              break;
        }

        return $out;
    }

    /**
     * Overload the parent::__toString() method to produce a JSON string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return $this->toJson($this->toArray());
    }

    /**
     * Overload the parent::toJson() method to produce a JSON string.
     *
     * @access  public
     * @param   mixed   $value    Value to encode in JSON.
     * @return  string
     * @throw   Hoa_Json_Exception
     */
    public function toJson ( $value = null ) {

        if(is_resource($value))
            throw new Hoa_Json_Exception(
                'JSON cannot encode a resource.', 0);

        return json_encode($value);
    }
}