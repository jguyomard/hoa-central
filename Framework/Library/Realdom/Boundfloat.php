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
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Boundfloat
 *
 */

/**
 * Hoa_Realdom_Float
 */
import('Realdom.Float') and load();

/**
 * Class Hoa_Realdom_Boundfloat.
 *
 * Realistic domain: boundfloat.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Boundfloat
 */

class Hoa_Realdom_Boundfloat extends Hoa_Realdom_Float {

    /**
     * Realistic domain name.
     *
     * @var Hoa_Realdom string
     */
    protected $_name = 'boundfloat';

    /**
     * Lower bound value.
     *
     * @var Hoa_Realdom_Boundfloat int
     */
    protected $_lower = 0;

    /**
     * Upper bound value.
     *
     * @var Hoa_Realdom_Boundfloat int
     */
    protected $_upper = 0;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   float   $lower    Lower bound value.
     * @param   float   $upper    Upper bound value.
     * @return  void
     */
    public function construct ( $lower = null, $upper = null ) {

        if(null === $lower)
            $lower = (float) ~PHP_INT_MAX;

        if(null === $upper)
            $upper = (float)  PHP_INT_MAX;

        $this->_lower = (float) min($lower, $upper);
        $this->_upper = (float) max($lower, $upper);

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed   $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        return    parent::predicate($q)
               && $q > $this->getLower()
               && $q < $this->getUpper();
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function _sample ( Hoa_Test_Sampler $sampler ) {

        return $sampler->getFloat($this->getLower(), $this->getUpper());
    }

    /**
     * Get the lower bound value.
     *
     * @access  public
     * @return  int
     */
    public function getLower ( ) {

        return $this->_lower;
    }

    /**
     * Get the upper bound value.
     *
     * @access  public
     * @return  int
     */
    public function getUpper ( ) {

        return $this->_upper;
    }
}
