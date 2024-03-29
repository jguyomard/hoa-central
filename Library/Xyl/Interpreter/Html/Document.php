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
 * \Hoa\Xyl\Exception
 */
-> import('Xyl.Exception')

/**
 * \Hoa\Xyl\Interpreter\Html\Concrete
 */
-> import('Xyl.Interpreter.Html.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Document.
 *
 * The <document /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Document extends Concrete implements \Hoa\Xyl\Element\Executable {

    /**
     * Title.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Title object
     */
    protected $_title     = null;

    /**
     * All document resources.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Document array
     */
    protected $_resources = array();



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $root = $this->getAbstractElementSuperRoot();

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<!--[if lt IE 7]><html class="ie6"><![endif]-->' . "\n" .
            '<!--[if    IE 7]><html class="ie7"><![endif]-->' . "\n" .
            '<!--[if    IE 8]><html class="ie8"><![endif]-->' . "\n" .
            '<!--[if (gte IE 9)|!(IE)]>' . "\n" .
            '<html>' . "\n" .
            '<![endif]-->' . "\n" .
            '<head>' . "\n" .
            '  '
        );

        if(null !== $this->_title)
            $this->_title->render($out);

        $out->writeAll(
            "\n" .
            '  <meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n" .
            '  <meta http-equiv="content-type" content="text/javascript; charset=utf-8" />' . "\n" .
            '  <meta http-equiv="content-type" content="text/css; charset=utf-8" />' . "\n"
        );

        if(isset($this->_resources['css'])) {

            $out->writeAll("\n");

            foreach($this->_resources['css'] as $href)
                $out->writeAll(
                    '  <link type="text/css" href="' . $href .
                    '" rel="stylesheet" />' . "\n"
                );
        }

        $out->writeAll(
            '</head>' . "\n" .
            '<body>' . "\n\n" .
            '<div id="body">' . "\n"
        );

        foreach($this as $child)
            if('title' != $child->abstract->getName())
                $child->render($out);

        $out->writeAll(
            "\n" . '</div>' . "\n\n" . '</body>' . "\n" . '</html>'
        );

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        //$this->computeStylesheet();

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

        $this->computeTitle();
        $this->computeStylesheet();

        return;
    }

    /**
     * Compute title.
     *
     * @access  protected
     * @return  void
     */
    protected function computeTitle ( ) {

        $xpath = $this->xpath('./__current_ns:title');

        if(empty($xpath))
            return;

        $this->_title = $this->getConcreteElement($xpath[0]);

        return;
    }

    /**
     * Compute ressources.
     *
     * @access  public
     * @param   array  &$resources    Ressources.
     * @return  void
     * @throw   \Hoa\Xyl\Exception
     */
    protected function computeRessources ( Array &$resources ) {

        $root      = $this->getAbstractElementSuperRoot();
        $theme     = $root->getParameters()->getFormattedParameter('theme');
        $router    = $root->getRouter();
        $libHead   = '#^' . preg_quote(
                         $root->resolve('hoa://Library/Xyl/', true)
                     ) . '(.*)$#';
        $appHead   = '#^' . preg_quote(
                         $_ = $root->resolve('hoa://Application/Public/', true)
                     ) . '(.*)$#';

        foreach($resources as &$resource) {

            if(0 !== preg_match($libHead, $resource, $matches)) {

                $target = $_ . $matches[1];

                if(true !== file_exists($target)) {

                    $copy = @copy(
                        $root->resolve($resource),
                        $root->resolve($target, true)
                    );

                    if(false === $copy)
                        throw new \Hoa\Xyl\Exception(
                            'Ressource %s can be copied to %s.', 0,
                            array('hoa://Library/Xyl/' . $matches[1], $target));
                }

                $resource = $target;
            }

            if(0 !== preg_match($appHead, $resource, $matches)) {

                $top = '_' . strtolower(substr(
                    $matches[1],
                    0,
                    ($pos = strpos($matches[1], '/')) ?: strlen($matches[1])
                ));

                if(false === $router->ruleExists($top)) {

                    if(false === $router->ruleExists('_resource'))
                        continue;

                    $top = '_resource';
                    $pos = false;
                }

                $resource = $router->unroute(
                    $top,
                    array(
                        'theme'    => $theme,
                        'resource' => substr(
                            $matches[1],
                            false !== $pos ? $pos + 1 : 0
                        )
                    )
                );
            }
        }

        return;
    }

    /**
     * Compute stylesheet.
     *
     * @access  protected
     * @return  void
     */
    protected function computeStylesheet ( ) {

        $root                    = $this->getAbstractElementSuperRoot();
        $this->_resources['css'] = $root->getStylesheets();
        $this->computeRessources($this->_resources['css']);

        return;
    }

    /**
     * Get the <title /> component.
     *
     * @access  public
     * @return  \Hoa\Xyl\Interpreter\Html\Title
     */
    public function getTitle ( ) {

        return $this->_title;
    }
}

}
