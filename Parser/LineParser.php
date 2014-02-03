<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\Parser;


use Leaf\Components\Ini\Collections\SectionsCollection;
use RuntimeException;

/**
 * Class LineParser
 * File-Stream Line-Parser for .ini file format.
 *
 * @package Leaf\Components\Ini\Parser
 * @version 1.0.0
 */
class LineParser
{
    /**
     * @var \Leaf\Components\Ini\Collections\SectionsCollection
     */
    protected $sectionCollection;
    /**
     * @var \Leaf\Components\Ini\DocumentObjects\Node
     */
    protected $lastSection;
    /**
     * @var
     */
    protected $namespace;

    /**
     * Constructor
     * Depends a SectionsCollection-Object.
     *
     * @param SectionsCollection $sectionCollection
     */
    public function __construct(SectionsCollection $sectionCollection)
    {
        $this->sectionCollection = $sectionCollection;
    }

    /**
     * parse()
     * executes line-based parsing on $content and adds a section, comment or property to the collection.
     *
     * @param $content
     * @throws RuntimeException
     * @return bool
     */
    public function parse($content)
    {
        $content = trim($content);

        if ( substr($content, 0, 1) === ';' ) {

            // Comment
            if ( null === $this->lastSection ) {

                $this->sectionCollection->getRoot()->addComment(substr($content, 1));

                return true;

            }

            if ( $this->lastSection ) {

                $this->lastSection->addComment(substr($content, 1));

            }

            return null;

        }
        elseif( substr($content, 0, 1) === '[' ) {

            // Section
            if ( null === $this->lastSection ) {

                $this->lastSection = $this->sectionCollection->getGlobalSection();

            }

            if ( substr($content, -1, 1) !== ']' ) {

                throw new RuntimeException('Unknown Entity. Probably a closing bracket elevates them to a section');

            }

            $section = trim($content, '[]');

            if ( ! $this->sectionCollection->getRoot()->hasSection($section) ) {

                $this->lastSection = $this->sectionCollection->getRoot()->addSection($section);

            }
            else {

                $this->lastSection = $this->sectionCollection->getRoot()->getSection($section);

            }

            return null;

        }
        elseif ( strlen($content) > 0 ) {

            // Property
            if ( null === $this->lastSection ) {

                $this->lastSection = $this->sectionCollection->getGlobalSection();

            }

            $pieces = array(
                'property' => null,
                'value' => null,
                'comment' => null,
            );

            $batch = array_keys($pieces);
            $current = 0;
            $inString = false;

            $str = fopen('php://memory','r+');
            fwrite($str, $content);
            rewind($str);

            while( $asc = fgetc($str) ) {

                if ( $current === 0 ) {

                    if ( $asc !== '=' ) {

                        $pieces[ $batch[$current] ] .= $asc;

                    }
                    else {

                        $current++;
                        continue;

                    }

                    continue;

                }

                if ( $current === 1 ) {

                    if ( ! $inString && $asc === '"' ) {

                        $inString = '"';

                    }
                    elseif ( ! $inString && $asc === "'" ) {

                        $inString = "'";

                    }

                    if ( $inString && $asc === $inString ) {

                        $inString = false;

                    }

                    if ( ! $inString && $asc === ';' ) {

                        $current++;
                        continue;

                    }

                    $pieces[ $batch[$current] ] .= $asc;
                    continue;

                }

                $pieces[ $batch[$current] ] .= $asc;

            }

            $property = $this->lastSection->addProperty($pieces['property'], $pieces['value']);

            if ( ! empty($pieces['comment']) ) {

                $property->addComment($pieces['comment']);

            }

            return null;

        }
        else {

            // Empty Line
            if ( ! $this->lastSection ) {

                $this->lastSection = $this->sectionCollection->getGlobalSection();

            }

            $this->lastSection->addSpace();

        }

        return null;
    }
} 