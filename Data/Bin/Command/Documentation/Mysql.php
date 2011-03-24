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
 * \Hoa\Console\System
 */
-> import('Console.System.~');

/**
 * Class MysqlCommand.
 *
 * Search in different MySQL reference manuals.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class MysqlCommand extends \Hoa\Console\Command\Generic {

    /**
     * Author name.
     *
     * @var VersionCommand string
     */
    protected $author      = 'Ivan Enderlin';

    /**
     * Program name.
     *
     * @var VersionCommand string
     */
    protected $programName = 'Mysql';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('all',               parent::NO_ARGUMENT, 'd'),
        array('ref3',              parent::NO_ARGUMENT, '3'),
        array('ref4',              parent::NO_ARGUMENT, '4'),
        array('ref5',              parent::NO_ARGUMENT, '5'),
        array('ref6',              parent::NO_ARGUMENT, '6'),
        array('administrator',     parent::NO_ARGUMENT, 'a'),
        array('query-browser',     parent::NO_ARGUMENT, 'q'),
        array('migration-toolkit', parent::NO_ARGUMENT, 'm'),
        array('workbench',         parent::NO_ARGUMENT, 'w'),
        array('help',              parent::NO_ARGUMENT, 'h'),
        array('help',              parent::NO_ARGUMENT, '?')
    );



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        $path = 'hoa://Data/Etc/Configuration/.Cache/HoaConsole.php';

        if(!file_exists($path))
            throw new \Hoa\Console\Command\Exception(
                'The cache “Console” is not found in %s. Must generate it.',
                0, $path);

        parent::listInputs($search);

        if(null === $search)
            return $this->usage();

        $cache   = require $path;
        $browser = $cache['parameters']['command.browser'];
        $docType = array();
        $search  = urlencode($search);

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case '3':
                case '4':
                    $docType[] = 'refman-41';
                  break;

                case '5':
                    $docType[] = 'refman-50';
                    $docType[] = 'refman-51';
                  break;

                case '6': 
                    $docType[] = 'refman-60';
                  break;

                case 'a':
                    $docType[] = 'refman-administrator';
                  break;

                case 'q':
                    $docType[] = 'refman-query-browser';
                  break;

                case 'm':
                    $docType[] = 'refman-migration-toolkit';
                  break;

                case 'w':
                    $docType[] = 'refman-workbench';
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;

                case 'd':
                default:
                    $docType[] = 'documentation';
                  break;
            }
        }

        if(empty($docType))
            $docType[] = 'documentation';

        $docType = array_unique($docType);

        foreach($docType as $i => $type) {

            $url = 'http://search.mysql.com/search?q=' . $search .
                   '&site=' . $type;

            $out = \Hoa\Console\System::execute($browser . ' ' .
                       \Hoa\Console\System::escapeArgument($url));

            empty($out)
            and
            cout('Open ' . parent::stylize($url, 'info') . '.');
        }

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : documentation:mysql [-d] [-3] [-4] [-5] [-6] [-a] [-q] [-m] [-w] <search>');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'd'    => 'Search in all documentations.',
            '3'    => 'Search in the MySQL reference v3.23.',
            '4'    => 'Search in the MySQL reference v4.0 and 4.1.',
            '5'    => 'Search in the MySQL reference v5.0 and 5.1.',
            '6'    => 'Search in the MySQL reference v6.0.',
            'a'    => 'Search in the administrator reference.',
            'q'    => 'Search in the query browser reference.',
            'm'    => 'Search in the migration toolkit reference.',
            'w'    => 'Search in the workbench reference.',
            'help' => 'This help.'
        )));

        return HC_SUCCESS;
    }
}

}
