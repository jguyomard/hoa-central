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
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Void
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Test_Urg_Type_Exception
 */
import('Test.Urg.Type.Exception');

/**
 * Hoa_Test_Urg_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg
 */
import('Test.Urg.~');

/**
 * Class Hoa_Test_Urg_Type_Void.
 *
 * Represent a null value.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_Void
 */

class Hoa_Test_Urg_Type_Void implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Random value.
     *
     * @var Hoa_Test_Urg_Type_Void null
     */
    protected $_value = void;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) { }

    /**
     * A predicate.
     *
     * @access  public
     * @param   mixed   $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        return void === $q;
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     */
    public function randomize ( ) {

        return;
    }

    /**
     * Get the random value.
     *
     * @access  protected
     * @return  void
     */
    public function getValue ( ) {

        return void;
    }
}