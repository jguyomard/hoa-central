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

namespace {

from('Hoa')

/**
 * \Hoa\Http\Exception
 */
-> import('Http.Exception.~')

/**
 * \Hoa\Http\Exception\CrossBufferization
 */
-> import('Http.Exception.CrossBufferization')

/**
 * \Hoa\Http
 */
-> import('Http.~')

/**
 * \Hoa\Stream\IStream\Out
 */
-> import('Stream.I~.Out')

/**
 * \Hoa\Stream\IStream\Bufferable
 */
-> import('Stream.I~.Bufferable');

}

namespace Hoa\Http\Response {

/**
 * Class \Hoa\Http\Response.
 *
 * HTTP response support.
 *
 * @TODO Follow http://tools.ietf.org/html/draft-nottingham-http-new-status-03.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class          Response
    extends    \Hoa\Http
    implements \Hoa\Stream\IStream\Out,
               \Hoa\Stream\IStream\Bufferable {

    /**
     * Continue.
     *
     * @const string
     */
    const STATUS_CONTINUE                        = '100 Continue';

    /**
     * Switching protocols.
     *
     * @const string
     */
    const STATUS_SWITCHING_PROTOCOLS             = '101 Switching Protocols';

    /**
     * Checkpoint.
     *
     * @const string
     */
    const STATUS_CHECKPOINT                      = '103 Checkpoint';

    /**
     * OK.
     *
     * @const string
     */
    const STATUS_OK                              = '200 Ok';

    /**
     * Created.
     *
     * @const string
     */
    const STATUS_CREATED                         = '201 Created';

    /**
     * Accepted.
     *
     * @const string
     */
    const STATUS_ACCEPTED                        = '202 Accepted';

    /**
     * Non-authoritative information.
     *
     * @const string
     */
    const STATUS_NON_AUTHORITATIVE_INFORMATION   = '203 Non Authoritative Information';

    /**
     * No content.
     *
     * @const string
     */
    const STATUS_NO_CONTENT                      = '204 No Content';

    /**
     * Reset content.
     *
     * @const string
     */
    const STATUS_RESET_CONTENT                   = '205 Reset Content';

    /**
     * Partial content.
     *
     * @const string
     */
    const STATUS_PARTIAL_CONTENT                 = '206 Partial Content';

    /**
     * IM used (please, see RFC 226).
     *
     * @const string
     */
    //const STATUS_IM_USED                       = '226 IM Used';

    /**
     * Multiple choices.
     *
     * @const string
     */
    const STATUS_MULTIPLE_CHOICES                = '300 Multiple Choices';

    /**
     * Moved permanently.
     *
     * @const string
     */
    const STATUS_MOVED_PERMANENTLY               = '301 Moved Permanently';

    /**
     * Found.
     *
     * @const string
     */
    const STATUS_FOUND                           = '302 Found';

    /**
     * See other.
     *
     * @const string
     */
    const STATUS_SEE_OTHER                       = '303 See Other';

    /**
     * Not modified.
     *
     * @const string
     */
    const STATUS_NOT_MODIFIED                    = '304 Not Modified';

    /**
     * Use proxy.
     *
     * @const string
     */
    const STATUS_USE_PROXY                       = '305 Use Proxy';

    /**
     * Switch proxy.
     *
     * @const string
     */
    const STATUS_SWITCHING_PROXY                 = '306 Switching Proxy';

    /**
     * Temporary redirect.
     *
     * @const string
     */
    const STATUS_TEMPORARY_REDIRECT              = '307 Temporary Redirect';

    /**
     * Resume incomplete.
     *
     * @const string
     */
    const STATUS_RESUME_INCOMPLETE               = '308 Resume Incomplete';

    /**
     * Bad request.
     *
     * @const string
     */
    const STATUS_BAD_REQUEST                     = '400 Bad Request';

    /**
     * Unauthorized.
     *
     * @const string
     */
    const STATUS_UNAUTHORIZED                    = '401 Unauthorized';

    /**
     * Payment required.
     *
     * @const string
     */
    const STATUS_PAYMENT_REQUIRED                = '402 Payment Required';

    /**
     * Forbidden.
     *
     * @const string
     */
    const STATUS_FORBIDDEN                       = '403 Forbidden';

    /**
     * Not found.
     *
     * @const string
     */
    const STATUS_NOT_FOUND                       = '404 Not Found';

    /**
     * Method not allowed.
     *
     * @const string
     */
    const STATUS_METHOD_NOT_ALLOWED              = '405 Method Not Allowed';

    /**
     * Not acceptable.
     *
     * @const string
     */
    const STATUS_NOT_ACCEPTABLE                  = '406 Not Acceptable';

    /**
     * Proxy authentification required.
     *
     * @const string
     */
    const STATUS_PROXY_AUTHENTIFICATION_REQUIRED = '407 Proxy Authentification Required';

    /**
     * Request time-out.
     *
     * @const string
     */
    const STATUS_REQUEST_TIME_OUT                = '408 Request Time Out';

    /**
     * Conflict.
     *
     * @const string
     */
    const STATUS_CONFLICT                        = '409 Conflict';

    /**
     * Gone.
     *
     * @const string
     */
    const STATUS_GONE                            = '410 Gone';

    /**
     * Length required.
     *
     * @const string
     */
    const STATUS_LENGTH_REQUIRED                 = '411 Length Required';

    /**
     * Precondition failed.
     *
     * @const string
     */
    const STATUS_PRECONDITION_FAILED             = '412 PreCondition Failed';

    /**
     * Request entity too large.
     *
     * @const string
     */
    const STATUS_REQUEST_ENTITY_TOO_LARGE        = '413 Request Entity Too Large';

    /**
     * Request URI too large.
     *
     * @const string
     */
    const STATUS_REQUEST_URI_TOO_LARGE           = '414 Request URI Too Large';

    /**
     * Unsupported media type.
     *
     * @const string
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE          = '415 Unsupported Media Type';

    /**
     * Requested range not satisfiable.
     *
     * @const string
     */
    const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = '416 Requested Range Not Satisfiable';

    /**
     * Expectation failed.
     *
     * @const string
     */
    const STATUS_EXPECTATION_FAILED              = '417 Expectation Failed';

    /**
     * I'm a teapot (see RFC 2324, April Fool's jok'e).'
     *
     * @const string
     */
    const STATUS_IM_A_TEAPOT                     = '418 I\'m a Teapot';

    /**
     * Upgrade required (RFC 2817).
     *
     * @const string
     */
    const STATUS_UPGRADE_REQUIRED                = '426 Upgrade Required';

    /**
     * Internal server error.
     *
     * @const string
     */
    const STATUS_INTERNAL_SERVER_ERROR           = '500 Internal Server Error';

    /**
     * Not implemented.
     *
     * @const string
     */
    const STATUS_NOT_IMPLEMENTED                 = '501 Not Implemented';

    /**
     * Bad gateway.
     *
     * @const string
     */
    const STATUS_BAD_GATEWAY                     = '502 Bad Gateway';

    /**
     * Service unavailable.
     *
     * @const string
     */
    const STATUS_SERVICE_UNAVAILABLE             = '503 Service Unavailable';

    /**
     * Gateway time-out.
     *
     * @const string
     */
    const STATUS_GATEWAY_TIME_OUT                = '504 Gateway Time Out';

    /**
     * HTTP version not supported.
     *
     * @const string
     */
    const STATUS_HTTP_VERSION_NOT_SUPPORTED      = '505 HTTP Version Not Supported';

    /**
     * Status (different ordering).
     *
     * @var \Hoa\Http\Response arra
     */
    private $_status                             = array();

    /**
     * Request HTTP version.
     *
     * @var \Hoa\Http\Request float
     */
    protected $_httpVersion                      = null;

    /**
     * Response body.
     *
     * @var \Hoa\Http\Request string
     */
    protected $_body                             = null;

    /**
     * This object hash.
     *
     * @var \Hoa\Http\Response string
     */
    private $_hash                               = null;

    /**
     * ob_*() is stateless, so we manage a stack to avoid cross-buffers
     * manipulations.
     *
     * @var \Hoa\Http\Response array
     */
    private static $_stack                       = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   bool   $newBuffer    Whether we run $this->newBuffer().
     *                               Following arguments are for this method.
     * @param   mixed  $call         First callable part.
     * @param   mixed  $able         Second callable part (if needed).
     * @param   int    $size         Size.
     * @return  void
     */
    public function __construct ( $newBuffer = true, $call = null, $able = '',
                                  $size      = null ) {

        parent::__construct();
        $this->_hash = spl_object_hash($this);

        if(true === $newBuffer)
            $this->newBuffer($call, $able, $size);

        if(empty($this->_status)) {

            $reflection = new \ReflectionClass($this);

            foreach($reflection->getConstants() as $value)
                $this->_status[$this->getStatus($value)] = $value;
        }

        return;
    }

    /**
     * Parse a HTTP packet.
     *
     * @access  public
     * @param   string  $packet    HTTP packet.
     * @return  void
     */
    public function parse ( $packet ) {

        $headers     = explode("\r\n", $packet);
        $status      = array_shift($headers);
        $this->_body = null;

        foreach($headers as $i => $header)
            if('' == trim($header)) {

                unset($headers[$i]);
                $this->_body = trim(
                    implode("\r\n", array_splice($headers, $i))
                );
                break;
            }

        if(0 === preg_match('#^HTTP/(1\.(?:0|1))\s+(\d{3})#i', $status, $matches))
            throw new Exception(
                'HTTP headers are not well-formed: %s.', 0, $status);

        if(!isset($this->_status[$matches[2]]))
            throw new Exception(
                'Unknow HTTP status %d in %s.', 1, array($matches[2], $status));

        $this->_httpVersion = (float) $matches[1];
        $this->_parse($headers);
        $this['status']     = $this->_status[$matches[2]];

        return;
    }

    /**
     * Get real status from static::STATUS_* constants.
     *
     * @access  public
     * @return  int
     */
    public static function getStatus ( $status ) {

        return (int) substr($status, 0, 3);
    }

    /**
     * Add a new header.
     *
     * @access  public
     * @return  void
     */
    public function sendHeader ( $header, $value, $replace = true,
                                 $status = null ) {

        if(   0    === strcasecmp('status', $header)
           && true === self::$_fcgi) {

            header(
                'HTTP/1.1 ' . $value,
                $replace,
                static::getStatus($value)
            );

            return;
        }

        header(
            $header . ': ' . $value,
            $replace,
            null !== $status ? static::getStatus($status) : null
        );

        return;
    }

    /**
     * Send all headers.
     *
     * @access  public
     * @return  void
     */
    public function sendHeaders ( ) {

        foreach($this->_headers as $header => $value)
            $this->sendHeader($header, $value);

        return;
    }

    /**
     * Get send headers.
     *
     * @access  public
     * @return  void
     */
    public function getSentHeaders ( ) {

        return implode("\r\n", headers_list());
    }

    /**
     * Start a new buffer.
     * The callable acts like a filter.
     *
     * @access  public
     * @param   mixed  $call    First callable part.
     * @param   mixed  $able    Second callable part (if needed).
     * @param   int    $size    Size.
     * @return  int
     */
    public function newBuffer ( $call = null, $able = '',  $size = null ) {

        $callable = null;

        if(null !== $call)
            $callable = xcallable($call, $able);

        $last = current(self::$_stack);
        $hash = $this->getHash();

        if(false === $last || $hash != $last[0])
            self::$_stack[] = array(
                0 => $hash,
                1 => 1
            );
        else
            ++self::$_stack[key(self::$_stack)][1];

        end(self::$_stack);
        ob_start($callable, (int) (bool) $size);

        return $this->getBufferLevel();
    }

    /**
     * Flush the buffer.
     *
     * @access  public
     * @return  void
     */
    public function flush ( ) {

        if(0 >= $this->getBufferSize())
            return;

        ob_flush();

        return;
    }

    /**
     * Delete buffer.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Http\Exception\CrossBufferization
     */
    public function deleteBuffer ( ) {

        $key = key(self::$_stack);

        if($this->getHash() != self::$_stack[$key][0])
            throw new \Hoa\Http\Exception\CrossBufferization(
                'Cannot delete this buffer because it was not opened by this ' .
                'class (%s, %s).',
                1, array(get_class($this), $this->getHash()));

        $out = ob_end_clean();

        if(false === $out)
            return false;

        --self::$_stack[$key][1];

        if(0 >= self::$_stack[$key][1])
            unset(self::$_stack[$key]);

        return true;
    }

    /**
     * Get buffer level.
     *
     * @access  public
     * @return  int
     */
    public function getBufferLevel ( ) {

        return ob_get_level();
    }

    /**
     * Get buffer size.
     *
     * @access  public
     * @return  int
     */
    public function getBufferSize ( ) {

        return ob_get_length();
    }

    /**
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @return  mixed
     * @throw   \Hoa\Http\Exception
     */
    public function write ( $string, $length ) {

        if(0 > $length)
            throw new \Hoa\Http\Exception(
                'Length must be greather than or equal to 0, given %d.',
                0, $length);

        if(strlen($string) > $length)
            $string = substr($string, 0, $length);

        echo $string;
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeString ( $string ) {

        echo (string) $string;
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $character    Character.
     * @return  mixed
     */
    public function writeCharacter ( $character ) {

        echo $character[0];
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool    $boolean    Boolean.
     * @return  mixed
     */
    public function writeBoolean ( $boolean ) {

        echo (string) (bool) $boolean;
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int     $integer    Integer.
     * @return  mixed
     */
    public function writeInteger ( $integer ) {

        echo (string) (int) $integer;
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @return  mixed
     */
    public function writeFloat ( $float ) {

        echo (string) (float) $float;
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @return  mixed
     */
    public function writeArray ( Array $array ) {

        echo var_export($array, true);
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  mixed
     */
    public function writeLine ( $line ) {

        if(false !== $n = strpos($line, "\n"))
            $line = substr($line, 0, $n + 1);

        echo $line;
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @return  mixed
     */
    public function writeAll ( $string ) {

        echo $string;
    }

    /**
     * Truncate a file to a given length.
     *
     * @access  public
     * @param   int     $size    Size.
     * @return  bool
     */
    public function truncate ( $size ) {

        if(0 === $size) {

            ob_clean();

            return true;
        }

        $bSize = $this->getBufferSize();

        if($size >= $bSize)
            return true;

        echo substr(ob_get_clean(), 0, $size);

        return true;
    }

    /**
     * Get this object hash.
     *
     * @access  public
     * @return  string
     */
    public function getHash ( ) {

        return $this->_hash;
    }

    /**
     * Set response HTTP version.
     *
     * @access  public
     * @param   float  $version    HTTP version.
     * @return  float
     */
    public function setHttpVersion ( $version ) {

        $old                = $this->_httpVersion;
        $this->_httpVersion = $version;

        return $old;
    }

    /**
     * Get response HTTP version.
     *
     * @access  public
     * @return  float
     */
    public function getHttpVersion ( ) {

        return $this->_httpVersion;
    }

    /**
     * Set response body.
     *
     * @access  public
     * @param   string  $body   Body.
     * @return  string
     */
    public function setBody ( $body ) {

        $old         = $this->_body;
        $this->_body = $body;

        return $old;
    }

    /**
     * Get response body.
     *
     * @access  public
     * @return  string
     */
    public function getBody ( ) {

        return $this->_body;
    }

    /**
     * Delete head buffer.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        $hash = $this->getHash();
        $last = current(self::$_stack);

        if($this->getHash() != $last[0])
            return;

        for($i = 0, $max = $last[1]; $i < $max; ++$i) {

            $this->flush();

            if(0 < $this->getBufferLevel())
                $this->deleteBuffer();
        }

        return;
    }
}

}
