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
 * \Hoa\Xml\Exception
 */
-> import('Xml.Exception.~')

/**
 * \Hoa\Xml\Element
 */
-> import('Xml.Element.~')

/**
 * \Hoa\Xml\Element\Model\Phrasing
 */
-> import('Xml.Element.Model.Phrasing');

}

namespace Hoa\Xml\Element {

/**
 * Class \Hoa\Xml\Element\Concrete.
 *
 * This class represents a XML element in a XML tree.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Concrete implements Element, \Countable, \IteratorAggregate, \ArrayAccess {

    /**
     * Store all elements of the abstract tree.
     *
     * @var \Hoa\Xml\Element\Concrete array
     */
    protected static $_store      = array();

    /**
     * Super roots of each abstract elements.
     *
     * @var \Hoa\Xml\Element\Concrete array
     */
    protected static $_superRoots = array();

    /**
     * Instances of conrete elements of each abstract element.
     *
     * @var \Hoa\Xml\Element\Concrete array
     */
    protected static $_multiton   = array();

    /**
     * Name of the concrete element.
     *
     * @var \Hoa\Xml\Element\Concrete string
     */
    protected $_name              = null;

    /**
     * Concrete children.
     *
     * @var \Hoa\Xml\Element\Concrete array
     */
    protected $_children          = array();

    /**
     * Concrete children for the iterator.
     *
     * @var \Hoa\Xml\Element\Concrete array
     */
    protected $_iterator          = array();

    /**
     * Abstract element.
     *
     * @var \Hoa\Xml\Element object
     */
    protected $abstract           = null;

    /**
     * Super root of the abstract element.
     *
     * @var \Hoa\Xml\Element object
     */
    protected $_superRoot         = null;



    /**
     * Build a concrete tree.
     *
     * @access  public
     * @param   \Hoa\Xml\Element  $abstract     Abstract element.
     * @param   \Hoa\Xml\Element  $superRoot    Super root.
     * @param   array             $rank         Rank: abstract elements to
     *                                          concrete elements.
     * @param   string            $namespace    Namespace.
     * @return  void
     */
    final public function __construct ( \Hoa\Xml\Element $abstract,
                                        \Hoa\Xml\Element $superRoot,
                                        Array            $rank = array(),
                                        $namespace             = null ) {

        self::$_store[]      = $abstract;
        self::$_superRoots[] = $superRoot;
        self::$_multiton[]   = $this;

        if(null !== $namespace)
            $abstract->useNamespace($namespace);

        if(null === $this->_name) {

            $this->_name     = strtolower(get_class($this));

            if(false !== $po = strrpos($this->_name, '\\'))
                $this->_name = substr($this->_name, $po + 1);
        }

        $this->abstract      = $abstract;
        $this->_superRoot    = $superRoot;

        if($this instanceof Model\Phrasing)
            $iterator = $abstract->readAsPhrasingModel($namespace);
        else
            $iterator = $abstract->selectChildElements();

        foreach($iterator as $child) {

            $name = $child->getName();

            if(!isset($rank[$name]))
                throw new \Hoa\Xml\Exception(
                    'Cannot build the concrete tree because the abstract ' .
                    'element <%s> has no ranked concrete element.', 0, $name);

            $c = $rank[$name];
            $h = new $c($child, $superRoot, $rank, $namespace);
            $this->_children[$h->getName()][] = $h;
            $this->_iterator[]                = $h;
        }

        $this->construct();

        return;
    }

    /**
     * Simplify overloading of the constructor.
     *
     * @access  public
     * @return  void
     */
    public function construct ( ) {

        return;
    }

    /**
     * Get the abstract element ID.
     *
     * @access  public
     * @param   \Hoa\Xml\Element  $element    Abstract element.
     * @return  mixed
     */
    public static function getAbstractElementId ( \Hoa\Xml\Element $element ) {

        return array_search($element, self::$_store);
    }

    /**
     * Get the abstract element.
     *
     * @access  public
     * @return  \Hoa\Xml\Element
     */
    public function getAbstractElement ( ) {

        return $this->abstract;
    }

    /**
     * Get the associated concrete element of an abstract element.
     *
     * @access  public
     * @param   \Hoa\Xml\Element  $element    Abstract element.
     * @return  \Hoa\Xml\Element\Concrete
     * @throw   \Hoa\Xml\Exception
     */
    public static function getConcreteElement ( \Hoa\Xml\Element $element ) {

        if(false === $id = self::getAbstractElementId($element))
            throw new \Hoa\Xml\Exception(
                'The basic element %s has no concrete equivalent.',
                1, $element->getName());

        return self::$_multiton[$id];
    }

    /**
     * Get the super-root of the abstract element.
     *
     * @access  public
     * @return  \Hoa\Xml\Element
     */
    public function getAbstractElementSuperRoot ( ) {

        return $this->_superRoot;
    }

    /**
     * Get the super-root of an abstract element.
     *
     * @access  public
     * @param   \Hoa\Xml\Element  $element    Abstract element.
     * @return  \Hoa\Xml\Element
     * @throws  \Hoa\Xml\Exception
     */
    public static function getAbstractElementSuperRootOf ( \Hoa\Xml\Element $element ) {

        if(false === $id = self::getAbstractElementId($element))
            throw new \Hoa\Xml\Exception(
                'The concrete element %s has no concrete equivalent and we ' .
                'cannot retrieve the super-root.', 2, $element->getName());

        return self::$_superRoots[$id];
    }

    /**
     * Get the name of the concrete element.
     *
     * @access  public
     * @return  string
     */
    public function getName ( ) {

        return $this->_name;
    }

    /**
     * Count children number.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_iterator);
    }

    /**
     * Get the iterator.
     *
     * @access  public
     * @return  \ArrayIterator
     */
    public function getIterator ( ) {

        return new \ArrayIterator($this->_iterator);
    }

    /**
     * Set a child.
     *
     * @access  public
     * @param   string                     $name     Child name.
     * @param   \Hoa\Xml\Element\Concrete  $value    Value.
     * @return  void
     */
    public function __set ( $name, Concrete $value ) {

        $this->_children[$name][] = $value;
        $this->_iterator[]        = $value;

        return;
    }

    /**
     * Get a child.
     *
     * @access  public
     * @param   string  $name    Child value.
     * @return  mixed
     */
    public function __get ( $name ) {

        if(!isset($this->_children[$name]))
            return null;

        return $this->_children[$name];
    }

    /**
     * Check if an element exists.
     *
     * @access  public
     * @param   int     $offset    Element index.
     * @return  bool
     */
    public function offsetExists ( $offset ) {

        return isset($this->_iterator[$offset]);
    }

    /**
     * Get an element.
     *
     * @access  public
     * @param   int     $offset    Element index.
     * @return  \Hoa\Xyl\Element\Concrete
     */
    public function offsetGet ( $offset ) {

        if(false === $this->offsetExists($offset))
            return null;

        return $this->_iterator[$offset];
    }

    /**
     * Set an element to an index.
     *
     * @access  public
     * @param   string                     $offset    Element index.
     * @param   \Hoa\Xyl\Element\Concrete  $value     Element.
     * @return  void
     */
    public function offsetSet ( $offset, $element ) {

        if(is_string($element))
            $name = $element;
        else
            $name = $element->getName();

        if(!isset($this->_children[$name]))
            $this->_children[$name] = array();

        $this->_children[$name][$offset] = $element;
        $this->_iterator[$offset]        = $element;

        return;
    }

    /**
     * Remove an element.
     *
     * @access  public
     * @param   string  $offset    Element index.
     * @return  void
     */
    public function offsetUnset ( $offset ) {

        if(!isset($this->_iterator[$offset]))
            return;

        $element = $this->_iterator[$offset];
        $name    = $element->getName();
        $i       = array_search($element, $this->_children[$name]);

        unset($this->_children[$name][$i]);
        unset($this->_iterator[$offset]);

        return;
    }

    /**
     * Redirect unknown call on the abstract element.
     *
     * @access  public
     * @param   string  $name         Name.
     * @param   array   $arguments    Arguments.
     * @return  mixed
     */
    public function __call ( $name, Array $arguments = array() ) {

        return call_user_func_array(
            array($this->abstract, $name),
            $arguments
        );
    }
}

}
