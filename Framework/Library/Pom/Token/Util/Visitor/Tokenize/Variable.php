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
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Variable
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
 * Hoa_Visitor_Registry_Aggregate
 */
import('Visitor.Registry.Aggregate');

/**
 * Class Hoa_Pom_Token_Util_Visitor_Tokenize_Variable.
 *
 * Visit a variable.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Pom
 * @subpackage  Hoa_Pom_Token_Util_Visitor_Tokenize_Variable
 */

class Hoa_Pom_Token_Util_Visitor_Tokenize_Variable extends Hoa_Visitor_Registry_Aggregate {

    /**
     * Visit a variable.
     *
     * @access  public
	 * @param   Hoa_Visitor_Element  $element    Element to visit.
	 * @param   mixed                $handle     Handle (reference).
     * @param   mixed                $eldnah     Handle (not reference).
     * @return  array
     */
    public function visitVariable ( Hoa_Visitor_Element $element,
                                    &$handle = null,
                                     $eldnah = null ) {

        return array(array(
            0 => Hoa_Pom::_VARIABLE,
            1 => '$' . $element->getName()->getString(),
            2 => -1
        ));
    }
}
