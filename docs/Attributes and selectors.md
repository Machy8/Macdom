# Attributes and selectors
- Html attributes have equal syntax to classic html attributes.
- Class and id shortcut selectors have equal syntax as in css.
- Boolean attributes are used in the same way as it is in html.

* id = **#**
* class = **.**
* data = **-**

## Class selector
Class selectors are synchronized with html class attribute.

```` Slim
div .myClass => <div class="myClass"></div>
div .myClass .myClass2 => <div class="myClass myClass2"></div>
div .myClass class=myClass2 => <div class="myClass myClass2"></div>
````

## Id selector
Id selector is not the primary selector and is removed if there is an html id attribute on the same line.

```` Slim
div #myId => <div id="myId"></div>
div #myId id=myId2 => <div id="myId2"></div>
````

## Data selector
Data selectors are not synchronized with the same attribute written in html. 

```` Slim
div -myData=someData => <div data-myData=someData></div>
div -myData='Some data' => <div data-myData='Some data'></div>
div -myData="Some data" => <div data-myData="Some data"></div>
````

## Html attributes
Html attributes have the same syntax as in html. Class attributes are synchronized with short selectors (.class).

```` Slim
div id=myId => <div id=myId></div>
div id='myId' => <div id='myId'></div>
div id="myId" => <div id="myId"></div>
div class=myClass .myClass2 => <div class="myClass myClass2"></div>
````

## Boolean attributes 
You can add another boolean attributes via `$macdom->addBooleanAttribute('attribute1 attribute2)`.

```` Slim
input $text disabled readonly => <input type="text" disabled readonly>
````

## Quick attributes
Two options
* For short values or values without spaces use *$attributeValue*.
* For long values like text in a placeholder use *$attribute value;* (closed by a semicolon).

You can change element quick attributes by calling `$macdom->changeElementQuickAttributes('element', 'attributeA attributeB')`.

### Default settings for each element

| Element  | Attributes                                      |
|----------|-------------------------------------------------|
| a         | href, target, role                              |
| abbr      | title                                           |
| audio     | src                                             |
| base      | href                                            |
| bdo       | dir                                             |
| button    | type, value                                     |
| canvas    | width, height                                   |
| col       | span                                            |
| data      | value                                           |
| embed     | src, type, width, height                        |
| form      | action, method, target                          |
| html      | lang                                            |
| iframe    | src, frameborder, width, height                 |
| img       | src, alt                                        |
| input     | type, value, placeholder                        |
| label     | for                                             |
| link      | href, rel, title                                |
| meta      | name, content                                   |
| object    | data, type                                      |
| ol        | type, start, reversed                           |
| optgroup  | label                                           |
| option    | value                                           |
| param     | name, value                                     |
| progress  | value, max                                      |
| q         | cite                                            |
| script    | src, type                                       |
| source    | src, type                                       |
| style     | type                                            |
| td        | rowspan, colspan                                |
| textarea  | placeholder                                     |
| track     | src, srclang, kind                              |
| menu      | type, label                                     |
| menuitem  | type                                            |
| xml       | version encoding                                |
| cloud     | domain, port, path, registerProcedure, protocol |
| enclosure | url                                             |
| rss       | version                                         |
| source    | url                                             |

### Example with "a" tag

```
qkAttributes: ['href', 'target', 'role'] => a $www.google.com $blank Text parameter
qkAttributes: ['target', 'href', 'role'] => a $blank $www.google.com Text parameter
```

If you want to add for example only the third attribute you can write it like this:
``` Slim
a $null $null $button Just a button
```

or use the index of the attribute:
``` Slim
a 3$button Just a button
```

Is possible to combine those options:
``` Slim
input $text 3$Placeholder here;
```