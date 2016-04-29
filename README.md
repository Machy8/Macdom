#Macdom
HTML preprocessor.

For more information see [WIKI](https://github.com/Machy8/Macdom/wiki)

##Requirements

- php 5.3+
- If you are using Nette Framework - v2.3+

##Installation
1 - Download the Macdom using composer:
```
 composer require machy8/macdom
```
2 - Usage:

**Normal:**

```php
$macdom = new \Machy8\Macdom\Loaders\Loader;
$compiled = $macdom->compile($content);
```

**Nette framework:**

Place the code bellow into your *base presenter*.

```php
protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->getLatte()->setLoader(new \Machy8\Macdom\Loaders\LoaderLatte($this));
        return $template;
    }
```
