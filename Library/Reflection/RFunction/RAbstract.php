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
 * \Hoa\Reflection\Exception
 */
-> import('Reflection.Exception')

/**
 * \Hoa\Reflection\Wrapper
 */
-> import('Reflection.Wrapper')

/**
 * \Hoa\Reflection\RParameter
 */
-> import('Reflection.RParameter')

/**
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Reflection\RFunction {

/**
 * Class \Hoa\Reflection\RFunction\RAbstract.
 *
 * Extending ReflectionMethod and ReflectionFunction capacities.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

abstract class RAbstract
    extends    \Hoa\Reflection\Wrapper
    implements \Hoa\Visitor\Element {

    /**
     * Function file.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract string
     */
    protected $_file             = null;

    /**
     * Function comment content.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract string
     */
    protected $_comment          = null;

    /**
     * Whether the function returns a reference or not.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract bool
     */
    protected $_returnsReference = false;

    /**
     * Function name.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract string
     */
    protected $_name             = null;

    /**
     * Whether parameters were already transformed or not.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract bool
     */
    protected   $_firstP         = true;

    /**
     * All parameters.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract array
     */
    protected $_parameters       = array();

    /**
     * Function body.
     *
     * @var \Hoa\Reflection\RFunction\RAbstract string
     */
    protected $_body             = null;



    /**
     * Reflect a function or a method.
     *
     * @access  public
     * @param   object  $wrapped    Function or method reflection instance.
     * @return  void
     */
    public function __construct ( $wrapped ) {

        $this->setWrapped($wrapped);

        $comment = $wrapped->getDocComment();
        $comment = preg_replace('#^(\s*/\*\*\s*)#', '', $comment);
        $comment = preg_replace('#(\s*\*/)$#',      '', $comment);
        $comment = preg_replace('#^(\s*\*\s*)#m',   '', $comment);

        $this->setCommentContent($comment);
        $this->setReference($wrapped->returnsReference());
        $this->setName($wrapped->getName());

        return;
    }

    /**
     * Set comment content.
     *
     * @access  public
     * @param   string  $comment    Comment content.
     * @return  string
     */
    public function setCommentContent ( $comment ) {

        $old            = $this->_comment;
        $this->_comment = $comment;

        return $old;
    }

    /**
     * Get comment content.
     *
     * @access  public
     * @return  string
     */
    public function getCommentContent ( ) {

        return $this->_comment;
    }

    /**
     * Get comment (content + decoration).
     *
     * @access  public
     * @return  string
     */
    public function getComment ( ) {

        if(null === $comment = $this->getCommentContent())
            return null;

        return "\n" . '/**' . "\n" . ' * ' .
               str_replace("\n", "\n" . ' * ', $comment) .
               "\n" . ' */';
    }

    /**
     * Set whether the function returns a reference or not.
     *
     * @access  public
     * @param   bool    $reference    Whether the functions returns a reference
     *                                or not.
     * @return  bool
     */
    public function setReference ( $reference ) {

        $old                     = $this->_returnsReference;
        $this->_returnsReference = $reference;

        return $old;
    }

    /**
     * Get whether the function returns a reference or not.
     *
     * @access  public
     * @return  bool
     */
    public function getReference ( ) {

        return $this->_returnsReference;
    }

    /**
     * Override the function or method reflection method.
     *
     * @access  public
     * @return  bool
     */
    public function returnsReference ( ) {

        return $this->getReference();
    }

    /**
     * Set the function name.
     *
     * @access  public
     * @param   string  $name    Name.
     * @return  string
     */
    public function setName ( $name ) {

        $old         = $this->_name;
        $this->_name = $name;

        return $old;
    }

    /**
     * Get the function name.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Get all parameters.
     *
     * @access  public
     * @return  array
     */
    public function getParameters ( ) {

        if(false === $this->_firstP)
            return $this->_parameters;

        foreach($this->getWrapped()->getParameters() as $i => $parameter)
            $this->_parameters[] = new \Hoa\Reflection\RParameter($parameter);

        $this->_firstP = false;

        return $this->_parameters;
    }

    /**
     * Set the function body.
     *
     * @access  public
     * @param   string  $body    Body.
     * @return  string
     */
    public function setBody ( $body ) {

        $old         = $this->_body;
        $this->_body = $body;

        return $old;
    }

    /**
     * Get the function body.
     *
     * @access  public
     * @return  string
     */
    public function getBody ( ) {

        if(null !== $this->_body)
            return $this->_body;

        if(null === $this->_file)
            $this->_initializeFile();

        for($i = $this->getWrapped()->getStartLine(),
            $m = $this->getWrapped()->getEndLine();
            $i < $m;
            ++$i)
            $this->_body .= $this->_file[$i];

        return $this->_body = rtrim(trim($this->_body, "{}\n"));
    }

    /**
     * Set the file.
     * Do not use this method :-). It should be friend with
     * \Hoa\Reflection\RClass (as C++ meaning).
     *
     * @access  public
     * @return  void
     */
    public function _setFile ( &$file ) {

        $this->_file = &$file;

        return;
    }

    /**
     * Initialize the file.
     *
     * @access  public
     * @return  void
     */
    protected function _initializeFile ( ) {

        $this->_file = file($this->getWrapped()->getFileName());

        return;
    }

    /**
     * Import a fragment.
     *
     * @access  public
     * @return  void
     * @throw   \Hoa\Reflection\Exception
     */
    public function importFragment ( $fragment ) {

        if(   ($fragment instanceof \Hoa\Reflection\RParameter)
           || ($fragment instanceof \Hoa\Reflection\Fragment\RParameter))
            $this->_parameters[] = $fragment;
        else
            throw new \Hoa\Reflection\Exception(
                'Unknow fragment %s; cannot import it.',
                0, get_class($fragment));

        return;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
