<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Pom_Token_Clone
 *
 */

/**
 * Hoa_Pom_Token_Util_Exception
 */
import('Pom.Token.Util.Exception');

/**
 * Hoa_Pom
 */
import('Pom.~');

/**
 * Hoa_Pom_Token_Variable
 */
import('Pom.Token.Variable');

/**
 * Hoa_Visitor_Element
 */
import('Visitor.Element');

/**
 * Class Hoa_Pom_Token_Clone.
 *
 * Represent a cloning.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Clone
 */

class Hoa_Pom_Token_Clone implements Hoa_Visitor_Element {

    /**
     * Object name.
     *
     * @var Hoa_Pom_Token_Variable object
     */
    protected $_object = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $object    Object name.
     * @return  void
     */
    public function __construct ( Hoa_Pom_Token_Variable $object ) {

        $this->setObject($object);

        return;
    }

    /**
     * Set object name.
     *
     * @access  public
     * @param   Hoa_Pom_Token_Variable  $object    Object name.
     * @return  Hoa_Pom_Token_Variable
     */
    public function setObject ( Hoa_Pom_Token_Variable $object ) {

        $old           = $this->_object;
        $this->_object = $object;

        return $old;
    }

    /**
     * Get object name.
     *
     * @access  public
     * @return  Hoa_Pom_Token_Variable
     */
    public function getObject ( ) {

        return $this->_object;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function accept ( Hoa_Visitor_Visit $visitor,
                             &$handle = null,
                              $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}
