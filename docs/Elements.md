# Elements

**Standard HTML elements:**
- a, abbr, address, area, article, aside, audio
- b, base, bdi, bdo, blockquote, body, br, button
- canvas, caption, cite, code, col, colgroup
- data, datalist, dd, del, details, dfn, div, dl, dt
- element, em, embed
- fieldset, figcaption, figure, footer, form
- h1, h2, h3, h4, h5, h6, head, header, hr, html
- i, iframe, img, input, ins
- kbd
- label, legend, li, link
- main, map, mark, meta, meter
- nav, noframes, noscript
- object, ol, optgroup, option, output
- p, param, pre, progress
- q
- rp, rt, rtc, ruby
- s, samp, script, section, select, shadow, small, source, span, strong, style, sub, summary, sup
- table, tbody, td, template, textarea, tfoot, th, thead, time, title, tr, track
- u, ul
- var, video
- wbr
    
**Experimental and not standardized API elements**
- bgsound, dialog, hgroup, image, menu, menuitem, nobr, picture
    
**Default XML elements**
- xml,
- category, channel, cloud, copyright
- day, description, docs
- enclosure
- generator, guid
- hour
- image, item
- language, lastBuildDate, link
- managingEditor
- pubDate
- rating, rss
- skipDays, skipHours, source
- textInput, title, ttl
- url
- webmaster

## Managing elements 
- Adding - `$macdom->addElement()`
- Removing - `$macdom->removeElement('element', Engine::CONTENT_HTML)`

``` php
    $macdom->addElement('element', [
        Engine::CONTENT_XML,
        Engine::UNPAIRED_ELEMENT,
        Engine::CONTENT_SKIPPED,
        'quickAttributes' => ['data-some-data'],
        'openTags' => ['<?'],
        'closeTags' => ['?>'],
    ]);
``` 
- Engine::CONTENT_SKIPPED => Whole tag and its content will be skipped
- Engine::CONTENT_XML => This element will be used only in xml content type
- Engine::CONTENT_XHTML => This element will be used in xml and html content type
- Engine::UNPAIRED_ELEMENT => Element is unpaired
