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
 * \Hoa\Visitor\Element
 */
-> import('Visitor.Element');

}

namespace Hoa\Compiler\Llk {

/**
 * Class \Hoa\Compiler\TreeNode.
 *
 * Provide a generic node for the AST produced by LL(k) parser.
 *
 * @author     Frédéric Dadeau <frederic.dadeau@femto-st.fr>
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Frédéric Dadeau, Ivan Enderlin.
 * @license    New BSD License
 */

class TreeNode implements \Hoa\Visitor\Element {

    /**
     * ID (should be something like #ruleName or token).
     *
     * @var \Hoa\Compiler\TreeNode string
     */
    protected $_id       = null;

    /**
     * Value of the node (non-null for token nodes).
     *
     * @var \Hoa\Compiler\TreeNode string
     */
    protected $_value    = null;

    /**
     * Children.
     *
     * @var \Hoa\Compiler\TreeNode array
     */
    protected $_children = null;

    /**
     * Attached data.
     *
     * @var \Hoa\Compiler\TreeNode array
     */
    protected $_data     = array();



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $id          ID.
     * @param   array   $value       Value.
     * @param   array   $children    Children.
     * @return  void
     */
    public function __construct ( $id, $value = null, Array $children = array() ) {

        $this->setId($id);
        $this->setValue($value);
        $this->setChildren($children);

        return;
    }

    /**
     * Set ID.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  string
     */
    public function setId ( $id ) {

        $old       = $this->_id;
        $this->_id = $id;

        return $old;
    }

    /**
     * Get ID.
     *
     * @access  public
     * @return  string
     */
    public function getId ( ) {

        return $this->_id;
    }

    /**
     * Set value.
     *
     * @access  public
     * @param   array  $value    Value (token & value).
     * @return  array
     */
    public function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get value.
     *
     * @access  public
     * @return  array
     */
    public function getValue ( ) {

        return $this->_value;
    }

    /**
     * Get value token.
     *
     * @access  public
     * @return  string
     */
    public function getValueToken ( ) {

        return $this->_value['token'];
    }

    /**
     * Get value value.
     *
     * @access  public
     * @return  string
     */
    public function getValueValue ( ) {

        return $this->_value['value'];
    }

    /**
     * Prepend a child.
     *
     * @access  public
     * @param   \Hoa\Compiler\TreeNode  $child    Child.
     * @return  \Hoa\Compiler\TreeNode
     */
    public function prependChild ( TreeNode $child ) {

        array_unshift($this->_children, $child);

        return $this;
    }

    /**
     * Append a child.
     *
     * @access  public
     * @param   \Hoa\Compiler\TreeNode  $child    Child.
     * @return  \Hoa\Compiler\TreeNode
     */
    public function appendChild ( TreeNode $child ) {

        $this->_children[] = $child;

        return $this;
    }

    /**
     * Set children.
     *
     * @access  public
     * @return  array
     */
    public function setChildren ( Array $children ) {

        $old             = $this->_children;
        $this->_children = $children;

        return $old;
    }

    /**
     * Get child.
     *
     * @access  public
     * @param   int  $i    Index.
     * @return  \Hoa\Compiler\TreeNode
     */
    public function getChild ( $i ) {

        return $this->_children[$i];
    }

    /**
     * Get children.
     *
     * @access  public
     * @return  array
     */
    public function getChildren ( ) {

        return $this->_children;
    }

    /**
     * Get number of children.
     *
     * @access  public
     * @return  int
     */
    public function getChildrenNumber ( ) {

        return count($this->_children);
    }

    /**
     * Get data.
     *
     * @access  public
     * @return  array
     */
    public function &getData ( ) {

        return $this->_data;
    }

    /**
     * Accept a visitor.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept ( \Hoa\Visitor\Visit $visitor,
                             &$handle = null, $eldnah = null ) {

        return $visitor->visit($this, $handle, $eldnah);
    }
}

}
