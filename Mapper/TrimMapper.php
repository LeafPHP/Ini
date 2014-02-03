<?php
/**
 * ini
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\Mapper;

use Leaf\Components\Ini\DocumentObjects\Node;

class TrimMapper
{
    public function __invoke(Node $node)
    {
        if ( $node->isSection() ) {
            $node->setAttribute('name', trim($node->getAttribute('name')));
        }

        if ( $node->isProperty() ) {
            $node->setAttribute('name', trim($node->getAttribute('name')));
            $node->setAttribute('value', trim($node->getAttribute('value')));
        }
    }
} 