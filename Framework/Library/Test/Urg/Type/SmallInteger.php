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
 * @subpackage  Hoa_Test_Urg_Type_SmallInteger
 *
 */

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Type_BoundInteger
 */
import('Test.Urg.Type.BoundInteger');

/**
 * Class Hoa_Test_Urg_Type_SmallInteger.
 *
 * Represent a small integer.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_SmallInteger
 */

class Hoa_Test_Urg_Type_SmallInteger extends Hoa_Test_Urg_Type_BoundInteger {

    /**
     * Name of type.
     *
     * @var Hoa_Test_Urg_Type_Interface_Type string
     */
    protected $_name = 'smallInteger';



    /**
     * Build a small integer.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        parent::__construct(-128, 127, parent::BOUND_OPEN, parent::BOUND_OPEN);
        parent::setArguments();

        return;
    }
}
