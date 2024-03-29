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
 * \Hoa\Stream\Notification\Exception
 */
-> import('Stream.Notification.Exception')

/**
 * \Hoa\Stream\Context
 */
-> import('Stream.Context');

}

namespace Hoa\Stream\Notification {

/**
 * Class \Hoa\Stream\Notification.
 *
 * Manage stream notifications.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Notification extends \Hoa\Stream\Context {

    /**
     * A remove address required for this stream has been resolved, or the
     * resolution failed.
     *
     * @const int
     */
    const RESOLVE       = STREAM_NOTIFY_RESOLVE;

    /**
     * A connection with an external resource has been established.
     *
     * @const int
     */
    const CONNECT       = STREAM_NOTIFY_CONNECT;

    /**
     * Additional authorization is required to access the specified resource.
     *
     * @const int
     */
    const AUTH_REQUIRED = STREAM_NOTIFY_AUTH_REQUIRED;

    /**
     * The mime-type of resource has been identified.
     *
     * @const int
     */
    const MIME_TYPE_IS  = STREAM_NOTIFY_MIME_TYPE_IS;

    /**
     * The size of the resource has been discovered.
     *
     * @const int
     */
    const FILE_SIZE_IS  = STREAM_NOTIFY_FILE_SIZE_IS;

    /**
     * The external resource has redirected the stream to an alternate location.
     *
     * @const int
     */
    const REDIRECTED    = STREAM_NOTIFY_REDIRECTED;

    /**
     * Indicate current progress of the stream transfer.
     *
     * @const int
     */
    const PROGRESS      = STREAM_NOTIFY_PROGRESS;

    /**
     * There is no more data available on the stream.
     *
     * @const int
     */
    const COMPLETED     = STREAM_NOTIFY_COMPLETED;

    /**
     * A generic error occured on the stream.
     *
     * @const int
     */
    const FAILURE       = STREAM_NOTIFY_FAILURE;

    /**
     * Authorization has been completed (with or without success).
     *
     * @const int
     */
    const AUTH_RESULT   = STREAM_NOTIFY_AUTH_RESULT;

    /**
     * Notifiers list.
     *
     * @var \Hoa\Stream\Notification array
     */
    private $_notifiers = array();



    /**
     * Static inheritance is not very functional in PHP < 5.3. So rewrite the
     * getInstance() method here :-).
     *
     * @access  public
     * @param   string  $id         Singleton ID.
     * @param   string  $wrapper    Wrapper name (falcultative if just using
     *                              notification, not the context).
     * @return  \Hoa\Stream\Notification
     * @throws  \Hoa\Stream\Notification\Exception
     */
    public static function getInstance ( $id = null, $wrapper = null ) {

        if(null === parent::$_currentId && null === $id)
            throw new Exception(
                'Must precise a singleton index once.', 0);

        if(false === parent::contextExists($id))
            parent::$_instance[$id] = new self($wrapper);

        if(null !== $id)
            parent::$_currentId = $id;

        return parent::$_instance[$id];
    }

    /**
     * Create the stream context.
     *
     * @access  protected
     * @return  resource
     */
    protected function setContext ( ) {

        $old            = $this->_context;
        $this->_context = stream_context_create(
            parent::getContext(),
            array('notification' => array($this, 'callback'))
        );

        return $old;
    }

    /**
     * Set the wrapper value.
     *
     * @access  protected
     * @param   string     $wrapper    Wrapper name.
     * @return  string
     */
    protected function setWrapper ( $wrapper ) {

        $old            = $this->_wrapper;
        $this->_wrapper = strtolower($wrapper);
    }

    /**
     * Get the wrapper value.
     *
     * @access  public
     * @return  string
     * @throws  \Hoa\Stream\Notification\Exception
     */
    public function getWrapper ( ) {

        $out = parent::getWrapper();

        if(null === $out)
            throw new Exception(
                'Wrapper cannot be null. Please, precise a wrapper name if you
                want to use notification _and_ context.', 1);

        return $out;
    }

    /**
     * Register a notifier.
     *
     * @access  public
     * @param   \Hoa\Stream\Notification\Notifiable  $notifier    Notifier.
     * @return  \Hoa\Stream\Notification
     * @throw   \Hoa\Stream\Notification\Exception
     */
    public function register ( Notifiable $notifier ) {

        $index = get_class($notifier);

        if(true === self::isRegistered($index))
            throw new Exception(
                'Notification %s is already registered.', 2, $index);

        $this->_notifiers[$index] = $notifier;

        return $this;
    }

    /**
     * Unregister a notifier.
     *
     * @access  public
     * @param   mixed   $notifier    Notifier instance or name (i.e. classname).
     * @return  \Hoa\Stream\Notification
     * @throw   \Hoa\Stream\Notification\Exception
     */
    public function unregister ( $notifier ) {

        if($notifier instanceof Notifiable)
            $notifier = get_class($notifier);

        unset($this->_notifiers[$notifier]);

        return $this;
    }

    /**
     * Check if notifier is already registered or not.
     *
     * @access  public
     * @param   mixed   $notifier    Notifier instance or name (i.e. classname).
     * @return  bool
     */
    public function isRegistered ( $notifier ) {

        if($notifier instanceof Notifiable)
            $notifier = get_class($notifier);

        return isset($this->_notifiers[$notifier]);
    }

    /**
     * Callback notification method.
     *
     * @access  public
     * @param   int     $notifCode      One of the self::* constants.
     * @param   int     $severity       One of the self::SEVIRITY_* constants.
     * @param   string  $message        Passed if a descriptive message is
     *                                  available for the event.
     * @param   int     $code           Passed if a descriptive messsage code is
     *                                  available for the event. The meaning of
     *                                  this value is dependent on the specific
     *                                  wrapper in use.
     * @param   int     $transferred    If applicable, the transferred bytes
     *                                  number.
     * @param   int     $max            If applicable, the max bytes number.
     * @throw   \Hoa\Stream\Notification\Exception
     * @return  void
     */
    public function callback ( $notifCode, $severity,    $message,
                               $code,      $transferred, $max ) {

        switch($notifCode) {

            case self::RESOLVE:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->resolve($severity, $message, $code, $transferred, $max);
              break;

            case self::CONNECT:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->connect($severity, $message, $code, $transferred, $max);
              break;

            case self::AUTH_REQUIRED:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->authRequired($severity, $message, $code, $transferred, $max);
              break;

            case self::MIME_TYPE_IS:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->mimeTypeIs($severity, $message, $code, $transferred, $max);
              break;

            case self::SIZE_IS:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->sizeIs($severity, $message, $code, $transferred, $max);
              break;

            case self::REDIRECTED:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->redirected($severity, $message, $code, $transferred, $max);
              break;

            case self::PROGRESS:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->progress($severity, $message, $code, $transferred, $max);
              break;

            case self::COMPLETED:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->completed($severity, $message, $code, $transferred, $max);
              break;

            case self::FAILURE:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->failure($severity, $message, $code, $transferred, $max);
              break;

            case self::AUTH_RESULT:
                foreach($this->_notifiers as $i => $notifier)
                    $notifier->authResult($severity, $message, $code, $transferred, $max);
              break;

            default:
                throw new Exception(
                    'Unknown notification code : %d.', 3, $notifCode);
        }

        return;
    }
}

}
