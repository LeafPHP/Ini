<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 02.02.14
 */

namespace Leaf\Components\Ini\DocumentObjects;


use DOMDocument;
use DOMXPath;

/**
 * Class Document
 * DOM DOMDocument replacement
 *
 * @package Leaf\Components\Ini\DocumentObjects
 * @version 1.0.0
 */
class Document
    extends DOMDocument
{
    /**
     * @var \DOMXPath
     */
    protected $xpath;

    /**
     * Constructor
     * requires the xml version, the encoding and the namespace of the element structure.
     *
     * @param string $version
     * @param string $encoding
     * @param string $namespace
     */
    public function __construct($version, $encoding, $namespace)
    {
        parent::__construct($version, $encoding);

        $this->xpath = new DOMXPath($this);
        $this->xpath->registerNamespace('ini', $namespace);

        $this->registerNodeClass('DOMElement', 'Leaf\\Components\\Ini\\DocumentObjects\\Node');
        $this->registerNodeClass('DOMComment', 'Leaf\\Components\\Ini\\DocumentObjects\\Comment');
        $this->registerNodeClass('DOMDocument', 'Leaf\\Components\\Ini\\DocumentObjects\\Document');
    }

    /**
     * getXPathObject()
     * xpath object getter.
     *
     * @return DOMXPath
     */
    public function getXPathObject()
    {
        return $this->xpath;
    }
} 