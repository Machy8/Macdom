#Macdom for Nette
HTML Abstraction markup language.
##Requirements
Nette version 2.3 or newer.
##Usage
1 - Download the Macdom using composer:
```
 composer require machy8/macdom:@dev
```
2 - Register the Macdom in the **config.neon** as an extension:
```
extensions:
	macdom: Machy8\Macdom\MacdomExtension
```
3 - Place the code bellow into your **base presenter**:
```
protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->getLatte()->setLoader(new \Machy8\Macdom\Loader($this));
        return $template;
    }
```
##Syntax
Each attribute must be separated by space. Each tag must be separated by line break.
###Tag
When you want to create for example a div tag simply write **div**.
There can be only one tag on each line.
You can add and remove Elements in the **Elements** class in **setElements()** => **elements[]**.
###Id attribute
Id attribute has a hash tag before and there can be only one on each line **#id** (Macdom will use only the first one).
###Class selectors
To add a class to an element write **.yourClass**.
For multiple classes use space to separate them:
```
.first .second .xxx
```
###HTML attributes
The same as in HTML.
###Text content
There are two ways
* On the same line as the tag is => good for short text => text in a link (do not use HTML tags here).
* On the next line => long text => paragraph in an article.

###Quick attributes
Two options
* For short values or values without spaces use **$attributeValue**.
* For long values like text in a placeholder use **$attribute value;** (closed by a semicolon).

The number and order of quick attributes is given by settings in the **Elements** class in **setElementsSettings() => qkAttributes[]** arrays. Please feel free to modify the settings.
For example an **a** tag:
```
qkAttributes: ['href', 'target', 'role'] => a $www.google.com $blank Text parameter
qkAttributes: ['target', 'href', 'role'] => a $blank $www.google.com Text parameter
```
If you want to add for example only the third attribute you can write it like this:
```
a $null $null $button Just a button
```
or use the index of the attribute:
```
a 3$button Just a button
```
Is posible to combine those options:
```
input $text 3$Placeholder here;
```
###N:href
It is posible to write **n:href=""** in similar syntax like quick attribute but only with 'n' before and semicolon on the end. Example:
```
n$Article:show $id, lang => cs;
```
###Boolean attributes
This is how I call one-word attributes with a boolean value in Macdom. For example **disabled, autofocus, etc…**.

For example:
```
textarea #myId disabled Just a text inside

textarea disabled #myId readonly .myClass Just a text inside
```
You can modify them in the **Elements** class in **setBooleanAttributes() => booleanAttributes[]**.
###Levels
* 4 spaces (default - depends on a number of spaces in one tabulator) = one level
* 1 tabulator = 8 spaces = 2 levels

You can change the number of spaces in the **Compiler** class. 
```
div (0 tabulators and spaces)
  div (4 spaces before)
    div (1 tabulator)
      div (1 tabulator + 4 spaces)
        div (2 tabulators)
  ```
###Skip areas
Skip tags are used on places, where we dont want to compile the code. The **skip** tag cannot be in another skip tag. You can change the name of this tag in the **Compiler** class.
```
SKIP
  content is not being compiled
/SKIP
```
Other ignored areas in basic settings: script, style
##Examples
See examples in the “examples” directory. There are examples on everything that is possible to do with Macdom. Just copy the content of those files into **@layout.latte** and see what happened.
