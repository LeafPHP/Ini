<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini;

use Leaf\Components\Ini\Collections\SectionsCollection;
use Leaf\Components\Ini\DocumentObjects\Comment;
use Leaf\Components\Ini\DocumentObjects\Document;
use Leaf\Components\Ini\DocumentObjects\Node;
use Leaf\Components\Ini\Parser\LineParser;
use XSLTProcessor;
use SplFileInfo;
use SplFileObject;
use RuntimeException;
use DOMDocument;

/**
 * Class IniFileObject
 * Coordinates ini discovery in a SplFileObject manner.
 *
 * @package Leaf\Components\Ini
 * @version 1.0.0
 */
class IniFileObject
    extends SplFileInfo
{
    /**
     * Sections Collection
     *
     * @var Collections\SectionsCollection
     */
    protected $sections;

    /**
     * Constructor
     * Opens an existent or non-existent ini file for preparation. Since this is a DOM-worker, you may specify
     * an explicit namespace-url for your application, defaults to: http://leaf.virtual-ns/2014/file-type/ini
     *
     * Notice: This class adopts the entire functionality of SplFileInfo.
     *
     * @param $file_name
     * @param string $namespace
     * @throws RuntimeException
     */
    public function __construct($file_name, $namespace = 'http://leaf.virtual-ns/2014/file-type/ini')
    {
        parent::__construct($file_name);

        if ( $this->isFile() ) {

            if ( ! $this->isReadable() ) {

                throw new RuntimeException(
                    'Unable to open '.$this->getPathname()
                );

            }

            $this->sections = new SectionsCollection($this->aggregateDocument($namespace));

            $file = $this->openFile('r');
            $file->setFlags(SplFileObject::DROP_NEW_LINE);

            $parser = new LineParser($this->sections);

            foreach ( $file as $line ) {

                $parser->parse($line);

            }

        }
        else {

            if ( ! $this->isWritable() && ( $this->getPathInfo()->isDir() && ! $this->getPathInfo()->isWritable() ) ) {

                throw new RuntimeException(
                    'Unable to create '.$this->getPathname().', destination is not writable'
                );

            }

            if ( $this->isFile() && ! $this->isWritable() ) {

                throw new RuntimeException('File is not writable');

            }

            $this->sections = new SectionsCollection($this->aggregateDocument($namespace));

        }
    }

    /**
     * getContent()
     * getter for the contents.
     *
     * @return SectionsCollection
     */
    public function getContent()
    {
        return $this->sections;
    }

    /**
     * save()
     * stores the entire document _AS IS_ into a file or memory:
     * - for memory storaging use: php://memory
     * - for file storaging use: foobar.xml
     *
     * Target files can, but must not, be exist. Target files are overwritten by this command.
     *
     * You may specify an XSL-Template ( $stylesheet ) and a special XSLTProcessor instance. Both are optional.
     *
     * @throws RuntimeException
     * @param null|string $fileName
     * @param null|DOMDocument $stylesheet
     * @param null|XSLTProcessor $xslt
     * @returns SplFileObject
     */
    public function saveXML($fileName, DOMDocument $stylesheet = null, XSLTProcessor $xslt = null)
    {
        if ( null !== $fileName ) {

            $file = new SplFileInfo($fileName);

            if ( ! $file->isWritable() && ( $file->getPathInfo()->isDir() && ! $file->getPathInfo()->isWritable() ) ) {

                throw new RuntimeException('Destination is not writable');

            }

            if ( $file->isFile() && ! $file->isWritable() ) {

                throw new RuntimeException('File is not writable');

            }

            $fileHandle = $file->openFile('w');
        }
        else {

            throw new RuntimeException('fileName parameter can not be null');

        }

        $xml = $this->sections->getRoot()->ownerDocument;

        if ( $stylesheet instanceof DOMDocument ) {

            if ( ! $xslt instanceof XSLTProcessor ) {

                $xslt = new XSLTProcessor();

            }

            $xslt->importStylesheet($stylesheet);

            $xml = $xslt->transformToXml($xml);
        }

        $fileHandle->fwrite($xml->saveXml());
        $fileHandle->rewind();

        return $fileHandle;
    }

    /**
     * saveIni()
     * stores the entires document _AS IS_ into a file with ini file formatting.
     *
     * You may specify an filename to save into an alternate file. Leaving the first parameter null will force
     * saveIni() to overwrite the current opened file.
     *
     * @param null $fileName
     * @throws RuntimeException
     * @returns SplFileObject
     */
    public function save($fileName = null)
    {
        if ( null !== $fileName ) {

            $file = new SplFileInfo($fileName);

            if ( ! $file->isWritable() && ( $file->getPathInfo()->isDir() && ! $file->getPathInfo()->isWritable() ) ) {

                throw new RuntimeException('Destination is not writable');

            }

            if ( $file->isFile() && ! $file->isWritable() ) {

                throw new RuntimeException('File is not writable');

            }

            $fileHandle = $file->openFile('w');
        }
        else {

            $fileHandle = $this->openFile('w');

        }

        foreach ( $this->getContent()->getRoot()->childNodes as $rootNode ) {

            if ( $rootNode instanceof Comment && ! empty($rootNode->nodeValue) ) {

                $fileHandle->fwrite(';'.$rootNode->nodeValue.PHP_EOL);

            }
            elseif ( $rootNode instanceof Comment && empty($rootNode->nodeValue) ) {

                $fileHandle->fwrite(PHP_EOL);

            }

            if ( $rootNode instanceof Node && ! $rootNode instanceof Comment ) {

                if ( $rootNode->isSection() || $rootNode->isGlobalSection() ) {

                    if ( $rootNode->isSection() ) {

                        $fileHandle->fwrite('['.$rootNode->getAttribute('name').']'.PHP_EOL);

                    }

                    foreach ( $rootNode->childNodes as $sectionChild ) {

                        if ( $sectionChild instanceof Comment && ! empty($sectionChild->nodeValue) ) {

                            $fileHandle->fwrite(';'.$sectionChild->nodeValue.PHP_EOL);

                        }
                        elseif ( $sectionChild instanceof Comment && empty($sectionChild->nodeValue) ) {

                            $fileHandle->fwrite(PHP_EOL);

                        }

                        if ( $sectionChild instanceof Node ) {

                            $fileHandle->fwrite(
                                $sectionChild->getAttribute('name').'='.
                                ( $sectionChild->hasAttribute('value') ? $sectionChild->getAttribute('value') : '' )
                            );

                            foreach ( $sectionChild->childNodes as $propertyChild ) {

                                if ( $propertyChild instanceof Comment ) {

                                    $fileHandle->fwrite(
                                        ' ;'.$propertyChild->nodeValue
                                    );

                                }

                            }

                            $fileHandle->fwrite(PHP_EOL);

                        }

                    }

                }

            }

        }

        $fileHandle->rewind();

        return $fileHandle;
    }

    /**
     * aggregateDocument() - helper method
     * this method is internally used to decouple the process of document instancing from the rest of the
     * constructor.
     *
     * @param $namespace
     * @return \DOMDocument
     */
    protected function aggregateDocument($namespace)
    {
        $document = new Document('1.0', 'utf-8', $namespace);
        $root = $document->createElementNS($namespace, 'ini:ini');

        $document->appendChild($root);

        return $document;
    }
}