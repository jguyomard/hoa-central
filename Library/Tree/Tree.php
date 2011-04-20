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
 * \Hoa\Tree\Exception
 */
-> import('Tree.Exception')

/**
 * \Hoa\Tree\Generic
 */
-> import('Tree.Generic');

}

namespace Hoa\Tree {

/**
 * Class \Hoa\Tree.
 *
 * Manipule a tree.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Tree extends Generic {

    /**
     * Insert a child.
     * Fill the child list from left to right.
     *
     * @access  public
     * @param   \Hoa\Tree  $child    Child to insert.
     * @return  \Hoa\Tree
     * @throw   \Hoa\Tree\Exception
     */
    public function insert ( Generic $child ) {

        if(!($child instanceof Tree))
            throw new Exception(
                'Child must be an instance of \Hoa\Tree; given %s.',
                0, get_class($child));

        $this->_childs[$child->getValue()->getId()] = $child;

        return $this;
    }

    /**
     * Delete a child.
     *
     * @access  public
     * @param   mixed   $nodeId    Node ID.
     * @return  \Hoa\Tree\Generic
     * @throw   \Hoa\Tree\Exception
     */
    public function delete ( $nodeId ) {

        unset($this->_childs[$nodeId]);

        return $this;
    }

    /**
     * Check if the node is a leaf.
     *
     * @access  public
     * @return  bool
     */
    public function isLeaf ( ) {

        return empty($this->_childs);
    }

    /**
     * Check if the node is a node (i.e. not a leaf).
     *
     * @access  public
     * @return  bool
     */
    public function isNode ( ) {

        return !empty($this->_childs);
    }
}

}
