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
 * @subpackage  Hoa_Test_Sampler_Random
 *
 */

/**
 * Hoa_Test_Sampler
 */
import('Test.Sampler.~');

/**
 * Class Hoa_Test_Sampler_Random.
 *
 * Random sampler.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Test
 * @subpackage  Hoa_Test_Sampler_Random
 */

class Hoa_Test_Sampler_Random extends Hoa_Test_Sampler {

    /**
     * Generate a discrete uniform distribution.
     *
     * @access  protected
     * @param   int  $lower    Lower bound value.
     * @param   int  $upper    Upper bound value.
     * @return  int
     */
    protected function _getInteger ( $lower, $upper ) {

        return (int) $this->_getFloat($lower, $upper);
    }

    /**
     * Generate a continuous uniform distribution.
     *
     * @access  protected
     * @param   float      $lower    Lower bound value.
     * @param   float      $upper    Upper bound value.
     * @return  float
     */
    protected function _getFloat ( $lower, $upper ) {

        return $lower + lcg_value() * ($upper - $lower);
    }
}