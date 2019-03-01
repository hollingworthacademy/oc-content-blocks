# Content Blocks for OctoberCMS

This plugin provides a back-end form widget and a front-end component for creating small, reusable content blocks for use in data models.

## Creating a Content Block

A content block is simply a front-end component that is registered with the content block manager. This is usually done through the `registerContentBlocks` method in a plugin's registration file (`Plugin.php`).

``` php
public function registerContentBlocks()
{
    return [
        \Example\Plugin\Components\MyContentBlock::class => 'myContentBlock'
    ];
}
```

## Using the Back-end Form Widget

This plugin registers a form widget for use with model forms. The model attribute that uses the widget must be _jsonable_ or similar type that can store array data. In the `fields.yaml` for your model set the field type to `contentblocks`.

``` yaml
fields:
    title:
        label: Title
        type:  text
    content:
        label: Content
        type:  contentblocks
```