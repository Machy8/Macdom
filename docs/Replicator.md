# Replicator
Replicator replicates lines content according to levels. Lines are replicated only, if the actual compiled line
is not within non-compile area and an html element wasn't detected.

## How to use replicator
**Constructor:**
 1. @ - Only *at*
 2. @my - *at* + *selector*
 
**Destructor:**
 1. /@ 
 2. /@selector

### @
If there is only *@* at the beginning of the actual compiled line then its content will be used on each following line 
that is on the same level, is not empty and is without the registered line selector.

*Example:*
````
@ div .block |
First
Second
Third
````

*Result:*
```` HTML
<div class="block">First</div>
<div class="block">Second</div>
<div class="block">Third</div>
````
Replicator also allows you to use variables in the registered content. 
Each "variable place" is marked with *[@]* on the registered line
and the content that will replace this mark must be within *[]*. 

*Example:*
````
@ div #[@] .block |
[first] First
[second] Second
[third] Third
````

*Result:*
```` HTML
<div id="first" class="block">First</div>
<div id="second" class="block">Second</div>
<div id="third" class="block">Third</div>
````

If there is more than one "variable place" then "replacements" must be in that order
in which they are on the registration line. If one variable place should be skipped, then simple add *[]*.

*Example:*
````
@ div #[@] .block[@] |
[first] [1] First
[second] [2] Second
[third] [3] Third
````

*Result:*
```` HTML
<div id="first" class="block1">First</div>
<div id="second" class="block2">Second</div>
<div id="third" class="block3">Third</div>
````

### @ + selector
If the registered line has a selector then the line is replicated on the same indentation level
and also in the more nested levels but only if there is the selector on the beginning of the actual compiled line.

*Example:*
````
@block div .block |
block First
Second
block Third
    block Fourth
        block Fifth
````

*Result:*
```` HTML
<div class="block">First</div>
Second
<div class="block">
    Third
    <div class="block">
        Fourth
        <div class="block">
            Fifth
        </div>
    </div>
</div>
````

### Destructor
Destructor is used when we want to deregister line content 
and it must be on the same level on which the line content was registered.

*Example:*
````
@ div .registered |
Text inside 1
Text inside 2
/@
Just a text
````

*Result:*
```` HTML
<div class="registered">Text inside 1</div>
<div class="registered">Text inside 2</div>
Just a text
````

## Other examples

### Combination
````
@ div .a
@block div .b |
A1
block B1
A2
block B2
````

*Result:*
```` HTML
<div class="a">A1</div>
<div class="b">B1</div>
<div class="a">A2</div>
<div class="b">B2</div>
````

### With different levels
````
@column div .column
    @ h1 .title |
    @content p .text |
column
   Title 1
   content Text 1
column
   Title 2
   content Text 2
````

*Result:*
```` HTML
<div class="column">
    <h1 class="title">Title 1</h1>
    <p class="text">Text 1</p>
</div>
<div class="column">
    <h1 class="title">Title 2</h1>
    <p class="text">Text 2</p>
</div>
````
