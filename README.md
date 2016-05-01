#Macdom
[![Version](https://img.shields.io/badge/stable-2.0.0-brightgreen.svg)](https://github.com/Machy8/Macdom/releases/tag/v2.0.0)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/Machy8/Macdom/blob/master/license.md)
[![Join the chat at https://gitter.im/Machy8/Macdom](https://badges.gitter.im/Machy8/Macdom.svg)](https://gitter.im/Machy8/Macdom?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

- For more information see [WIKI](https://github.com/Machy8/Macdom/wiki) *(always up to date to the newest stable version)*
- **[Try it on CODEPEN](http://codepen.io/Machy8/pen/mPLdbg)** *(JavaScript version is always behind the php version so it may contain bugs and errors that have already been fixed in the PHP version)*

##Requirements

- php 5.3+
- If you are using Nette Framework - v2.3+

##Installation
**1 - Download the Macdom using composer:**
```
 composer require machy8/macdom
```
**2 - Usage:**

*Normal:*

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
