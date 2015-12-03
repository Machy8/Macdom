#Macdom for Nette
HTML Abstraction markup language.

For more information see [WIKI](https://github.com/Machy8/Macdom-for-Nette/wiki)

##Requirements
Nette version 2.3 or newer.
##Note to develop version
May contain errors and bugs. Use Macdom from the Master branch for your projects.
##Installation
1 - Download the Macdom using composer:
```
 composer require machy8/macdom:dev-develop
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

##Examples
See examples in the “examples” directory. There are examples on everything that is possible to do with Macdom. Just copy the content of those files into **@layout.latte** and see what happened.
