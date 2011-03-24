<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Cache\Frontend
 */
-> import('Cache.Frontend.~');

}

namespace Hoa\Cache\Frontend {

/**
 * Class \Hoa\Cache\Frontend\Output.
 *
 * Ouput catching system for frontend cache.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class Output extends Frontend {

    /**
     * Output buffer level.
     *
     * @var \Hoa\Cache\Frontend\Output array
     */
    protected $_level = array();



    /**
     * Start an output buffering.
     *
     * @access  public
     * @param   string  id    ID of cache.
     * @return  bool
     */
    public function start ( $id = null ) {

        $this->makeId($id);
        $md5 = $this->getIdMd5();
        $out = $this->_backend->load();

        if(false !== $out) {

            echo $out;

            return false;
        }

        ob_start();
        ob_implicit_flush(false);
        $this->_level[$md5] = ob_get_level();

        return true;
    }

    /**
     * End an output buffering.
     *
     * @access  public
     * @return  void
     */
    public function end ( ) {

        $content = '';
        $md5     = $this->getIdMd5();

        while(ob_get_level() >= $this->_level[$md5])
            $content .= ob_get_clean();

        $this->_backend->store($content);
        $this->removeId();

        echo $content;

        return;
    }
}

}
