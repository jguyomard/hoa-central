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
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Plugin_Interface
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Interface Hoa_Tokenizer_Plugin_Interface.
 *
 * Interface of tokenizer builder plugins.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Plugin_Interface
 */

interface Hoa_Tokenizer_Plugin_Interface {

    /**
     * Receive a token.
     *
     * @access  public
     * @param   mixed   $token         Token type (int or string).
     * @param   string  $content       Token content.
     * @param   int     $lineNumber    Token line number.
     * @return  array
     * @throw   Hoa_Tokenizer_Plugin_Exception
     */
    public function receive ( $token, $content, $lineNumber );
}
