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
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_StringLength
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Validate_Abstract
 */
import('Validate.Abstract');

/**
 * Class Hoa_Validate_StringLength.
 *
 * Validate a data, should be 
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Validate
 * @subpackage  Hoa_Validate_StringLength
 */

class Hoa_Validate_StringLength extends Hoa_Validate_Abstract {

    /**
     * Is not greater than.
     *
     * @const string
     */
    const IS_TOO_LONG  = 'isTooLong';
    const IS_TOO_SHORT = 'isTooShort';

    /**
     * Errors messages
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $errors = array(
        self::IS_TOO_SHORT => 'Data is too short, the minimum is %s, given %s.',
        self::IS_TOO_LONG  => 'Data is too long, the maximum is %s, given %s.'
    );

    /**
     * Needed arguments.
     *
     * @var Hoa_Validate_Abstract array
     */
    protected $arguments = array(
        'min' => 'specify the minimum length of the given string',
        'max' => 'specify the maximum length of the given string'
    );



    /**
     * Check if a data is valid.
     *
     * @access  public
     * @param   string  $data    Data to valid.
     * @return  bool
     * @throw   Hoa_Validate_Exception
     */
    public function isValid ( $data = null ) {

        $return = true;

        if(strlen($data) < $this->getValidatorArgument('min')) {

            $this->addOccuredError(
                self::IS_TOO_SHORT,
                array($this->getValidatorArgument('min'), strlen($data))
            );

            $return = false;
        }

        if(strlen($data) > $this->getValidatorArgument('max')) {

            $this->addOccuredError(
                self::IS_TOO_LONG,
                array($this->getValidatorArgument('max'), strlen($data))
            );

            $return = false;
        }

        return $return;
    }
}
