# Macros
Macros in Macdom are "prearranged code".

## Default macros

| Input                         | Output                                                             |
|-------------------------------|--------------------------------------------------------------------|
| !5                            | \<!DOCTYPE html>                                                   |
| !DOCTYPE html                 | \<!DOCTYPE html>                                                   |
| charset utf-8                 | \<meta charset="utf-8">                                            |
| utf-8                         | \<meta charset="utf-8">                                            |
| keywords keyword1, keyword2   | \<meta name="Keywords" content="keyword1, keyword2">               |
| description Short description | \<meta name="Description" content="Short description">             |
| author Vladimír Macháček      | \<meta name="Author" content="Vladimír Macháček">                  |
| viewport width=device-width   | \<meta name="viewport" content="width=device-width">               |
| index-follow                  | \<meta name="robots" content="index, follow">                      |
| no-index-follow               | \<meta name="robots" content="noindex, nofollow">                  |
| fb title Short title          | \<meta property="og:title" content="Short title">                  |
| tw creator @8machy            | \<meta name="twitter:creator" content="8machy">                    |
| css path/to/style.css         | \<link rel="stylesheet" type="text/css" href="path/to/style.css">  |
| favicon favicon.ico           | \<link rel="shortcut icon" href="favicon.ico">                     |
| js somescript.js              | \<script type="text/javascript" src="somescript.js"></script>      |
| js-async somescript.js        | \<script type="text/javascript" src="somescript.js" async></script>|
| // Inline comment here        | \<!-- Inline comment here -->                                      |
| /* Openning html comment      | \<!--                                                              |
| */ Closing html comment       | \-->                                                               |
| !xml                          | <?xml version="1.0" encoding="UTF-8" ?>                            |
| \.js$                         | script $' . $keyword . ' $text/javascript ' . $line                |
| \.css$                        | link $' . $keyword . ' $stylesheet $text/css ' . $line             |

## Managing macros
- Adding `$macdom->addMacro()`
- Removing `$macdom->removeMacro('macroname', Engine::CONTENT_HTML)`

```` PHP
$quickList = function ($content) {
    $listItems = explode(' ', $content);
    $list = '<ul>';

    foreach ($listItems as $listItem) {
        if ( ! $listItem) {
            continue;
        }

        $list .= '<li>' . $listItem . '</li>';
    }

    $list .= '</ul>';

    return $list;
};

$macdom
    ->addMacro('sectionTitle', function ($content) {
        return '<h1 class="sectionTitle">' . $content . '</h1>';
    })
    ->addMacro('quickList', $quickList, [Engine::CONTENT_XML]);
````
