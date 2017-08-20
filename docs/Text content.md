# Text content

**Two options**

* On the same line as the tag is => good for a short text => text in a link
* On the next nested line => long text => paragraph in an article
* Or combination of previous options
* Use **|** to preserve trailing space on the end of the line

*Example:*
``` Slim
// 1
b Bold

// 2
b
  Bold

// 3
span <b>Bold</b>

// 4
span
  <b>Bold</b>

// 5
span Some <b>bold</b> text.

// 6
span Some
  <b>bold</b>
  text.

// 7
span Some |
  <b>bold</b> |
  text.
```

*Result:*
``` html
// 1. - 2.
<b>
    Bold
</b>

// 3. - 4.
<span>
    <b>
        Bold
    </b>
</span>

// 5. - 7.
<span>
    Some
    <b>
        bold
    </b>
    text.
</span>
```
