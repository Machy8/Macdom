#Macdom
HTML and template preprocessor.

For more information see [WIKI](https://github.com/Machy8/Macdom-for-Nette/wiki)

##Requirements

- php 5.3+
- If you are using Nette Framework: Nette v2.3 or newer.

##Installation
1 - Download the Macdom using composer:
```
 composer require machy8/macdom
```
2 - Usage:

**Normal:**

```
$macdom = new \Machy8\Macdom\Loader;
$compiled = $macdom->compileContent($content);
```

**Nette framework:**

Place the code bellow into your *base presenter*.

```
protected function createTemplate()
    {
        $template = parent::createTemplate();
        $template->getLatte()->setLoader(new \Machy8\Macdom\LoaderLatte($this));
        return $template;
    }
```
