<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\Aggregator;


use Leaf\Components\Ini\Collections\SectionsCollection;
use Leaf\Components\Ini\DocumentObjects\Node;

/**
 * Class ArrayAggregator
 * Aggregates an Array of a SectionsCollection.
 *
 * @package Leaf\Components\Ini\Aggregator
 * @version 1.0.0
 */
class ArrayAggregator
{
    /**
     * @var \Leaf\Components\Ini\Collections\SectionsCollection
     */
    protected $collection;

    /**
     * Constructor
     * requires a SectionsCollection
     *
     * @param SectionsCollection $collection
     */
    public function __construct(SectionsCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * aggregate()
     * aggregates the entire document of the SectionsCollection ( without comments ) into an array.
     * Properties that already exists while the aggregation loop iterates the document will be transported
     * into an array.
     *
     * You may specify an callback which will be exclusively executed at array write steps to map or transport the
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
    public function aggregate(Callable $callback = null, $propertyDecoupleSeparator = null, $globalKey = '*')
    {
        $contents = array();

        $aggregator = function(Node $property) use ( $callback, $propertyDecoupleSeparator) {

            /** @var \Leaf\Components\Ini\DocumentObjects\Node $property */

            if ( null !== $propertyDecoupleSeparator ) {

                $outbound = array();

                $current =& $outbound;

                $propertyMap = explode($propertyDecoupleSeparator, $property->getAttribute('name'));

                foreach ( $propertyMap as $horizontal ) {

                    $current =& $current[$horizontal];

                }

                if ( null !== $callback ) {

                    $current = call_user_func($callback, $property->getAttribute('value'));

                }
                else {

                    $current = $property->getAttribute('value');

                }

                unset($current);

                return $outbound;

            }
            else {

                return $property->asArray();

            }

        };

        foreach ( $this->collection->getRoot()->getAllSections() as $section ) {

            /** @var \Leaf\Components\Ini\DocumentObjects\Node $section */

            if ( $section->isGlobalSection() ) {

                $key = $globalKey;

            }
            else {

                $key = $section->getAttribute('name');

            }

            $contents[$key] = array();

            foreach ( $section->getAllProperties() as $property ) {

                $contents[$key] = array_merge_recursive(
                    $contents[$key],
                    call_user_func($aggregator, $property)
                );

            }

        }

        return $contents;
    }
} 