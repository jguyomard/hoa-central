<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Apc
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Cache_Exception
 */
import('Cache.Exception');

/**
 * Hoa_Cache_Backend
 */
import('Cache.Backend');

/**
 * Class Hoa_Cache_Backend_Apc.
 *
 * Alternative PHP Cache backend manager.
 * APC is a PECL extension, so it's not installed in PHP. Take care that APC
 * module is loaded.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Cache
 * @subpackage  Hoa_Cache_Backend_Apc
 */

class Hoa_Cache_Backend_Apc extends Hoa_Cache_Backend {

    /**
     * Check if APC is loaded, else an exception is thrown.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function __construct ( Array $parameters = array() ) {

        if(!extension_loaded('apc'))
            throw new Hoa_Cache_Exception(
                'APC (PECL extension) is not loaded on server.', 0);

        parent::__construct($parameters);

        return;
    }

    /**
     * Save cache content in APC store.
     *
     * @access  public
     * @param   mixed  $data    Data to store.
     * @return  mixed
     */
    public function store ( $data ) {

        $this->clean();

        if(false !== $this->getParameter('serialize_content'))
            $data  = serialize($data);

        return apc_store(
            $this->getIdMd5(),
            $data,
            $this->getParameter('lifetime')
        );
    }

    /**
     * Load data from APC cache.
     *
     * @access  public
     * @return  void
     */
    public function load ( ) {

        $this->clean();

        $content = apc_fetch($this->getIdMd5());

        if(true === $this->getParameter('serialize_content'))
            $content = unserialize($content);

        return $content;
    }

    /**
     * Clean expired cache.
     * Note : Hoa_Cache::CLEAN_EXPIRED is not supported with APC.
     *
     * @access  public
     * @param   int  $lifetime    Lifetime of caches.
     * @return  void
     * @throw   Hoa_Cache_Exception
     */
    public function clean ( $lifetime = Hoa_Cache::CLEAN_USER ) {

        switch($lifetime) {

            case Hoa_Cache::CLEAN_ALL:
                return apc_clear_cache();
              break;

            case Hoa_Cache::CLEAN_EXPIRED:
                throw new Hoa_Cache_Exception(
                    'Hoa_Cache::CLEAN_EXPIRED constant is not supported by ' .
                    'APC cache backend.', 1);
              break;

            case Hoa_Cache::CLEAN_USER:
            default:
                return apc_clear_cache('user');
        }

        return;
    }

    /**
     * Remove a cache data.
     *
     * @access  public
     * @return  void
     */
    public function remove ( ) {

        apc_delete($this->getIdMd5());

        return;
    }
}
