<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\Collections;

use DOMDocument;
use DOMXPath;
use Leaf\Components\Ini\DocumentObjects\Node;
use Leaf\Components\Ini\DocumentObjects\Document;
use Leaf\Components\Ini\Aggregator\ArrayAggregator;
use Leaf\Components\Ini\Mapper\TrimMapper;

/**
 * Class SectionsCollection
 * SectionsCollection Data Broker Object
 *
 * @package Leaf\Components\Ini\Collections
 * @version 1.0.0
 */
class SectionsCollection
{
    /**
     * holds the DOMXPath instance
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * Constructor
     * requires a Document instance.
     *
     * @param Document $dom
     */
    public function __construct(Document $dom)
    {
        $this->xpath = $dom->getXPathObject();
    }

    /**
     * getRoot()
     * returns the Root element of the document.
     *
     * @return \Leaf\Components\Ini\DocumentObjects\Node
     */
    public function getRoot()
    {
        return $this->xpath->document->documentElement;
    }

    /**
     * getSection()
     * returns the requested section object.
     *
     * @param $name
     * @return \Leaf\Components\Ini\DocumentObjects\Node
     */
    public function getSection($name)
    {
        return $this->getRoot()->getSection($name);
    }

    /**
     * hasSection()
     * asks for a section at the root element.
     *
     * @param $name
     * @return bool
     */
    public function hasSection($name)
    {
        return $this->getRoot()->hasSection($name);
    }

    /**
     * addSection()
     * adds a sectiont to the root element.
     *
     * @param $name
     * @return Node
     */
    public function addSection($name)
    {
        return $this->getRoot()->addSection($name);
    }

    /**
     * getGlobalSection()
     * returns the global section.
     *
     * @return Node
     */
    public function getGlobalSection()
    {
        return $this->getRoot()->hasGlobalSection()
            ? $this->getRoot()->getGlobalSection()
            : $this->getRoot()->addGlobalSection();
    }

    /**
     * getDocument()
     * return the DOMDocument.
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->getRoot()->ownerDocument;
    }

    /**
     * asArray()
     * aggregates the entire document ( without comments ) into an array. Properties that already exists while
     * the aggregation loop iterates the document will be transported into an array.
     *
     * You may specify an property decouple separator to decouple array-like notations like:
     * "foo.bar.baz = boing" into [ 'foo' => [ 'bar' => [ 'baz' => 'boing' ] ] ]. defaults to null ( no decoupling ).
     *
     * You may specify an global key for the root key, defaults to '*'.
     *
     * @param null $propertyDecoupleSeparator
     * @param string $globalKey
     * @return array
     */
    public function asArray($propertyDecoupleSeparator = null, $globalKey = '*')
    {
        $aggregator = new ArrayAggregator($this);

        return $aggregator->aggregate(null, $propertyDecoupleSeparator, $globalKey);
    }

    /**
     * asMappedArray()
     * aggregates the entire document ( without comments ) into an array. Properties that already exists while
     * the aggregation loop iterates the document will be transported into an array.
     *
     * You have to specify an callback which will be exclusively executed at array write steps to map or transport the
     * result of the node.
     *
     * You may specify an property decouple separator to decouple array-like notations like:
     * "foo.bar.baz = boing" into [ 'foo' => [ 'bar' => [ 'baz' => 'boing' ] ] ]. defaults to null ( no decoupling ).
     *
     * You may specify an global key for the root key, defaults to '*'.
     *
     * @param callable $callback
     * @param null $propertyDecoupleSeparator
     * @param string $globalKey
     * @return array
     */
    public function asMappedArray(Callable $callback, $propertyDecoupleSeparator = null, $globalKey = '*')
    {
        $aggregator = new ArrayAggregator($this);

        return $aggregator->aggregate($callback, $propertyDecoupleSeparator, $globalKey);
    }

    /**
     * dropComments()
     * action query command to wipe _ALL_ comments from the document.
     *
     * @return $this
     */
    public function dropAllComments()
    {
        $this->getRoot()->dropComments(true);

        return $this;
    }

    /**
     * mapNodesWith()
     * action query command to map an callable on all / a reduced map of elements.
     * Use the second parameter to specify an root-level based xpath query.
     *
     * @param callable $callback
     * @param string $query
     * @return $this
     */
    public function mapNodesWith(Callable $callback, $query = '//*')
    {
        foreach ( $this->queryXPath($query) as $node ) {

            call_user_func($callback, $node);

        }

        return $this;
    }

    /**
     * trimKeysAndValues()
     * action query command to map an trim mapper to all elements.
     * This mapper adopts the functionality of the php core function trim() and trims any name and probably existent
     * value ( Element Nodes, not Comments ).
     *
     * @return $this
     */
    public function trimKeysAndValues()
    {
        $callable = new TrimMapper();
        /** @var Callable $callable */
        return $this->mapNodesWith($callable);
    }

    /**
     * queryXPath()
     * executes a xpath query on the document. Allows to pass a context element to reduce the amount of parsed
     * elements for relative queries. The context defaults to the root element of the document.
     *
     * @param $query
     * @param Node $node
     * @return \DOMNodeList
     */
    public function queryXPath($query, Node $node = null)
    {
        return $this->xpath->query($query, null !== $node ? $node : $this->getRoot());
    }

} 