<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\DocumentObjects;

use DOMElement;
use DOMException;
use DOMXPath;

/**
 * Class Node
 * DOM DOMElement replacement
 *
 * @package Leaf\Components\Ini\DocumentObjects
 * @version 1.0.0
 */
class Node
    extends DOMElement
{
    /**
     * isSection()
     * "is current element a section"-identifier.
     *
     * Optional: You may pass a name as first parameter to force this function to check
     * if the current section has an attribute that content equals to the parameter content.
     *
     * @param null|string $name
     * @return bool
     */
    public function isSection($name = null)
    {
        if ( null === $name ) {
            return $this->nodeName === 'ini:section' && $this->hasAttribute('name');
        }
        else {
            return $this->isSection() && $this->getAttribute('name') === $name;
        }
    }

    /**
     * isGlobalSection()
     * "is current element a global section"-identifier.
     *
     * @return bool
     */
    public function isGlobalSection()
    {
        return $this->nodeName === 'ini:global-section';
    }

    /**
     * isProperty()
     * "is current element a property"-identifier.
     *
     * Optional: You may pass a name as first parameter to force this function to check
     * if the current property has an attribute that contents equals to the parameter content.
     *
     * @param null|string $name
     * @return bool
     */
    public function isProperty($name = null)
    {
        if ( null === $name ) {
            return $this->nodeName === 'ini:property' && $this->hasAttribute('name');
        }
        else {
            return $this->isProperty() && $this->getAttribute('name') === $name;
        }
    }

    /**
     * isRoot()
     * "is current element the root element"-identifier.
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->nodeName === 'ini:ini';
    }

    /**
     * hasValue()
     * asks for a value on a property element. Throws an exception if called on a non-property.
     *
     * @return bool
     * @throws \DOMException
     */
    public function hasValue()
    {
        if ( ! $this->isProperty() ) {

            throw new DOMException('This element is not a property');

        }

        return $this->hasAttribute('value');
    }

    /**
     * hasComments()
     * asks for comments an a property, section, global section or root.
     *
     * @return bool
     */
    public function hasComments()
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query('child::comment()')->length > 0;
    }

    /**
     * hasProperty()
     * asks for a specific property on the current section or global section.
     * Throws an exception if called on a non-section.
     *
     * @param $name
     * @return bool
     * @throws \DOMException
     */
    public function hasProperty($name)
    {
        if ( ! $this->isSection() && ! $this->isGlobalSection() ) {

            throw new DOMException('This element is not a section');

        }

        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:property[@name='".$name."'][1]", $this)->length > 0;
    }

    /**
     * getProperty()
     * property-getter. Throws an exception if called on a non-section.
     *
     * @param $name
     * @return Node
     * @throws \DOMException
     */
    public function getProperty($name)
    {
        if ( ! $this->isSection() && ! $this->isGlobalSection() ) {

            throw new DOMException('This element is not a section');

        }

        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:property[@name='".$name."'][1]", $this)->item(0);
    }

    /**
     * setValue()
     * property-value setter. Throws an exception if called on a non-property.
     *
     * @param $value
     * @throws \DOMException
     */
    public function setValue($value)
    {
        if ( ! $this->isProperty() ) {

            throw new DOMException('This element is not a property');

        }

        $this->setAttribute('value', $value);
    }

    /**
     * getValue()
     * property-value getter. Throws an exception if called on a non-property.
     *
     * @throws \DOMException
     * @returns string
     */
    public function getValue()
    {
        if ( ! $this->isProperty() ) {

            throw new DOMException('This element is not a property');

        }

        return $this->getAttribute('value');
    }

    /**
     * hasSection()
     * asks for a section. Works from everywhere.
     *
     * @param $name
     * @return bool
     */
    public function hasSection($name)
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:section[@name='".$name."'][1]", $this->ownerDocument->documentElement)->length > 0;
    }

    /**
     * getSection()
     * section getter. Works from everywhere.
     *
     * @param $name
     * @return Node
     */
    public function getSection($name)
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:section[@name='".$name."'][1]", $this->ownerDocument->documentElement)->item(0);
    }

    /**
     * hasGlobalSection()
     * asks for the global section.
     *
     * @return bool
     */
    public function hasGlobalSection()
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:global-section[1]", $this->ownerDocument->documentElement)->length > 0;
    }

    /**
     * getGlobalSection()
     * getter for the global section.
     *
     * @return Node
     */
    public function getGlobalSection()
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./ini:global-section[1]", $this->ownerDocument->documentElement)->item(0);
    }

    /**
     * addGlobalSection()
     * adds a global section element at the top of the document.
     *
     * @returns Node
     */
    public function addGlobalSection()
    {
        $element = $this->ownerDocument->createElementNS($this->namespaceURI, 'ini:global-section');

        return $this->ownerDocument->documentElement->insertBefore(
            $element,
            $this->ownerDocument->documentElement->childNodes->item(0)
        );
    }

    /**
     * addSection()
     * adds a section to the document. Throws an exception if not called from root.
     *
     * @param $name
     * @return Node
     * @throws \DOMException
     */
    public function addSection($name)
    {
        if ( ! $this->isRoot() ) {

            throw new DOMException('This element is not the root element');

        }

        if ( null === $name ) {
            return $this->addGlobalSection();
        }

        $element = $this->ownerDocument->createElementNS($this->namespaceURI, 'ini:section');
        $element->setAttribute('name', (string) $name);

        return $this->appendChild($element);
    }

    /**
     * addProperty()
     * adds a property to the document. Throws an exception if not called from a section.
     *
     * @param $name
     * @param null $value
     * @return Node
     * @throws \DOMException
     */
    public function addProperty($name, $value = null)
    {
        if ( ! $this->isSection() && ! $this->isGlobalSection() ) {

            throw new DOMException('This element is not a section');

        }

        $property = $this->ownerDocument->createElementNS($this->namespaceURI, 'ini:property');
        $property->setAttribute('name', (string) $name);

        if ( null !== $value ) {

            $property->setAttribute('value', (string) $value);

        }

        return $this->appendChild($property);
    }

    /**
     * addSpace()
     * adds an Empty Comment to the propery.
     *
     * @return Node
     */
    public function addSpace()
    {
        $comment = $this->ownerDocument->createComment('');

        return $this->appendChild($comment);
    }

    /**
     * addComment()
     * adds a Comment to the Document.
     *
     * @param $string
     * @return \DOMNode
     */
    public function addComment($string)
    {
        $comment = $this->ownerDocument->createComment((string) $string);

        return $this->appendChild($comment);
    }

    /**
     * dropComments()
     * removes comments of the current target
     */
    public function dropComments($deep = false)
    {
        $target = $this;

        if ( $this->isRoot() && $deep ) {

            $query = '//comment()';
            $target = $this->ownerDocument->documentElement;

        }
        elseif ( $this->isSection() || $this->isGlobalSection() ) {

            if ( $deep ) {

                $query = '//comment()';

            }
            else {

                $query = './comment()';

            }

        }
        else {

            $query = './comment()';

        }

        $xpath = new DOMXPath($this->ownerDocument);
        $result = $xpath->query($query, $target);
        $targets = $result->length;

        foreach ( $result as $current ) {
            /** @var Node $current */
            $current->parentNode->removeChild($current);
        }

        return $targets;
    }

    /**
     * delete()
     * removes a property or a complete section.
     */
    public function delete()
    {
        $this->parentNode->removeChild($this);
    }

    /**
     * asArray()
     * Array exporter for a section or property.
     *
     * @return array
     */
    public function asArray()
    {
        if ( $this->isSection() || $this->isGlobalSection() ) {

            $properties = array();

            foreach ( $this->getAllProperties() as $property ) {

                /** @var Node $property */
                $properties = array_merge_recursive($properties, $property->asArray());

            }

            return $properties;

        }

        if ( $this->isProperty() ) {

            return array(
                $this->getAttribute('name') => $this->getAttribute('value'),
            );

        }

        return array();
    }

    /**
     * getAllSections()
     * returns all sections.
     *
     * @return \DOMNodeList
     */
    public function getAllSections()
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        return $xpath->query("./*[name()='ini:global-section' or name()='ini:section']", $this->ownerDocument->documentElement);
    }

    /**
     * getAllProperties()
     * gets All Properties of the current section or the global document.
     *
     * @return \DOMNodeList
     */
    public function getAllProperties()
    {
        $xpath = new DOMXPath($this->ownerDocument);
        $xpath->registerNamespace('ini', $this->namespaceURI);

        if ( ! $this->isRoot() ) {

            return $xpath->query('./ini:property', $this);

        }

        return $xpath->query('//ini:property', $this);
    }
} 