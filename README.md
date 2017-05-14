# Macdom
[![Build Status](https://travis-ci.org/Machy8/Macdom.svg?branch=2.0)](https://travis-ci.org/Machy8/Macdom)
[![Packagist](https://img.shields.io/packagist/v/machy8/macdom.svg?maxAge=2592000)](https://packagist.org/packages/machy8/macdom)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/Machy8/Macdom/blob/master/license.md)
[![Join the chat at https://gitter.im/Machy8/Macdom](https://badges.gitter.im/Machy8/Macdom.svg)](https://gitter.im/Machy8/Macdom?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

- For more information see [WIKI](https://github.com/Machy8/Macdom/wiki) *(always up to date to the newest stable version)*
- **[Try it on CODEPEN](http://codepen.io/Machy8/pen/mPLdbg)** *(JavaScript version is always behind the php version so it may contain bugs and errors that have already been fixed in the PHP version)*

## Sample

**Macdom**
```` Slim
!5
html
head
	utf-8
	viewport
	favicon includes/favicon.ico
	title Macdom example
body
	h1 #title .titles .main-title Hello world
	nav
		@ a $http://www.[@].com $blank Link on -
		[google] .first-link Google
		[yahoo] Yahoo
		[github] Github	
	div #wrapper Some text <b>here</b>.
	https://www.code.jquery.com/jquery-1.12.0.min.js async
	
````

**Result**
```` html
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width" name="viewport">
		<link rel="shortcut icon" href="includes/favicon.ico">
		<title>Macdom example</title>
	</head>
	<body>
		<h1 id="title" class="titles main-title">Hello world</h1>
		<nav>
			<a target="blank" href="http://www.google.com" class="first-link">Link on - Google</a>
			<a target="blank" href="http://www.yahoo.com">Link on - Yahoo</a>
			<a target="blank" href="http://www.github.com">Link on - Github</a>
		</nav>
		<div id="wrapper">
			Some text <b>here</b>.
		</div>
		<script async="" type="text/javascript" src="https://www.code.jquery.com/jquery-1.12.0.min.js"></script>
	</body>
</html>
````

## Requirements
- php 5.4+
- If you use Nette Framework - v2.3+

## Installation
**1 - Download the Macdom using composer:**
```
 composer require machy8/macdom
```
**2 - Usage:**

*Typical:*

```php
$macdom = new Machy8\Macdom\Loaders\Loader;
$compiled = $macdom->compile($content);
```

*Nette framework:*

Place the code bellow into your *base presenter*.

```php
protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->getLatte()->setLoader(new \Machy8\Macdom\Loaders\LoaderLatte($this));
        return $template;
    }
```
