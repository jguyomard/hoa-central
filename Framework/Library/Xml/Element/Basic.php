<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Xml\Element
 */
-> import('Xml.Element.~')

/**
 * \Hoa\Xml\CssToXPath
 */
-> import('Xml.CssToXPath')

/**
 * \Hoa\Stream\IStream\Structural
 */
-> import('Stream.I~.Structural');

}

namespace Hoa\Xml\Element {

/**
 * Class \Hoa\Xml\Element\Basic.
 *
 * This class represents a XML element in a XML tree.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class          Basic
    extends    \SimpleXMLElement
    implements Element,
               \Hoa\Stream\IStream\Structural {

    /**
     * CssToXPath instance.
     *
     * @var \Hoa\Xml\CssToXPath object
     */
    protected static $_cssToXPath = null;

    /**
     * String buffer (nodeValue).
     *
     * @var \Hoa\StringBuffer object
     */
    protected static $_buffer     = null;



    /**
     * Select root of the document: :root.
     *
     * @access  public
     * @return  \Hoa\Xml\Element\Basic
     */
    public function selectRoot ( ) {

        self::$_buffer = null;

        return simplexml_import_dom(
            $this->readDOM()->ownerDocument->documentElement,
            get_class($this)
        );
    }

    /**
     * Select any elements: *.
     *
     * @access  public
     * @return  array
     */
    public function selectAnyElements ( ) {

        self::$_buffer = null;

        return $this->xpath('//__current_ns:*');
    }

    /**
     * Select elements of type E: E.
     *
     * @access  public
     * @param   string  $E    Element E.
     * @return  array
     */
    public function selectElements ( $E = null ) {

        if(null === $E)
            return $this->selectAnyElements();

        self::$_buffer = null;

        return $this->xpath('//__current_ns:' . $E);
    }

    /**
     * Select F elements descendant of an E element: E F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectDescendantElements ( $F = null ) {

        return $this->selectElements($F);
    }

    /**
     * Select F elements children of an E element: E > F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectChildElements ( $F = null ) {

        self::$_buffer = null;

        if(null === $F || '*' == $F)
            return $this->xpath('child::__current_ns:*');

        return $this->xpath('child::__current_ns:' . $F);
    }

    /**
     * Select an F element immediately preceded by an E element: E + F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  \Hoa\Xml\Element\Basic
     */
    public function selectAdjacentSiblingElement ( $F ) {

        self::$_buffer = null;
        $handle        = $this->xpath(
            'following-sibling::__current_ns:*[1]/self::__current_ns:' . $F
        );

        if(empty($handle))
            return false;

        return $handle[0];
    }

    /**
     * Select F elements preceded by an E element: E ~ F.
     *
     * @access  public
     * @param   string  $F    Element F.
     * @return  array
     */
    public function selectSiblingElements ( $F = null ) {

        if(null === $F)
            $F = '*';

        self::$_buffer = null;

        return $this->xpath('following-sibling::__current_ns:' . $F);
    }

    /**
     * Execute a query selector and return the first result.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  \Hoa\Xml\Element\Basic
     * @throw   \Hoa\Compiler\Exception
     */
    public function querySelector ( $query ) {

        $handle = $this->querySelectorAll($query);

        if(empty($handle))
            return false;

        return $handle[0];
    }

    /**
     * Execute a query selector and return one or many results.
     *
     * @access  public
     * @param   string  $query    Query.
     * @return  \Hoa\Xml\Element\Basic
     * @throw   array
     */
    public function querySelectorAll ( $query ) {

        if(null === self::$_cssToXPath) {

            self::$_cssToXPath = new \Hoa\Xml\CssToXPath();
            self::$_cssToXPath->setDefaultNamespacePrefix('__current_ns');
        }

        self::$_buffer = null;
        self::$_cssToXPath->compile($query);

        return $this->xpath(self::$_cssToXPath->getXPath());
    }

    /**
     * Transform this object to a string.
     *
     * @access  public
     * @return  string
     */
    public function __toString ( ) {

        return (string) $this;
    }

    /**
     * Read all attributes.
     *
     * @access  public
     * @return  array
     */
    public function readAttributes ( ) {

        $handle = (array) $this->attributes();

        if(!isset($handle['@attributes']))
            return array();

        return $handle['@attributes'];
    }

    /**
     * Read a specific attribute.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  string
     */
    public function readAttribute ( $name ) {

        $attributes = $this->readAttributes();

        if(false === array_key_exists($name, $attributes))
            return null;

        return $attributes[$name];
    }

    /**
     * Whether an attribute exists.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  bool
     */
    public function attributeExists ( $name ) {

        return true === array_key_exists($name, $this->readAttributes());
    }

    /**
     * Read attributes value as a list.
     *
     * @access  public
     * @return  array
     */
    public function readAttributesAsList ( ) {

        $attributes = $this->readAttributes();

        foreach($attributes as $name => &$value)
            $value = explode(' ', $value);

        return $attributes;
    }

    /**
     * Read a attribute value as a list.
     *
     * @access  public
     * @param   string  $name    Attribute's name.
     * @return  array
     */
    public function readAttributeAsList ( $name ) {

        return explode(' ', $this->readAttribute($name));
    }

    /**
     * Read custom attributes (as a set).
     * For example:
     *     <component data-abc="def" data-uvw="xyz" />
     * “data” is a custom attribute, so the $set.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributes ( $set ) {

        $out     = array();
        $set    .= '-';
        $strlen  = strlen($set);

        foreach($this->readAttributes() as $name => $value)
            if($set === substr($name, 0, $strlen))
                $out[substr($name, $strlen)] = $value;

        return $out;
    }

    /**
     * Read custom attributes values as a list.
     *
     * @access  public
     * @param   string  $set    Set name.
     * @return  array
     */
    public function readCustomAttributesAsList ( $set ) {

        $out = array();

        foreach($this->readCustomAttributes($set) as $name => $value)
            $out[$name] = explode(' ', $value);

        return $out;
    }

    /**
     * Read attributes as a string.
     *
     * @access  public
     * @return  string
     */
    public function readAttributesAsString ( ) {

        $out = null;

        foreach($this->readAttributes() as $name => $value)
            $out .= ' ' . $name . '="' . str_replace('"', '\"', $value) . '"';

        return $out;
    }

    /**
     * Read all with XML node.
     *
     * @access  public
     * @return  string
     */
    public function readXML ( ) {

        return $this->asXML();
    }

    /**
     * Read content as a DOM tree.
     *
     * @access  public
     * @return  \DOMElement
     */
    public function readDOM ( ) {

        return dom_import_simplexml($this);
    }

    /**
     * Read children as a phrasing model, i.e. transform:
     *     <foo>abc<bar>def</bar>ghi</foo>
     * into
     *     <foo><???>abc</???><bar>def</bar><???>ghi</???></foo>
     * where <???> is the value of the $element argument, i.e. the inter-text
     * element name. Please, see the \Hoa\Xml\Element\Model\Phrasing interface.
     *
     * @access  public
     * @param   string  $namespace    Namespace to use ('' if none).
     * @param   string  $element      Inter-text element name.
     * @return  array
     */
    public function readAsPhrasingModel ( $namespace = '', $element = '__text' ) {

        $out   = array();
        $list  = $this->readDOM()->childNodes;
        $class = get_class($this);

        for($i = 0, $max = $list->length; $i < $max; ++$i) {

            $node = $list->item($i);

            switch($node->nodeType) {

                case XML_ELEMENT_NODE:
                    $out[] = simplexml_import_dom($node, $class);
                  break;

                case XML_TEXT_NODE:
                    $out[] = new $class(
                        '<' . $element . '>' . $node->nodeValue .
                        '</' . $element . '>',
                        LIBXML_NOXMLDECL,
                        false,
                        $namespace,
                        false
                    );
                  break;
            }
        }

        return $out;
    }

    /**
     * Use a specific namespace.
     * For performance reason, we did not test if the namespace exists in the
     * document. Please, see the \Hoa\Xml::namespaceExists() method to do that.
     *
     * @access  public
     * @param   string  $namespace    Namespace.
     * @return  \Hoa\Xml\Element
     */
    public function useNamespace ( $namespace ) {

        $this->registerXPathNamespace('__current_ns', $namespace);

        return $this;
    }
}

}
