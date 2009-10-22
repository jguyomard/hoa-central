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
 * @package     Hoa_Framework
 *
 */

/**
 * Hoa_Exception
 */
require_once 'Exception.php';

/**
 * Interface Hoa_Framework_Parameterizable.
 *
 * Interface for all classes or packages that are parameterizable.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Parameterizable
 */

interface   Hoa_Framework_Parameterizable
    extends Hoa_Framework_Parameterizable_Readable,
            Hoa_Framework_Parameterizable_Writable { }

/**
 * Interface Hoa_Framework_Parameterizable_Readable.
 *
 * Interface for all classes or packages which parameters are readable.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Parameterizable
 * @subpackage  Hoa_Framework_Parameterizable_Readable
 */

interface Hoa_Framework_Parameterizable_Readable {

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( );

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key );

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key );
}

/**
 * Interface Hoa_Framework_Parameterizable_Writable.
 *
 * Interface for all classes or packages which parameters are writable.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Parameterizable
 * @subpackage  Hoa_Framework_Parameterizable_Writable
 */

interface Hoa_Framework_Parameterizable_Writable {

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in );

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value );
}

/**
 * Class Hoa_Framework_Parameter.
 *
 * The parameter object, contains a set of parameter. It can be shared with
 * other class with permissions (read, write or both).
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP5
 * @version     0.1
 * @package     Hoa_Framework_Protocol
 */

class Hoa_Framework_Parameter {

    /**
     * Permission to read.
     *
     * @const int
     */
    const PERMISSION_READ  = 1;

    /**
     * Permission to write.
     *
     * @const int
     */
    const PERMISSION_WRITE = 2;

    /**
     * Permission to share.
     *
     * @const int
     */
    const PERMISSION_SHARE = 4;

    /**
     * Collection of package's parameters.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_parameters = array();

    /**
     * Collection of package's keywords.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_keywords   = array();

    /**
     * Parameters' owner.
     *
     * @var Hoa_Framework_Parameter string
     */
    private $_owner      = null;

    /**
     * Owner's friends with associated permissions.
     *
     * @var Hoa_Framework_Parameter array
     */
    private $_friends    = array();



    /**
     * Construct a new set of parameters.
     *
     * @access  public
     * @param   Hoa_Framework_Parameterizable  $owner         Owner.
     * @param   array                          $keywords      Keywords.
     * @param   array                          $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Hoa_Framework_Parameterizable $owner,
                                  Array $keywords   = array(),
                                  Array $parameters = array() ) {

        $this->_owner = get_class($owner);

        if(!empty($keywords))
            $this->setKeywords($owner, $keywords);

        if(!empty($parameters))
            $this->setDefaultParameters($owner, $parameters);

        return;
    }

    /**
     * Set default parameters to a class.
     *
     * @access  public
     * @param   object  $id            Owner or friends.
     * @param   array   $parameters    Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setDefaultParameters ( $id, Array $parameters ) {

        $this->check($id, self::PERMISSION_WRITE);

        $this->_parameters = $parameters;

        // Before assigning, check if a file does not exist. It has a higher
        // priority.

        return;
    }

    /**
     * Get default parameters from a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getDefaultParameters ( $id ) {

        return $this->getParameters($id);
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   object  $id      Owner or friends.
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( $id, Array $in ) {

        foreach($in as $key => $value)
            $this->setParameter($id, $key, $value);

        return;
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @param   object  $id      Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( $id ) {

        $this->check($id, self::PERMISSION_READ);

        return $this->_parameters;
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $id, $key, $value ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_parameters))
            $old = $this->_parameters[$key];

        $this->_parameters[$key] = $value;

        return $old;
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $id, $key ) {

        $parameters = $this->getParameters($id);

        if(array_key_exists($key, $parameters))
            return $parameters[$key];

        return null;
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $id, $key ) {

        $parameter = $this->getParameter($id, $key);

        if(null === $parameter)
            return null;

        return self::zFormat(
            $parameter,
            $this->getKeywords($id),
            $this->getParameters($id)
        );
    }

    /**
     * Unlinearize a branche to an array.
     *
     * @access  public
     * @param   object  $id         Owner of friends.
     * @param   string  $branche    Branche.
     * @return  array
     */
    public function unlinearizeBranche ( $id, $branche ) {

        $parameters = $this->getParameters($id);
        $keywords   = $this->getKeywords($id);
        $out        = array();
        $qBranche   = preg_quote($branche);

        foreach($parameters as $key => $value) {

            if(0 === preg_match('#^' . $qBranche . '(.*)?#', $key, $match))
                continue;

            $handle  = array();
            $explode = preg_split(
                '#((?<!\\\)\.)#',
                $match[1],
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            $end     = count($explode) - 1;
            $i       = $end;

            while($i >= 0) {

                $explode[$i] = str_replace('\\.', '.', $explode[$i]);

                if($i != $end)
                    $handle = array($explode[$i] => $handle);
                else
                    $handle = array($explode[$i] => self::zFormat(
                        $value,
                        $keywords,
                        $parameters
                    ));

                $i--;
            }

            $out = array_merge_recursive($out, $handle);
        }

        return $out;
    } 

    /**
     * Set many keywords to a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @param   array   $in    Keywords to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setKeywords ( $id, Array $in = array() ) {

        foreach($in as $key => $value)
            $this->setKeyword($id, $key, $value);

        return;
    }

    /**
     * Get many keywords from a class.
     *
     * @access  public
     * @param   object  $id    Owner or friends.
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getKeywords ( $id ) {

        $this->check($id, self::PERMISSION_READ);

        return $this->_keywords;
    }

    /**
     * Set a keyword to a class.
     *
     * @access  public
     * @param   object  $id       Owner or friends.
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setKeyword ( $id, $key, $value ) {

        $this->check($id, self::PERMISSION_WRITE);

        $old = null;

        if(true === array_key_exists($key, $this->_keywords))
            $old = $this->_keywords[$key];

        $this->_keywords[$key] = $value;

        return $old;
    }

    /**
     * Get a keyword from a class.
     *
     * @access  public
     * @param   object  $id         Owner or friends.
     * @param   string  $keyword    Keyword.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getKeyword ( $id, $keyword ) {

        $keywords = $this->getKeywords($id);

        if(true === array_key_exists($keyword, $keywords))
            return $keywords[$keyword];

        return null;
    }

    /**
     * ZFormat a string.
     * ZFormat is inspired from the famous Zsh (please, take a look at
     * http://zsh.org), and specifically from ZStyle.
     *
     * ZFormat has the following pattern:
     *     (:subject[:format]:)
     *
     * where subject could be a:
     *   * keyword, i.e. a simple string: foo;
     *   * reference to an existing parameter, i.e. a simple string prefixed by
     *     a %: %bar;
     *   * constant, i.e. a combination of chars, first is prefixed by a _: _Ymd
     *     will given the current year, followed by the current month and
     *     finally the current day.
     *
     * and where the format is a combination of chars, that apply functions on
     * the subject:
     *   * h: to get the head of a path (equivalent to dirname);
     *   * t: to get the tail of a path (equivalent to basename);
     *   * r: to get the path without extension;
     *   * e: to get the extension;
     *   * l: to get the result in lowercase;
     *   * u: to get the result in uppercase;
     *   * U: to get the result with the first letter in uppercase;
     *   * s/<foo>/<bar>/: to replace all matches <foo> by <bar> (the last / is
     *     optional, only if more options are given after);
     *   * s%<foo>%<bar>%: to replace the prefix <foo> by <bar> (the last % is
     *     also optional);
     *   * s#<foo>#<bar>#: to replace the suffix <foo> by <bar> (the last # is
     *     also optional).
     *
     * Known constants are:
     *   * d: day of the month, 2 digits with leading zeros;
     *   * j: day of the month without leading zeros;
     *   * N: ISO-8601 numeric representation of the day of the week;
     *   * w: numeric representation of the day of the week;
     *   * z: the day of the year (starting from 0);
     *   * W: ISO-8601 week number of year, weeks starting on Monday;
     *   * m: numeric representation of a month, with leading zeros;
     *   * n: numeric representation of a month, without leading zeros;
     *   * Y: a full numeric representation of a year, 4 digits;
     *   * y: a two digit representation of a year;
     *   * g: 12-hour format of an hour without leading zeros;
     *   * G: 24-hour format of an hour without leading zeros;
     *   * h: 12-hour format of an hour with leading zeros;
     *   * H: 24-hour format of an hour with leading zeros;
     *   * i: minutes with leading zeros;
     *   * s: seconds with leading zeros;
     *   * u: microseconds;
     *   * O: difference to Greenwich time (GMT) in hours;
     *   * T: timezone abbreviation;
     *   * U: seconds since the Unix Epoch (a timestamp).
     * There are very usefull for dynamic cache paths for example.
     *
     * Examples:
     *   Let keywords $k and parameters $p:
     *     $k = array(
     *         'foo'      => 'bar',
     *         'car'      => 'DeLoReAN',
     *         'power'    => 2.21,
     *         'answerTo' => 'life_universe_everything_else',
     *         'answerIs' => 42,
     *         'hello'    => 'wor.l.d'
     *     );
     *     $p = array(
     *         'plpl'        => '(:foo:U:)',
     *         'foo'         => 'ar(:%plpl:)',
     *         'favoriteCar' => 'A (:car:l:)!',
     *         'truth'       => 'To (:answerTo:ls/_/ /U:) is (:answerIs:).',
     *         'file'        => '/a/file/(:_Ymd:)/(:hello:trr:).(:power:e:)',
     *         'recursion'   => 'oof(:%foo:s#ar#az:)'
     *     );
     *   Then, after applying the zFormat, we get:
     *     * plpl:        'Bar', put the first letter in uppercase;
     *     * foo:         'arBar', call the parameter plpl;
     *     * favoriteCar: 'A delorean!', all is in lowercase;
     *     * truth:       'To Life universe everything else is 42', all is in
     *                    lowercase, then replace underscores by spaces, and
     *                    finally put the first letter in uppercase; and no
     *                    transformation for 42;
     *     * file:        '/a/file/20090505/wor.21', get date constants, then
     *                    get the tail of the path and remove extension twice,
     *                    and add the extension of power;
     *     * recursion:   'oofarbaz', get 'arbar' first, and then, replace the
     *                    suffix 'ar' by 'az'.
     *
     * @access  public
     * @param   string    $value         Parameter value.
     * @param   array     $keywords      Keywords.
     * @param   array     $parameters    Parameters.
     * @return  string
     * @throw   Hoa_Exception
     *
     * @todo
     *   Add the cast. Maybe like this: (:subject:format[:cast]:) where cast
     * could be integer, float, array etc.
     */
    public static function zFormat ( $value,
                                     Array $keywords   = array(),
                                     Array $parameters = array() ) {

        preg_match_all(
            '#([^\(]+)?(?:\(:(.*?):\))?#',
            $value,
            $matches,
            PREG_SET_ORDER
        );
        array_pop($matches);

        $out = null;

        foreach($matches as $i => $match) {

            $out .= $match[1];

            if(!isset($match[2]))
                continue;

            preg_match(
                '#([^:]+)(?::(.*))?#',
                $match[2],
                $submatch
            );

            if(!isset($submatch[1]))
                continue;

            $key    = $submatch[1];
            $word   = substr($key, 1);
            $handle = null;

            // Call a parameter.
            if($key[0] == '%') {

                if(false === array_key_exists($word, $parameters))
                    throw new Hoa_Exception(
                        'Parameter %s is not found in the parameter rule %s.',
                        0, array($word, $parameter));

                $newParameters = $parameters;
                unset($newParameters[$word]);

                $handle = self::zFormat(
                    $parameters[$word],
                    $keywords,
                    $newParameters
                );

                unset($newParameters);
            }
            // Call a constant (only date constants for now).
            elseif($key[0] == '_') {

                preg_match_all(
                    '#(d|j|N|w|z|W|m|n|Y|y|g|G|h|H|i|s|u|O|T|U)#',
                    $word,
                    $constants
                );

                if(!isset($constants[1]))
                    throw new Hoa_Exception(
                        'An invalid constant char is found in the parameter ' .
                        'rule %s.', 1, $parameter);

                $handle = date(implode('', $constants[1]));
            }
            // Call a keyword.
            else {

                if(false === array_key_exists($key, $keywords))
                    throw new Hoa_Exception(
                        'Keyword %s is not found in the parameter rule %s.', 2,
                        array($key, $parameter));

                $handle = $keywords[$key];
            }

            if(!isset($submatch[2])) {

                $out .= $handle;
                continue;
            }

            preg_match_all(
                '#(h|t|r|e|l|u|U|s(/|%|\#)(.*?)(?<!\\\)\2(.*?)(?:(?<!\\\)\2|$))#',
                $submatch[2],
                $flags
            );

            if(empty($flags))
                continue;

            foreach($flags[1] as $i => $flag)
                switch($flag) {

                    case 'h':
                        $handle = dirname($handle);
                      break;

                    case 't':
                        $handle = basename($handle);
                      break;

                    case 'r':
                        if(false !== $position = strrpos($handle, '.', 1))
                            $handle = substr($handle, 0, $position);
                      break;

                    case 'e':
                        if(false !== $position = strrpos($handle, '.', 1))
                            $handle = substr($handle, $position + 1);
                      break;

                    case 'l':
                        $handle = strtolower($handle);
                      break;

                    case 'u':
                        $handle = strtoupper($handle);
                      break;

                    case 'U':
                        $handle = ucfirst($handle);
                      break;

                    default:
                        if(!isset($flags[3]) && !isset($flags[4]))
                            throw new Hoa_Exception(
                                'Unrecognized format pattern in the parameter %s.',
                                0, $parameter);

                        if(isset($flags[3][1]) && isset($flags[3][1])) {

                            $l = $flags[3][1];
                            $r = $flags[4][1];
                        }
                        else {

                            $l = $flags[3][0];
                            $r = $flags[4][0];
                        }

                        $l     = preg_quote($l, '#');

                        switch($flags[2][0]) {

                            case '%':
                                $l  = '^' . $l;
                              break;

                            case '#':
                                $l .= '$';
                              break;
                        }

                        $handle = preg_replace('#' . $l . '#', $r, $handle);
                }

            $out .= $handle;
        }

        return $out;
    }

    /**
     * Check if an object has permissions to read or write into this set of
     * parameters.
     *
     * @access  public
     * @param   object  $id             Owner or friends.
     * @param   int     $permissions    Permissions (please, see the
     *                                  self::PERMISSION_* constants).
     * @return  bool
     * @throw   Hoa_Exception
     */
    public function check ( $id, $permissions ) {

        if(!(   $id instanceof Hoa_Framework_Parameterizable
             || $id instanceof Hoa_Framework_Parameterizable_Readable
             || $id instanceof Hoa_Framework_Parameterizable_Writable))
            throw new Hoa_Exception(
                'Class %s is not valid. ' .
                'Parameterizable classes must extend ' .
                'Hoa_Framework_Parameterizable, ' .
                'Hoa_Framework_Parameterizable_Readable or ' .
                'Hoa_Framework_Parameterizable_Writable interfaces.',
                0, $id);

        $iid = get_class($id);

        if($this->_owner == $iid)
            return true;

        if(!array_key_exists($iid, $this->_friends))
            throw new Hoa_Exception(
                'Class %s is not friend of %s and cannot share its parameters.',
                0, array($iid, $this->_owner));

        $p = $this->_friends[$iid];

        if(0 === ($permissions & $p))
            if(0 !== $permissions & self::PERMISSION_READ)
                throw new Hoa_Exception(
                    'Class %s does not have permission to read parameters ' .
                    'from %s.', 1, array($iid, $this->_owner));
            elseif(0 !== $permissions & self::PERMISSION_WRITE)
                throw new Hoa_Exception(
                    'Class %s does not have permission to write parameters ' .
                    'from %s.', 2, array($iid, $this->_owner));
            else
                throw new Hoa_Exception(
                    'Class %s does not have permission to share parameters ' .
                    'from %s.', 3, array($iid, $this->_owner));

        return true;
    }

    /**
     * Share this set of parameters of another class.
     * Only owner can share its set of parameters with someone else; it is more
     * simple like this… (because of changing permissions cascade effect).
     *
     * @access  public
     * @param   object  $owner          Owner or friend.
     * @param   object  $friend         Friend.
     * @param   int     $permissions    Permissions (please, see the
     *                                  self::PERMISSION_* constants).
     * @return  void
     * @throw   Hoa_Exception
     */
    public function shareWith ( $owner, $friend, $permissions ) {

        $this->check($owner, self::PERMISSION_SHARE);

        $this->_friends[get_class($friend)] = $permissions;

        return;
    }
}