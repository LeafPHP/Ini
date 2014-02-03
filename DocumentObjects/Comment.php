<?php
/**
 * ini file component of leaf
 * Developer: tr0y
 * Date: 01.02.14
 */

namespace Leaf\Components\Ini\DocumentObjects;

use DOMComment;

/**
 * Class Comment
 * DOM DOMComment replacement
 *
 * @package Leaf\Components\Ini\DocumentObjects
 * @version 1.0.0
 */
class Comment
    extends DOMComment
{
    /**
     * setComment()
     * sets the comment content.
     *
     * @param $string
     */
    public function setComment($string)
    {
        $this->nodeValue = (string) $string;
    }

    /**
     * getComment()
     * gets the comment content.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->nodeValue;
    }
} 