# Compilation and formatting options

## Setting content type
- Setting content type - `$macdom->setContentType(Engine::CONTENT_XHTML)` with one of the following options.
Content type options
 - Engine::CONTENT_HTML => html elements
 - Engine::CONTENT_XHTML => xhtml elements, xml syntax
 - Engine::CONTENT_XML => xml elements, xml syntax

## Skipped areas
If you want to switch off Macdom compilation simply nest the code into `macdom-off` tag.
Default ignored areas are *style, script, textarea, code, php*.

```` SLIM
div
    macdom-off
        div
            div
                div
````

**Output**
```` HTML
<div>
        div
            div
                div
</div>
````

## Output formatter
Macdom is using [Xhtml formatter](https://github.com/Machy8/xhtml-formatter). 
If you don't want to format the output code, you can switch off the formatter
by wrapping your code by `<formatter-off></formatter-off>` tag or by calling the method 
`$macdom->disableOutputFormatter()` to disable the output formatter entirely.

```` HTML
<div>
	<formatter-off>
		<p>
			<b>Unformatted code goes <u>here</u></b>!
		</p>
	</formatter-off>
</div>
````
