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
 * Class \Hoa\Cache\Frontend\AFunction.
 *
 * Function catching system for frontend cache.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class AFunction extends Frontend {

    /**
     * Function arguments.
     *
     * @var \Hoa\Cache\Frontend\Funtion array
     */
    protected $_arguments = array();



    /**
     * Overload member class with __call.
     *
     * @access  public
     * @param   string  $function     Function called.
     * @param   array   $arguments    Arguments of method.
     * @return  mixed
     * @throw   \Hoa\Cache\Exception
     */
    public function __call ( $function, Array $arguments ) {

        if(!function_exists($function))
            throw new \Hoa\Cache\Exception('Function %s does not exists.',
                0, $function);

        $this->_arguments = $this->ksort($arguments);
        $idExtra          = serialize($this->_arguments);
        $this->makeId($function . '/' . $idExtra);
        $content          = $this->_backend->load();

        if(false !== $content) {

            echo $content[0];   // output

            return $content[1]; // return
        }

        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array($function, $arguments);
        $output = ob_get_contents();
        ob_end_clean();

        $this->_backend->store(array($output, $return));
        $this->removeId();

        echo $output;

        return $return;
    }
}

}
