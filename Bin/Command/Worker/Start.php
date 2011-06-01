<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Worker\Backend\Shared
 */
-> import('Worker.Backend.Shared');

}

namespace Bin\Command\Worker {

/**
 * Class \Bin\Command\Main\Start.
 *
 * Start a worker.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Start extends \Hoa\Console\Dispatcher\Kit {

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('fastcgi', \Hoa\Console\GetOption::NO_ARGUMENT, 'f'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT, 'h'),
        array('help',    \Hoa\Console\GetOption::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $fastcgi = 'localhost:9000';

        while(false !== $c = $this->getOption($v)) switch($c) {

            case 'f':
                $fastcgi = $v;
              break;

            case 'h':
            case '?':
                return $this->usage();
              break;
        }

        $this->parser->listInputs($workerPath);

        if(null === $workerPath)
            return $this->usage();

        $output = \Hoa\Worker\Backend\Shared::start(
            'tcp://' . $fastcgi,
            $workerPath
        );

        cout($output);

        return;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : worker:start <options> <worker_path>');
        cout('Options :');
        cout($this->makeUsageOptionsList(array(
            'f'    => 'PHP-FPM socket URI (default: localhost:9000).',
            'help' => 'This help.'
        )));

        return;
    }
}

}
