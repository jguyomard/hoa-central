<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Core {

/**
 * Class \Hoa\Core\Protocol.
 *
 * Abstract class for all hoa://'s components.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class Protocol {

    /**
     * Overwrite components if already exists.
     *
     * @const bool
     */
    const OVERWRITE        = true;

    /**
     * Do not overwrite components if already exists.
     *
     * @const bool
     */
    const DO_NOT_OVERWRITE = false;

    /**
     * Component's name.
     *
     * @var \Hoa\Core\Protocol string
     */
    protected $_name       = null;

    /**
     * Path for the reach() method.
     *
     * @var \Hoa\Core\Protocol string
     */
    protected $_reach      = null;

    /**
     * Collections of sub-components.
     *
     * @var \Hoa\Core\Protocol array
     */
    private $_components   = array();

    /**
     * Cache of resolver.
     *
     * @var \Hoa\Core\Protocol array
     */
    private static $_cache = array();

    /**
     * Static indentation for the __toString() method.
     *
     * @var \Hoa\Core\Protocol int
     */
    private static $i      = 0;



    /**
     * Construct a protocol's component.
     * If it is not a data object (i.e. if it is not extend this class to overload
     * the $this->_name property), we can set the $this->_name property
     * dynamically. So usefull to create components on the fly…
     *
     * @access  public
     * @param   string  $name     Component's name.
     * @param   string  $reach    Path for the reach() method (usefull for the
     *                            helper).
     * @return  void
     */
    public function __construct ( $name = null, $reach = null ) {

        if(null !== $name)
            $this->_name = $name;

        if(null !== $reach)
            $this->_reach = $reach;

        return;
    }

    /**
     * Helper for adding component easily.
     *
     * @access  public
     * @param   string  $path         hoa:// path.
     * @param   string  $reach        Path for the reach() method.
     * @param   bool    $overwrite    Overwrite existing components (please, see
     *                                self::*_OVERWRITE constants).
     * @return  \Hoa\Core\Protocol
     */
    public function addComponentHelper ( $path, $reach,
                                         $overwrite = self::DO_NOT_OVERWRITE ) {

        $components = explode('/', $path);
        $current    = Core::getProtocol();
        $handle     = null;
        $max        = count($components) - 1;

        foreach($components as $i => $component) {

            if(    $current->componentExists($component)
               && (self::DO_NOT_OVERWRITE === $overwrite
               ||  $i != $max)) {

                $current = $current->getComponent($component);
                continue;
            }

            if($i != $max)
                $handle = new Protocol\Generic($component);
            else
                $handle = new Protocol\Generic($component, $reach);

            $current->addComponent($handle);
            $current = $handle;
        }

        return $this;
    }

    /**
     * Add a component.
     *
     * @access  public
     * @param   \Hoa\Core\Protocol  $component    Component to add.
     * @return  \Hoa\Core\Protocol
     * @throws  \Hoa\Core\Exception
     */
    public function addComponent ( Protocol $component ) {

        $name = $component->getName();

        if(empty($name))
            throw new Exception(
                'Cannot add a component to the protocol hoa:// without a name.', 0);

        $this->_components[$component->getName()] = $component;

        return $this;
    }

    /**
     * Remove a component.
     *
     * @access  public
     * @param   string  $component    Component name to remove.
     * @return  \Hoa\Core\Protocol
     */
    public function removeComponent ( $component ) {

        if(false === $this->componentExists($component))
            return $this;

        unset($this->_components[$component]);

        return $this;
    }

    /**
     * Get a specific component.
     *
     * @access  public
     * @param   string  $component    Component name.
     * @return  \Hoa\Core\Protocol
     * @throw   \Hoa\Core\Exception
     */
    public function getComponent ( $component ) {

        if(false === $this->componentExists($component))
            throw new Exception(
                'Component %s does not exist.', 1, $component);

        return $this->_components[$component];
    }

    /**
     * Check if a component exists.
     *
     * @access  public
     * @param   string  $component    Component name.
     * @return  bool
     */
    public function componentExists ( $component ) {

        return array_key_exists($component, $this->_components);
    }

    /**
     * Front method for resolving a path. Please, look the $this->_resolve()
     * method.
     *
     * @access  public
     * @param   string  $path    Path to resolve.
     * @return  mixed
     */
    public function resolve ( $path ) {

        if(substr($path, 0, 6) !== 'hoa://')
            return $path;

        if(isset(self::$_cache[$path]))
            return self::$_cache[$path];

        $handle              = $this->_resolve($path);
        self::$_cache[$path] = $handle;

        return $handle;
    }

    /**
     * Resolve a path, i.e. iterate the components tree and reach the queue of
     * the path.
     *
     * @access  public
     * @param   string  $path    Path to resolve.
     * @return  mixed
     */
    protected function _resolve ( $path ) {

        if(substr($path, 0, 6) == 'hoa://')
            $path = substr($path, 6);

        if(empty($path))
            return null;

        $path = trim($path, '/');
        $pos  = strpos($path, '/');

        if(false !== $pos)
            $next = substr($path, 0, $pos);
        else {

            $pos = strpos($path, '#');

            if(false !== $pos)
                $next = substr($path, 0, $pos);
            else
                $next = $path;
        }

        if(true === $this->componentExists($next)) {

            if(false === $pos)
                return $this->getComponent($next)->reach(null);

            $handle = substr($path, $pos + 1);

            if('#' == $path[$pos])
                return $this->getComponent($next)->reachId($handle);

            return $this->getComponent($next)->_resolve($handle);
        }

        return $this->reach($path);
    }

    /**
     * Queue of the component.
     * Generic one. Should be overload in childs classes.
     *
     * @access  public
     * @param   string  $queue    Queue of the component (generally, a filename,
     *                            with probably a query).
     * @return  mixed
     */
    public function reach ( $queue ) {

        return $this->_reach . $queue;
    }

    /**
     * ID of the component.
     * Generic one. Should be overload in childs classes.
     *
     * @access  public
     * @param   string  $id    ID of the component.
     * @return  mixed
     * @throw   \Hoa\Core\Exception
     */
    public function reachId ( $id ) {

        throw new Exception(
            'The component %s has no ID support (tried to reach #%s).',
            0, array($this->getName(), $id));
    }

    /**
     * Get component's name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get reach's root.
     *
     * @access  protected
     * @return  string
     */
    protected function getReach ( ) {

        return $this->_reach;
    }

    /**
     * Print a tree of component.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        $out = str_repeat('  ', self::$i) . $this->getName() . "\n";

        foreach($this->_components as $foo => $component) {

            self::$i++;
            $out .= $component->__toString();
            self::$i--;
        }

        return $out;
    }
}

}

namespace Hoa\Core\Protocol {

/**
 * Class \Hoa\Core\Protocol\Generic.
 *
 * hoa://'s protocol's generic component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Generic extends \Hoa\Core\Protocol { }

/**
 * Class \Hoa\Core\Protocol\Root.
 *
 * hoa://'s protocol's root.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Root extends \Hoa\Core\Protocol {

    /**
     * Component's name.
     *
     * @var \Hoa\Core\Protocol\Root string
     */
    protected $_name = 'hoa://';
}

/**
 * Class \Hoa\Core\Protocol\Wrapper.
 *
 * Wrapper for hoa://'s protocol.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Wrapper {

    /**
     * Opened stream.
     *
     * @var \Hoa\Core\Protocol\Wrapper resource
     */
    private $_stream     = null;

    /**
     * Stream name (filename).
     *
     * @var \Hoa\Core\Protocol\Wrapper string
     */
    private $_streamName = null;

    /**
     * Stream context (given by the streamWrapper class).
     *
     * @var \Hoa\Core\Protocol\Wrapper resource
     */
    public $context      = null;



    /**
     * Get the real path of the given URL.
     * Could return false if the path cannot be reached.
     *
     * @access  public
     * @param   string  $path    Path (or URL).
     * @return  mixed
     */
    public static function realPath ( $path ) {

        return \Hoa\Core::getProtocol()->resolve($path);
    }

    /**
     * Close a resource.
     * This method is called in response to fclose().
     * All resources that were locked, or allocated, by the wrapper should be
     * released.
     *
     * @access  public
     * @return  void
     */
    public function stream_close ( ) {

        if(true === @fclose($this->getStream())) {

            $this->_stream     = null;
            $this->_streamName = null;
        }

        return;
    }

    /**
     * Tests for end-of-file on a file pointer.
     * This method is called in response to feof().
     *
     * access   public
     * @return  bool
     */
    public function stream_eof ( ) {

        return feof($this->getStream());
    }

    /**
     * Flush the output.
     * This method is called in respond to fflush().
     * If we have cached data in our stream but not yet stored it into the
     * underlying storage, we should do so now.
     *
     * @access  public
     * @return  bool
     */
    public function stream_flush ( ) {

        return fflush($this->getStream());
    }

    /**
     * Advisory file locking.
     * This method is called in response to flock(), when file_put_contents()
     * (when flags contains LOCK_EX), stream_set_blocking() and when closing the
     * stream (LOCK_UN).
     *
     * @access  public
     * @param   int     $operation    Operation is one the following:
     *                                  * LOCK_SH to acquire a shared lock (reader) ;
     *                                  * LOCK_EX to acquire an exclusive lock (writer) ;
     *                                  * LOCK_UN to release a lock (shared or exclusive) ;
     *                                  * LOCK_NB if we don't want flock() to
     *                                    block while locking (not supported on
     *                                    Windows).
     * @return  bool
     */
    public function stream_lock ( $operation ) {

        return flock($this->getStream(), $operation);
    }

    /**
     * Open file or URL.
     * This method is called immediately after the wrapper is initialized (f.e.
     * by fopen() and file_get_contents()).
     *
     * @access  public
     * @param   string  $path           Specifies the URL that was passed to the
     *                                  original function.
     * @param   string  $mode           The mode used to open the file, as
     *                                  detailed for fopen().
     * @param   int     $options        Holds additional flags set by the
     *                                  streams API. It can hold one or more of
     *                                  the following values OR'd together:
     *                                    * STREAM_USE_PATH, if path is relative,
     *                                      search for the resource using the
     *                                      include_path;
     *                                    * STREAM_REPORT_ERRORS, if this is
     *                                    set, you are responsible for raising
     *                                    errors using trigger_error during
     *                                    opening the stream. If this is not
     *                                    set, you should not raise any errors.
     * @param   string  &$openedPath    If the $path is opened successfully, and
     *                                  STREAM_USE_PATH is set in $options,
     *                                  $openedPath should be set to the full
     *                                  path of the file/resource that was
     *                                  actually opened.
     * @return  bool
     */
    public function stream_open ( $path, $mode, $options, &$openedPath ) {

        $p = self::realPath($path);

        if(false === $p)
            return false;

        if(null === $this->context)
            $openedPath = fopen(
                $p,
                $mode,
                $options & STREAM_USE_PATH
            );
        else
            $openedPath = fopen(
                $p,
                $mode,
                $options & STREAM_USE_PATH,
                $this->context
            );

        $this->_stream     = $openedPath;
        $this->_streamName = $p;

        return true;
    }

    /**
     * Read from stream. 
     * This method is called in response to fread() and fgets().
     *
     * @access  public
     * @param   int     $count    How many bytes of data from the current
     *                            position should be returned.
     * @return  string
     */
    public function stream_read ( $count ) {

        return fread($this->getStream(), $count);
    }

    /**
     * Seek to specific location in a stream.
     * This method is called in response to fseek().
     * The read/write position of the stream should be updated according to the
     * $offset and $whence.
     *
     * @access  public
     * @param   int     $offset    The stream offset to seek to.
     * @param   int     $whence    Possible values:
     *                               * SEEK_SET to set position equal to $offset
     *                                 bytes ;
     *                               * SEEK_CUR to set position to current
     *                                 location plus $offsete ;
     *                               * SEEK_END to set position to end-of-file
     *                                 plus $offset.
     * @return  bool
     */
    public function stream_seek ( $offset, $whence = SEEK_SET ) {

        return fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Retrieve information about a file resource.
     * This method is called in response to fstat().
     *
     * @access  public
     * @return  array
     */
    public function stream_stat ( ) {

        return fstat($this->getStream());
    }

    /**
     * Retrieve the current position of a stream.
     * This method is called in response to ftell().
     *
     * @access  public
     * @return  int
     */
    public function stream_tell ( ) {

        return ftell($this->getStream());
    }

    /**
     * Truncate a stream to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function stream_truncate ( $size ) {

        return ftruncate($this->getStream(), $size);
    }

    /**
     * Write to stream.
     * This method is called in response to fwrite().
     *
     * @access  public
     * @param   string  $data    Should be stored into the underlying stream.
     * @return  int
     */
    public function stream_write ( $data ) {

        return fwrite($this->getStream(), $data);
    }

    /**
     * Close directory handle.
     * This method is called in to closedir().
     * Any resources which were locked, or allocated, during opening and use of
     * the directory stream should be released.
     *
     * @access  public
     * @return  bool
     */
    public function dir_closedir ( ) {

        if(true === $handle = @closedir($this->getStream())) {

            $this->_stream     = null;
            $this->_streamName = null;
        }

        return $handle;
    }

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * @access  public
     * @param   string  $path       Specifies the URL that was passed to opendir().
     * @param   int     $options    Whether or not to enforce safe_mode (0x04).
     *                              It is not used here.
     * @return  bool
     */
    public function dir_opendir ( $path, $options ) {

        $p      = self::realPath($path);
        $handle = null;

        if(null === $this->context)
            $handle = @opendir($p);
        else
            $handle = @opendir($p, $this->context);

        if(false === $handle)
            return false;

        $this->_stream     = $handle;
        $this->_streamName = $p;

        return true;
    }

    /**
     * Read entry from directory handle.
     * This method is called in response to readdir().
     *
     * @access  public
     * @return  mixed
     */
    public function dir_readdir ( ) {

        return readdir($this->getStream());
    }

    /**
     * Rewind directory handle.
     * This method is called in response to rewinddir().
     * Should reset the output generated by self::dir_readdir, i.e. the next
     * call to self::dir_readdir should return the first entry in the location
     * returned by self::dir_opendir.
     *
     * @access  public
     * @return  bool
     */
    public function dir_rewinddir ( ) {

        return rewinddir($this->getStream());
    }

    /**
     * Create a directory.
     * This method is called in response to mkdir().
     *
     * @access  public
     * @param   string  $path       Directory which should be created.
     * @param   int     $mode       The value passed to mkdir().
     * @param   int     $options    A bitwise mask of values.
     * @return  bool
     */
    public function mkdir ( $path, $mode, $options ) {

        if(null === $this->context)
            return mkdir(
                self::realPath($path),
                $mode,
                $options | STREAM_MKDIR_RECURSIVE
            );

        return mkdir(
            self::realPath($path),
            $mode,
            $options | STREAM_MKDIR_RECURSIVE,
            $this->context
        );
    }

    /**
     * Rename a file or directory.
     * This method is called in response to rename().
     * Should attempt to rename $from to $to.
     *
     * @access  public
     * @param   string  $from    The URL to current file.
     * @param   string  $to      The URL which $from should be renamed to.
     * @return  bool
     */
    public function rename ( $from, $to ) {

        if(null === $this->context)
            return rename(self::realPath($from), self::realPath($to));

        return rename(self::realPath($from), self::realPath($to), $this->context);
    }

    /**
     * Remove a directory.
     * This method is called in response to rmdir().
     *
     * @access  public
     * @param   string  $path       The directory URL which should be removed.
     * @param   int     $options    A bitwise mask of values. It is not used
     *                              here.
     * @return  bool
     */
    public function rmdir ( $path, $options ) {

        if(null === $this->context)
            return rmdir(self::realPath($path));

        return rmdir(self::realPath($path), $this->context);
    }

    /**
     * Delete a file.
     * This method is called in response to unlink().
     *
     * @access  public
     * @param   string  $path    The file URL which should be deleted.
     * @return  bool
     */
    public function unlink ( $path ) {

        if(null === $this->context)
            return unlink(self::realPath($path));

        return unlink(self::realPath($path), $this->context);
    }

    /**
     * Retrieve information about a file.
     * This method is called in response to all stat() related functions.
     *
     * @access  public
     * @param   string  $path     The file URL which should be retrieve
     *                            information about.
     * @param   int     $flags    Holds additional flags set by the streams API.
     *                            It can hold one or more of the following
     *                            values OR'd together.
     *                            STREAM_URL_STAT_LINK: for resource with the
     *                            ability to link to other resource (such as an
     *                            HTTP location: forward, or a filesystem
     *                            symlink). This flag specified that only
     *                            information about the link itself should be
     *                            returned, not the resource pointed to by the
     *                            link. This flag is set in response to calls to
     *                            lstat(), is_link(), or filetype().
     *                            STREAM_URL_STAT_QUIET: if this flag is set,
     *                            our wrapper should not raise any errors. If
     *                            this flag is not set, we are responsible for
     *                            reporting errors using the trigger_error()
     *                            function during stating of the path.
     * @return  array
     */
    public function url_stat ( $path, $flags ) {

        if(false === $p = self::realPath($path))
            if($flags & STREAM_URL_STAT_QUIET)
                return array(); // Not sure…
            else
                return trigger_error(
                    'Path ' . $path . ' cannot be resolved.',
                    E_WARNING
                );

        if($flags & STREAM_URL_STAT_LINK)
            return @lstat($p);

        return @stat($p);
    }

    /**
     * Get stream resource.
     *
     * @access  protected
     * @return  resource
     */
    protected function getStream ( ) {

        return $this->_stream;
    }

    /**
     * Get stream name.
     *
     * @access  protected
     * @return  resource
     */
    protected function getStreamName ( ) {

        return $this->_streamName;
    }
}

}

namespace {

/**
 * Alias of the \Hoa\Core::getInstance()->getProtocol()->resolve() method.
 * method.
 *
 * @access  public
 * @param   string  $path    Path to resolve.
 * @return  mixed
 */
if(!ƒ('resolve')) {
function resolve ( $path ) {

    return \Hoa\Core::getInstance()->getProtocol()->resolve($path);
}}

/**
 * Register the hoa:// protocol.
 */
stream_wrapper_register('hoa', '\Hoa\Core\Protocol\Wrapper');

}
