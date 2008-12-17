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
 * @category    Data
 *
 */

/**
 * Hoa_Version
 */
import('Version.~');

/**
 * Hoa_File
 */
import('File.~');

/**
 * Hoa_File_Util
 */
import('File.Util');

/**
 * Hoa_Configuration_Ini
 */
import('Configuration.Ini');

/**
 * Hoa_Configuration_Xml
 */
import('Configuration.Xml');

/**
 * Hoa_Configuration_Yaml
 */
import('Configuration.Yaml');

/**
 * Class CacheCommand.
 *
 * This command allow to know version and revision of the framework.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 */

class CacheCommand extends Hoa_Console_Command_Abstract {

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
    protected $programName = 'Cache';

    /**
     * Options description.
     *
     * @var VersionCommand array
     */
    protected $options     = array(
        array('xml',  parent::NO_ARGUMENT, 'x'),
        array('yaml', parent::NO_ARGUMENT, 'y'),
        array('yml',  parent::NO_ARGUMENT, 'y'),
        array('ini',  parent::NO_ARGUMENT, 'i'),
        array('php',  parent::NO_ARGUMENT, 'p'),
        array('help', parent::NO_ARGUMENT, 'h'),
        array('help', parent::NO_ARGUMENT, '?')
    );

    /**
     * Configuration type to cache.
     *
     * @var VersionCommand array
     */
    protected $type        = array();

    /**
     * All files to cache.
     *
     * @var VersionCommand array
     */
    protected $file        = array();



    /**
     * The entry method.
     *
     * @access  public
     * @return  int
     */
    public function main ( ) {

        while(false !== $c = parent::getOption($v)) {

            switch($c) {

                case 'x':
                    $this->addType('xml');
                  break;

                case 'y':
                    $this->addType('yml');
                    $this->addType('yaml');
                  break;

                case 'i':
                    $this->addType('ini');
                  break;

                case 'p':
                    $this->addType('php');
                  break;

                case 'h':
                case '?':
                    return $this->usage();
                  break;
            }
        }

        $this->scan();
        $this->cache();

        return HC_SUCCESS;
    }

    /**
     * The command usage.
     *
     * @access  public
     * @return  int
     */
    public function usage ( ) {

        cout('Usage   : maintenance:cache [-x] [-y] [-i] [-p]');
        cout('Options :');
        cout(parent::makeUsageOptionsList(array(
            'x'    => 'Generate the cache of XML configuration.',
            'y'    => 'Generate the cache of YAML configuration.',
            'i'    => 'Generate the cache of PHP configuration.',
            'p'    => 'Generate the cache of INI configuration.',
            'help' => 'This help.'
        )));
        cout(
            'If file is ommitted, it will be replaced by “*.<type>”, ' .
            'where <type> is given by -x, -y, -i, or -p, or * by default.'
        );
        cout();

        return HC_SUCCESS;
    }

    /**
     * Add a configuration type to cache.
     *
     * @access  protected
     * @param   string     $type    The configuration type.
     * @return  array
     */
    protected function addType ( $type ) {

        if(null === $type || isset($this->type[$type]))
            return;

        $this->type[$type] = true;

        return $this->type;
    }

    /**
     * Scan all files to cache.
     *
     * @access  protected
     * @return  void
     */
    protected function scan ( ) {

        foreach($this->type as $type => $foo)
            $scan[] = '*.' . $type;

        if(empty($scan))
            $scan[] = '*.*';

        foreach($scan as $foo => $value)
            $this->file += glob(HOA_DATA_CONFIGURATION . DS . $value);
    }

    /**
     * Cache files.
     *
     * @access  protected
     * @return  void
     */
    protected function cache ( ) {

        if(empty($this->file)) {

            cout(parent::stylize('No configuration to cache', 'error'));
            return;
        }

        $cache = HOA_DATA_CONFIGURATION_CACHE;

        cout('The cache configuration path is ' . $cache . '.');
        cout();

        foreach($this->file as $i => $file) {

            $array = null;

            switch(Hoa_File_Util::getExt($file)) {

                case 'ini':
                  break;

                case 'php':
                  break;

                case 'xml':
                  break;

                case 'yml':
                case 'yaml':
                    $yml   = new Hoa_Configuration_Yaml(
                                 $file, 0,
                                 Hoa_Configuration::CONVERT_TO_ARRAY
                             );
                    $array = $yml->get();
                  break;
            }

            $filename = substr($file, strrpos($file, '/') + 1);

            parent::status(
                'Cache file ' . parent::stylize($filename, 'info') . '.',
                false !== Hoa_File::write(
                    $cache . DS . Hoa_File_Util::skipExt($filename) . '.php',
                    '<?php ' . "\n\n" .
                    '/**' . "\n" .
                    ' * Generated the ' . date('Y-m-d\TH:i:s.000000\Z', time()) . ".\n" .
                    ' */' . "\n\n" .
                    'return ' . var_export($array, true) . ';',
                    Hoa_File::MODE_WRITE
                )
            );
        }
    }
}
