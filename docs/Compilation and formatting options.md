# Compilation and formatting options

## Skipped areas
If you want to switch off macdom compilation simply nest the code into `macdom-off` tag.
Default ignored areas are *style, script, textarea, code, php*.

````
div
    macdom-off
        div
            div
                div
````

**Output**
```` html
<div>
        div
            div
                div
</div>
````

## Output formatter
Macdom is using [Xhtml formatter](https://github.com/Machy8/xhtml-formatter). 
If you don't want to format the output code, you can switch off the formatter by wrapping your by `<formatter-off></formatter-off>` tag 
or by calling the method `$macdom->disableOutputFormatter()` to disable the output formatter entirely.

````
<div>
	<formatter-off>
		<p>
			<b>Unformatted code goes <u>here</u></b>!
		</p>
	</formatter-off>
</div>
````
