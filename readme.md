---
Leaf/Ini
===

### What is **Leaf/ini** ?
Leaf/ini is a document-based ini-file component for PHP that adopts all features from DOM, SplFileInfo and SplFileObject. The intention of Leaf/ini is to stop the lack of support of the available ini-parser ( including the php commands parse_ini_file and parse_ini_string ), all of them wasn't able to handle multiple entries of the same key inside of a ini-section. The major goal of this component was to build a solid component to access non-strict defined INI-Files like php.ini of the PHP-Package.

### Installation with **Composer**
Leaf/ini is available from github and via composer. Just add `"leaf/ini": "~1.0"` to your `composer.json`:

    {
        "require": {
            "leaf/ini": "~1.0"
        }
    }
or use the require-command of composer: `composer require leaf/ini:~1.0`

> *Notice:* Composer installs the component into the vendor folder located in the same destination as your composer.json. The package-file of this library will force composer to install all files into `<vendor>/leaf/leaf/components/ini/`. This step is required for the autoloading mechanism of composer to resolve the `Leaf\Components\Ini`-namespace properly.

### Requirements and Dependencies
Leaf/ini is compatible with **PHP 5.3** or higher and needs no external libraries or php modules. There is also no compability issue for future changes to `preg_*`-methods, this component is using string streams. The following PHP Classes are used by Leaf/ini and should not be disabled:

 - DOMDocument
 - DOMElement
 - DOMComment
 - SplFileInfo
 - SplFileObject

### Usage / Examples
To start with Leaf/ini you have to import the IniFileObject-Class to your namespace ( all example of this document will import them to the global namespace ). You have to start with instancing the class and preparing a file-destination ( no matter if the target file exist or not ). 

#### Inspecting the DOM result of a existing file
Just open a file and check the aggregated DOM result of the parsed `.ini`-file:

    <?php
    
    # include autoloader
    require 'vendor/autoload.php';
    
    # merge IniFileObject to this namespace
    use Leaf\Components\Ini\IniFileObject;
    
    # instance InIFileObject to access php.ini
    $iniFile = new IniFileObject('php.ini');
    
    # (not required) do not preserve white spaces:
    $iniFile->getContent()->getDocument()->preserveWhiteSpace = false;
    
    # (not required) please format output:
    $iniFile->getContent()->getDocument()->formatOutput = true;
    
    # send xml header to the browser:
    header('Content-Type: application/xml; charset=utf-8');
    
    # send the XML:
    $iniFile->saveXML('php://memory')->fpassthru();

The result will look like [this][1].
#### Exporting all sections (trimmed) to an array

    <?php
    
    #include autoloader
    require 'vendor/autoload.php';
    
    # merge IniFileObject to this namespace
    use Leaf\Components\Ini\IniFileObject;
    
    # instance IniFileObject to access php.ini
    $iniFile = new IniFileObject('php.ini');
    
    # trim all property names and property values
    $iniFile->getContent()->trimKeysAndValues();
    
    # export all sections to an array
    var_dump( $iniFile->getContent()->asArray() );

The result will look like [this][2].

#### Exporting a single section (trimmed) to an array

    <?php
    
    #include autoloader
    require 'vendor/autoload.php';
    
    # merge IniFileObject to this namespace
    use Leaf\Components\Ini\IniFileObject;
    
    # instance IniFileObject to access php.ini
    $iniFile = new IniFileObject('php.ini');
    
    # trim all property names and property values
    $iniFile->getContent()->trimKeysAndValues();
    
    # export all sections to an array
    var_dump( $iniFile->getContent()->getSection('PHP')->asArray() );

The result will look like [this][3]. This also works in the same way with properties. `$iniFile->getContent()->getSection('PHP')->getProperty('engine')->asArray();` will export the `engine`-property of the `PHP`-section as an array ( `[ 'engine' => 'On' ]` ).

### API Reference

**Leaf\Components\Ini\IniFileObject**
> IniFileObject::__construct( *string* **fileName** , string **namespace** = `...` )

Parameters

 - **fileName** - *the file that should be prepared to open, if the file exists the file will be parsed*
 - **namespace** - *the namespace that should be used, defaults to `http://leaf.virtual-ns/2014/file-type/ini`*

This is the Constructor of this class. A **fileName** must be specified, the **namespace** will be used for the `ini:*`-prefix of the XML document.

----------

> IniFileObject::getContent()

Returns

 - instance `Leaf\Components\Ini\Collections\SectionsCollection`

This method can be used to access the content of the nodes.

----------

> IniFileObject::saveXml( *string* **fileName**, *DOMDocument* **stylesheet** = `null`, *XSLTProcessor* **xslt** = `null` )

Parameters

 - **fileName** - *the filename to store the XML-Document*
 - **stylesheet** - *a optional stylesheet loaded into a `DOMDocument`-instance to apply to the document*
 - **xslt** - *a optional alternative configured XSLTProcessor for the given stylesheet*

Returns

 - instance `SplFileObject` part of the PHP core and [documented here][4].

This is a storage method and fully compatible with all [writable stream wrappers of php][5].
 
----------

> IniFileObject::save( *string* **fileName** = `null` )

Parameters

 - **fileName** - *if not null, this fileName will be used to store the DOM extraction as a ini file. If null the original name passed to the constructor will be used*

Returns

 - instance `SplFileObject` part of the PHP core and [documented here][6].

This is a storage method and fully compatible with all [writable stream wrappers of php][5].

----------

**Leaf\Components\Ini\DocumentObjects\Node** - *extends* `DOMElement`
> Node::isSection( *string* **name** = `null` )

Parameters

 - **name** - *if not null, this string will be used for section-validation, otherwise the current node is the target*

Returns

 - type of `boolean`

This method can be used to check if a given or the current element is a ini section.

----------

> Node::isGlobalSection()

Returns

 - type of `boolean`

This method can be used to check if the current element is the ini global section.

----------

> Node::isProperty( *string* **name** = `null` )

Parameters

 - **name** - *if not null, this string will be used for property-validation, otherwise the current node is the target*

Returns

 - type of `boolean`

This method can be used to check if the current element is a property.

----------

> Node::isRoot()

Returns

 - type of `boolean`

This method can be used to check if the current element is the root element of the document.

----------

> Node::hasValue()

Returns

 - type of `boolean`

Throws

 - `DOMException`

This method can be used to check if the current property has a value. Throws a `DOMException` if not called from a property-node.

----------

> Node::hasComments()

Returns

 - type of `boolean`

This method can be used to check if the current property, section or global section has comments.

----------

> Node::hasProperty( *string* **name** )

Parameters

 - **name** - *this string will be used as the property name*

Returns

 - type of `boolean`

This method can be used to check if the current section or global section has a property with name **name**.

----------

> Node::getProperty( *string* **name** )

Parameters

 - **name** - *this string will be used as the property name*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node` or type of `null`

This method can be used to get a property with name **name**.

----------

> Node::setValue( *string* **value** )

Parameters

 - **name** - *this string will be used as the value content for a property*

This method can be used to set a property value with content **value**.

----------

> Node::getValue()

Returns

 - type of `string` or type of `null`

Throws

 - `DOMException`

----------

> Node::hasSection( *string* **name** )

Parameters

 - **name** - *this string will be used as the section name*

Returns

 - type of `boolean`

This method can be used to check if the section **name** is already registered to the document.

----------

> Node::getSection( *string* **name** )

Parameters

 - **name** - *this string will be used as the section name*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node` or type of `null`

This method can be used to a section with name **name**.

----------

> Node::hasGlobalSection()

Returns

 - type of `boolean`

This method can be used to check if the current node is a ini global section.

----------

> Node::getGlobalSection()

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method can be used to get the global-section node.

----------

> Node::addGlobalSection()

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This is a public reachable internal method to implement the global section and should not be used, the `Leaf\Components\Ini\Collections\SectionsCollection` will do the implementation of the global section for you.

----------

> Node::addSection( *string* **name** )

Parameters

 - **name** - *This string will be used as the name of the created section*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method can be used to create a new section with name **name**.

----------

> Node::addProperty( *string* **name**, *string* **value** = `null` )

Parameters

 - **name** - *This string will be used as the name of the created property*
 - **value** - *This string will be used as the value of the created propery, if null the property has no value*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method can be used to create a new property with name **name** on a section node or global-section node.

----------

> Node::addSpace()

Returns

 - instance `Leaf\Compoennts\Ini\DocumentObjects\Comment`

This method can be used to create a new empty comment on a section, global section or root node. Empty comments are evaluated as empty lines inside of a saved ini file.

----------

> Node::dropComments( *boolean* **deep** = `false` )

Parameters

 - **deep** - *This boolean will be used to engage or prevent a deep scan for comments. If `false` only the current level will be scanned, if `true` all nodes of the content will be scanned recursively*

Returns

 - type of `integer` - *representing the count of deleted comment-nodes*

This method can be used to delete all comment-nodes of a section node, a global section node or the entire document.

----------

> Node::Delete()

This method can be used to delete a section, global section or property.

----------

> Node::getAllSections()

Returns

 - instance `DOMNodeList` - *representing all sections of the document*

This method can be used to get a `DOMNodeList` of all sections of the ini file.

----------

> Node::getAllProperties()

Returns

 - instance `DOMNodeList` - *representing all sections in context of the current node ( Root / Section / Global Section )*

This method can be used to get a `DOMNodeList` for all properties of a section or the entire document.

----------

**Leaf\Components\Ini\DocumentObjects\Document** *extends* `DOMDocument`
> Document::__construct( *string* **version**, *string* **encoding**, *string* **namespace** )

Parameters

 - **version** - *This string will be used as the XML version of the document*
 - **encoding** - *This string will be used as the XML encoding of the document*
 - **namespace** - *This string will be used as the namespace of the document root*
 
This method is the Constructor of this class and instances the internal used `DOMXPath`. On each construction all node classes of this component will be registered to the `DOMDocument`-instance and the namespace *namespace* to the `DOMXPath`-instance.

----------

> Document::getXPathObject()

Returns

- instance *of a connected* `DOMXPath`

This method can be used to get the connected `DOMXPath` instance for the document.

----------

**Leaf\Components\Ini\DocumentObjects\Comment** *extends* `DOMComment`
> Comment::setComment( *string* **string** )

Parameters

 - **string** - *This string will be used as the comment content*

This method can be used to set the content of a comment.

----------

> Comment::getComment()

This method can be used to get the content of a comment.

----------

**Leaf\Components\Ini\Collections\SectionsCollection**
> SectionsCollection::__construct( *Leaf\Components\Ini\DocumentObjects\Document* **dom** )

Parameters

 - **dom** - *Instance of `Leaf\Components\Ini\DocumentObjects\Document` used for this collection*

This is the constructor of this class. The constructor delegates the `DOMXPath` objects to his scope.

----------

> SectionsCollection::getRoot()

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method can be used to get the root-node of the document.

----------

> SectionsCollection::getSection( *string* **name** )

Parameters

 - **name** - *This string will be used as the section name*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node` or type of `null`

This method is a wrapper of `Leaf\Components\Ini\DocumentObjects\Node::getSection()` on the root node.

----------

> SectionsCollection::hasSection( *string* **name** )

Parameters

 - **name** - *This string will be used as the section name*

Returns

 - type of `boolean`

This method is a wrapper of `Leaf\Components\Ini\DocumentObjects\Node::hasSection()` on the root node.

----------

> SectionsCollection::addSection( *string* **name** )

Parameters

 - **name** - *This string will be used as the section name*

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method is a wrapper of `Leaf\Components\Ini\DocumentObjects\Node::addSection()` on the root node.

----------

> SectionsCollection::getGlobalSection()

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method is a wrapper of `Leaf\Components\Ini\DocumentObjects\Node::getGlobalSection()` on the root node with an cascading behavior that automaticly adds the global-section node to the document if not exists.

----------

> SectionsCollection::getDocument()

Returns

 - instance `Leaf\Components\Ini\DocumentObjects\Node`

This method can be used to access the `Leaf\Components\Ini\DocumentObjects\Node`-instance of this collection.

----------

> SectionsCollection::asArray( *string* **propertyDecoupleSeparator** = `null`, *string* **globalKey** = `*` )

Parameters

 - **propertyDecoupleSeparator** - *This string will be used as the decouple separator, if null decoupling is disabled*
 - **globalKey** - *This string will be used as the representive name of the global section inside of the array, defaults to `*`*

Returns

 - type of `array`

This method can be used to aggregate an associative multi-dimensional array of all sections and properties. If the **propertyDecoupleSeparator** is not null the value of this parameter will be used to split the property name into array segments. The **globalKey** will be used as the representive name of the global section, defaults to `*`.

----------

> SectionsCollection::asMappedArray( *Callable* **callback**, *string* **propertyDecoupleSeparator** = `null`, *string* **globalKey** = `*` )

Parameters

 - **callback** - *This parameter with type of `Callable` ( pseudo-interface of PHP for callbacks ) will be used as the method executed on all nodes before inserting them into the new destination*
 - **propertyDecoupleSeparator** and **globalKey** works in the way defined in the `asArray()`-Method of this class.

Returns

 - type of `array`

This method can be used to aggregate an associative multi-dimensional array of all sections and properties. The callback will be executed on **all nodes**. This behaviour is additional to the behavior of the `asArray()` method of this class.

----------

> SectionsCollection::dropAllComments()

Returns

 - instance `this` => `Leaf\Components\Ini\Collections\SectionsCollection`

This method can be used to drop all comments form all nodes on the current document.

----------

> SectionsCollection::mapNodesWith( *Callable* **callback**, *string* **query** = `//*` )

Parameters

 - **callback** - *This parameter with type of `Callable` ( pseudo-interface of PHP for callbacks ) will be used as the method executed on all given nodes*
 - **query** - *This parameter will be used as the XPath query string, defaults to `//*` ( all nodes on all levels )*

Returns

 - instance `this` => `Leaf\Components\Ini\Collections\SectionsCollection`

This method can be used to manipulate nodes of the current document. The **callback**-Callable will be executed on the nodes respondedfrom the XPath-query **query**.

----------

> SectionsCollection::trimKeysAndValues()

Returns

 - instance `this` => `Leaf\Components\Ini\Collections\SectionsCollection`

This method wraps the `mapNodesWith`-method and executes `Leaf\Components\Ini\Mapper\TrimMapper` on all nodes.

----------

> SectionsCollection::queryXPath( *string* **query**, *Leaf\Components\Ini\DocumentObjects\Node* **node** = `null` )

Parameters

 - **query** - *This string will be used as the XPath query*
 - **node** - *This parameter with type of `Leaf\Components\Ini\DocumentObjects\Node` will be used as the execution context of the query and defaults to the root node of the document`

Returns

 - instance `DOMNodeList`

This method can be used to query the document with assistance of XPath. If a **node** is given the query will only affect subnodes of this node.

----------

Internal Classes ( that are not part of the public API ):
 - `Leaf\Components\Ini\Mapper\TrimMapper` is a internal class and will not be documented along this documentation. This class is in general an on class level defined Callable and implements only a constructor and an `__invoke()`-method.
 - `Leaf\Components\Ini\Parser\LineParser` is the parser core of this component and will not be documented along this documentation.
 - `Leaf\Components\Ini\Arregator\ArrayAggregator` is a internal class and will not be documented along this documentation.
             
#### License

**The MIT License (MIT)**

Copyright (c) 2014 Matthias Kaschubowski

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

#### Thanks to

 - **riano** - *for suggestions and being there for discussions about this library*


  [1]: https://gist.github.com/Golpha/8da6f1585acb44617ffe#file-php-ini-as-xml
  [2]: https://gist.github.com/Golpha/8da6f1585acb44617ffe#file-php-ini-as-array
  [3]: https://gist.github.com/Golpha/8da6f1585acb44617ffe#file-php-ini-single-section-as-array
  [4]: http://php.net/splfileobject
  [5]: http://php.net/manual/en/wrappers.php.php
  [6]: http://php.net/splfileobject