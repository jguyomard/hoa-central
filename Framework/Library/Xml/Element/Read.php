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
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_Read
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Xml_Exception
 */
import('Xml.Exception');

/**
 * Hoa_Xml_Element_Basic
 */
import('Xml.Element.Basic') and load();

/**
 * Hoa_Stream_Interface_In
 */
import('Stream.Interface.In') and load();

/**
 * Hoa_StringBuffer_ReadWrite
 */
import('StringBuffer.ReadWrite');

/**
 * Class Hoa_Xml_Element_Read.
 *
 * Read a XML element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.3
 * @package     Hoa_Xml
 * @subpackage  Hoa_Xml_Element_Read
 */

class          Hoa_Xml_Element_Read
    extends    Hoa_Xml_Element_Basic
    implements Hoa_Stream_Interface_In {

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @return  bool
     */
    public function eof ( ) {

        if(null === parent::$_buffer)
            return true;

        return parent::$_buffer->eof();
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     * @throw   Hoa_Xml_Exception
     */
    public function read ( $length ) {

        if($length <= 0)
            throw new Hoa_Xml_Exception(
                'Length must be greather than 0, given %d.', 0, $length);

        if(null === parent::$_buffer) {

            parent::$_buffer = new Hoa_StringBuffer_ReadWrite();
            parent::$_buffer->initializeWith($this->__toString());
        }

        return parent::$_buffer->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString ( $length ) {

        return $this->read($length);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @return  string
     */
    public function readCharacter ( ) {

        return $this->read(1);
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @return  bool
     */
    public function readBoolean ( ) {

        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger ( $length = 1 ) {

        return (int) $this->read($length);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat ( $length = 1 ) {

        return (float) $this->read($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @access  public
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray ( $argument = null ) {

        return (array) $this;
    }

    /**
     * Read a line.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( ) {

        $handle = $this->readAll();
        $n      = strpos($handle, "\n");

        if(false === $n)
            return $handle;

        return substr($handle, 0, $n);
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @return  string
     */
    public function readAll ( ) {

        return $this->__toString();
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf ( $format ) {

        return sscanf($this->readAll(), $format);
    }
}
