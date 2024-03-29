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
 * \Hoa\Regex\Visitor\Exception
 */
-> import('Regex.Visitor.Exception')

/**
 * \Hoa\Regex\Visitor\Visit
 */
-> import('Regex.Visitor.Visit')

/**
 * \Hoa\Math\Util
 */
-> import('Math.Util')

/**
 * \Hoa\Math\Combinatorics\Combination
 */
-> import('Math.Combinatorics.Combination');

}

namespace Hoa\Regex\Visitor {

/**
 * Class \Hoa\Regex\Visitor\UniformPreCompute.
 *
 * Pre-compute the AST.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class UniformPreCompute implements Visit {

    /**
     * Given size: n.
     *
     * @var \Hoa\Regex\Visitor\UniformPreCompute int
     */
    protected $_n = 0;



    /**
     * Initialize the size.
     *
     * @access  public
     * @param   int  $n    Size.
     * @return  void
     */
    public function __construct ( $n = 0 ) {

        $this->setSize($n);

        return;
    }

   /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function visit ( \Hoa\Visitor\Element $element,
                            &$handle = null, $eldnah = null ) {

        $n                  = null === $eldnah ? $this->_n : $eldnah;
        $data               = &$element->getData();

        if(!isset($data['precompute']))
            $data['precompute'] = array($n => array());

        if(isset($data['precompute'][$n]['n']))
            return $data['precompute'][$n]['n'];

        $data['precompute'][$n]['n'] = 0;

        if(0 === $n)
            return 0;

        $out = &$data['precompute'][$n]['n'];

        switch($element->getId()) {

            case '#expression':
            case '#capturing':
            case '#namedcapturing':
                return $out = $element->getChild(0)->accept($this, $handle, $n);
              break;

            case '#alternation':
            case '#class':
                foreach($element->getChildren() as $child)
                    $out += $child->accept($this, $handle, $n);

                return $out;
              break;

            case '#concatenation':
                $Γ = \Hoa\Math\Combinatorics\Combination::Γ(
                    $element->getChildrenNumber(),
                    $n,
                    true
                );

                if(!isset($data['precompute'][$n]['Γ']))
                    $data['precompute'][$n]['Γ'] = array();

                foreach($Γ as $γ) {

                    if(true === in_array(0, $γ))
                        continue;

                    $oout = 1;

                    foreach($γ as $α => $_γ)
                        $oout *= $element->getChild($α)->accept(
                            $this,
                            $handle,
                            $_γ
                        );

                    if(0 !== $oout)
                        $data['precompute'][$n]['Γ'][] = $γ;

                    $out += $oout;
                }

                return $out;
              break;

            case '#quantification':
                if(!isset($data['precompute'][$n]['xy']))
                    $data['precompute'][$n]['xy'] = array();

                $xy = $element->getChild(1)->getValueValue();
                $x  = 0;
                $y  = 0;

                switch($element->getChild(1)->getValueToken()) {

                    case 'zero_or_one':
                        $y = 1;
                      break;

                    case 'zero_or_more':
                        $y = $this->getSize();
                      break;

                    case 'one_or_more':
                        $x = 1;
                        $y = $this->getSize();
                      break;

                    case 'exactly_n':
                        $x = $y = (int) substr($xy, 1, -1);
                      break;

                    case 'n_to_m':
                        $xy = explode(',', substr($xy, 1, -1));
                        $x  = (int) trim($xy[0]);
                        $y  = (int) trim($xy[1]);
                      break;

                    case 'n_or_more':
                        $xy = explode(',', substr($xy, 1, -1));
                        $x  = (int) trim($xy[0]);
                        $y  = $this->getSize();
                      break;
                }

                for($α = $x; $α <= $y; ++$α) {

                    $data['precompute'][$n]['xy'][$α] = array();
                    $Γ  = \Hoa\Math\Combinatorics\Combination::Γ($α, $n, true);
                    $ut = 0;

                    foreach($Γ as $γ) {

                        if(true === in_array(0, $γ))
                            continue;

                        $oout = 1;

                        foreach($γ as $β => $_γ)
                            $oout *= $element->getChild(0)->accept(
                                $this,
                                $handle,
                                $_γ
                            );

                        if(0 !== $oout)
                            $data['precompute'][$n]['xy'][$α]['Γ'] = $γ;

                        $ut += $oout;
                    }

                    $data['precompute'][$n]['xy'][$α]['n'] = $ut;
                    $out += $ut;
                }

                return $out;
              break;

            case '#negativeclass':
                $minus = 0;

                foreach($element->getChildren() as $child)
                    $minus += $child->accept($this, $handle, $n);

                return $out = 126 - 32 - $minus;
              break;

            case '#range':
                return $out = max(
                    0,
                      ord($this->tokenToChar($element->getChild(1))),
                    - ord($this->tokenToChar($element->getChild(0))),
                    + 1
                );
              break;

            case 'token':
                return $out = \Hoa\Math\Util::δ($n, 1);
        }

        return -1;
    }

    /**
     * Set size.
     *
     * @access  public
     * @param   int  $n    Size.
     * @return  int
     */
    public function setSize ( $n ) {

        $old      = $this->_n;
        $this->_n = $n;

        return $old;
    }

    /**
     * Get size.
     *
     * @access  public
     * @return  int
     */
    public function getSize ( ) {

        return $this->_n;
    }

    public function tokenToChar ( $element ) {

        $value = $element->getValueValue();

        switch($element->getValueToken()) {

            case 'character':
                switch($value) {

                    case '\a':
                        return "\a";

                    case '\e':
                        return "\e";

                    case '\f':
                        return "\f";

                    case '\n':
                        return "\n";

                    case '\r':
                        return "\r";

                    case '\t':
                        return "\t";

                    default:
                        return chr($value[2]);
                }
              break;

            case 'dynamic_character':
                $value = ltrim($value, '\\');

                switch($value[0]) {

                    case 'x':
                        $value = trim($value, 'x{}');
                        return $this->uni_chr($value);
                      break;

                    default:
                        return chr(octdec($value));
                }
              break;

            case 'character_type':
                $value = ltrim($value, '\\');

                switch($value) {

                    case 'C':
                        return $this->_sampler->getInteger(0, 127);

                    case 'd':
                        return $this->_sampler->getInteger(0, 9);

                    case 's':
                        $value = $this->_sampler->getInteger(0, 1)
                                     ? 'h'
                                     : 'v';

                    case 'h':
                        return static::$_hSpaces[
                            $this->_sampler->getInteger(
                                0,
                                count(static::$_hSpaces) - 1
                            )
                        ];

                    case 'v':
                        return static::$_vSpaces[
                            $this->_sampler->getInteger(
                                0,
                                count(static::$_vSpaces) - 1
                            )
                        ];

                    case 'w':
                        $_  = array_merge(
                            range(0x41, 0x5a),
                            range(0x61, 0x7a),
                            array(0x5f)
                        );

                        return $this->uni_chr(dechex($_[
                            $this->_sampler->getInteger(
                                0,
                                count($_) - 1
                            )
                        ]));

                    default:
                        return '?';
                }
              break;

            case 'literal':
                return str_replace('\\\\', '\\', preg_replace(
                    '#\\\(?!\\\)#',
                    '',
                    $element->getValueValue()
                ));
        }
    }
}

}
