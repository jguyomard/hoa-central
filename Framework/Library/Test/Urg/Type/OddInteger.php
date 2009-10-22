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
 * @subpackage  Hoa_Test_Urg_Type_OddInteger
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
 * Hoa_Test_Urg_Type_Exception_Maxtry
 */
import('Test.Urg.Type.Exception.Maxtry');

/**
 * Hoa_Test_Urg_Type_Interface_Type
 */
import('Test.Urg.Type.Interface.Type');

/**
 * Hoa_Test_Urg_Type_BoundInteger
 */
import('Test.Urg.Type.BoundInteger');

/**
 * Hoa_Test
 */
import('Test.~');

/**
 * Class Hoa_Test_Urg_Type_OddInteger.
 *
 * Represent an even integer.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 *              Julien LORRAIN <julien.lorrain@gmail.com>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Urg_Type_OddInteger
 */

class Hoa_Test_Urg_Type_OddInteger extends    Hoa_Test_Urg_Type_BoundInteger
                                   implements Hoa_Test_Urg_Type_Interface_Type {

    /**
     * Build an even integer.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        parent::__construct(
            (parent::getNegativeInfinity() / 2) + 1,
            (parent::getPositiveInfinity() - 1) / 2,
            parent::BOUND_CLOSE,
            parent::BOUND_CLOSE
        );

        return;
    }

    /**
     * A predicate.
     *
     * @access  public
     * @param   int     $q    Q-value.
     * @return  bool
     */
    public function predicate ( $q = null ) {

        if(null === $q)
            $q = $this->getValue();

        return $q % 2 != 0;
    }

    /**
     * Choose a random value.
     *
     * @access  public
     * @return  void
     * @throws  Hoa_Test_Urg_Type_Exception_Maxtry
     */
    public function randomize ( ) {

        $maxtry = Hoa_Test::getInstance()->getParameter('test.maxtry');

        do {

            parent::randomize();
            $random = $this->getValue() * 2 - 1;

        } while(false === $this->predicate($random) && $maxtry-- > 0);

        if($maxtry == -1)
            throw new Hoa_Test_urg_Type_Exception_Maxtry(
                'All tries failed (%d tries).',
                0, Hoa_Test::getInstance()->getParameter('test.maxtry'));

        return;
    }
}