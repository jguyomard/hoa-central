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
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Decorator_Fieldset
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Form_Exception
 */
import('Form.Exception');

/**
 * Hoa_Form_Decorator_Abstract
 */
import('Form.Decorator.Abstract');

/**
 * Class Hoa_Form_Decorator_Fieldset.
 *
 * Decorate the fieldset element.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Form
 * @subpackage  Hoa_Form_Decorator_Fieldset
 */

class Hoa_Form_Decorator_Fieldset extends Hoa_Form_Decorator_Abstract {

    /**
     * Make a render of a fieldset.
     *
     * @access  public
     * @param   Hoa_Form_Element_Abstract  $element      The element.
     * @param   Hoa_Form_Element_Label     $label        The associated label
     *                                                   element.
     * @param   Hoa_Validate_Abstract      $validator    The element validator
     *                                                   collection.
     * @return  string
     */
    public function render ( Hoa_Form_Element_Abstract $element,
                             Hoa_Form_Element_Label    $label,
                             Hoa_Validate_Abstract     $validator ) {

        return '  ' . str_replace("\n", "\n  ", $element . '') . "\n";
    }
}
