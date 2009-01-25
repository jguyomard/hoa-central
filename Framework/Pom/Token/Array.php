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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Array
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom_Token_Util_Interface_Tokenizable
 */
import('Pom.Token.Util.Interface.Tokenizable');

/**
 * Hoa_Pom_Token_Util_Interface_SuperScalar
 */
import('Pom.Token.Util.Interface.SuperScalar');

/**
 * Hoa_Pom_Token_Util_Interface_Type
 */
import('Pom.Token.Util.Interface.Type');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Class Hoa_Pom_Token_Array.
 *
 * Represent an array (aïe, not easy …).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Array
 */

class Hoa_Pom_Token_Array implements Hoa_Pom_Token_Util_Interface_Tokenizable,
                                     Hoa_Pom_Token_Util_Interface_SuperScalar,
                                     Hoa_Pom_Token_Util_Interface_Type {

    /**
     * Represent a key of an array.
     *
     * @const int
     */
    const KEY   = 0;

    /**
     * Represent a value of an array.
     *
     * @const int
     */
    const VALUE = 1;

    /**
     * Set of key/value that constitute an array.
     *
     * @var Hoa_Pom_Token_Array array
     */
    protected $_array = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed   $elements    Could be an instance of an element or a
     *                               collection of elements.
     * @return  void
     */
    public function __construct ( $elements = array() ) {

        $this->addElements((array) $elements);

        return;
    }

    /**
     * Add many elements.
     *
     * @access  public
     * @param   array   $elements    Add many elements to the array.
     * @return  array
     */
    public function addElements ( Array $elements = array() ) {

        foreach($elements as $i => $element)
            $this->addElement($element[self::KEY], $element[self::VALUE]);

        return $this->getArray();
    }

    /**
     * Add an element.
     *
     * @access  public
     * @param   mixed   $key      Key to add. Null to auto-increment.
     * @param   mixed   $value    Value to add.
     * @return  array
     * @throw   Hoa_Pom_Token_Util_Exception
     */
    public function addElement ( $key, $value ) {

        if(null !== $key)
            switch(get_class($key)) {

                case 'Hoa_Pom_Token_Call':
                case 'Hoa_Pom_Token_Clone':
                case 'Hoa_Pom_Token_Comment':
                case 'Hoa_Pom_Token_Number_DNumber':
                case 'Hoa_Pom_Token_Number_LNumber':
                case 'Hoa_Pom_Token_New':
                case 'Hoa_Pom_Token_Operation':
                case 'Hoa_Pom_Token_String_Boolean':
                case 'Hoa_Pom_Token_String_Constant':
                case 'Hoa_Pom_Token_String_EncapsedConstant':
                case 'Hoa_Pom_Token_String_Null':
                case 'Hoa_Pom_Token_Variable':
                  break;

                default:
                    throw new Hoa_Pom_Token_Util_Exception(
                        'An array key cannot accept a class that ' .
                        'is an instance of %s.', 0, get_class($key));
            }

        switch(get_class($value)) {

            case 'Hoa_Pom_Token_Array':
            case 'Hoa_Pom_Token_Call':
            case 'Hoa_Pom_Token_Clone':
            case 'Hoa_Pom_Token_Comment':
            case 'Hoa_Pom_Token_Number_DNumber':
            case 'Hoa_Pom_Token_Number_LNumber':
            case 'Hoa_Pom_Token_New':
            case 'Hoa_Pom_Token_Operation':
            case 'Hoa_Pom_Token_String_Boolean':
            case 'Hoa_Pom_Token_String_Constant':
            case 'Hoa_Pom_Token_String_EncapsedConstant':
            case 'Hoa_Pom_Token_String_Null':
            case 'Hoa_Pom_Token_Variable':
              break;

            default:
                throw new Hoa_Pom_Token_Util_Exception(
                    'An array value cannot accept a class that ' .
                    'is an instance of %s.', 1, get_class($value));
        }

        return $this->_array[] = array(
            self::KEY   => $key,
            self::VALUE => $value
        );
    }

    /**
     * Get the complete array.
     *
     * @access  protected
     * @return  array
     */
    protected function getArray ( ) {

        return $this->_array;
    }

    /**
     * Empty this array.
     *
     * @access  public
     * @return  array
     */
    public function emptyMe ( ) {

        $old          = $this->_array;
        $this->_array = array();

        return $old;
    }

    /**
     * Check if this array is empty or not.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty ( ) {

        return $this->getArray() == array();
    }

    /**
     * Check if a data is an uniform super-scalar or not.
     *
     * @access  public
     * @return  bool
     */
    public function isUniformSuperScalar ( ) {

        $old     = null;
        $current = null;

        foreach($this->getArray() as $i => $entry) {

            if($entry instanceof Hoa_Pom_Token_Util_Interface_SuperScalar)
                if($entry->isUniformSuperScalar())
                    continue;
                else
                    return false;

            if(!($entry instanceof Hoa_Pom_Token_Util_Interface_Scalar))
                return false;

            if(null === $old) {

                $old = get_class($entry);
                continue;
            }

            $current = get_class($entry);

            if($current != $old)
                return false;

            $old = $current;
        }

        return true;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function tokenize ( ) {

        $first  = true;
        $array  = array();
        $handle = null;

        foreach($this->getArray() as $i => $a) {

            if(false === $first)
                $array[] = array(
                    0 => Hoa_Pom::_COMMA,
                    1 => ',',
                    2 => -1
                );
            else
                $first = false;

            $handle = array_merge(
                (null !== $a[self::KEY]
                     ? array_merge(
                           $a[self::KEY]->tokenize(),
                           array(array(
                               0 => Hoa_Pom::_DOUBLE_ARROW,
                               1 => '=>',
                               2 => -1
                           ))
                       )
                     : array()
                ),
                $a[self::VALUE]->tokenize()
            );

            foreach($handle as $key => $value)
                $array[] = $value;
        }

        return array_merge(
            array(array(
                0 => Hoa_Pom::_ARRAY,
                1 => 'array',
                2 => -1
            )),
            array(array(
                0 => Hoa_Pom::_OPEN_PARENTHESES,
                1 => '(',
                2 => -1
            )),
            $array,
            array(array(
                0 => Hoa_Pom::_CLOSE_PARENTHESES,
                1 => ')',
                2 => -1
            ))
        );
    }
}