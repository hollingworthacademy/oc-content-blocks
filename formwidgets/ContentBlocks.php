<?php namespace Hollingworth\ContentBlocks\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Hollingworth\ContentBlocks\Classes\ContentBlockManager;

/**
 * ContentBlocks Form Widget
 */
class ContentBlocks extends FormWidgetBase
{
    protected $formWidgets = [];

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'hollingworth_pages_content_blocks';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $blocks = post($this->getFieldName()) ?? $this->getLoadValue() ?? [];

        foreach ($blocks as $id => $block) {
            $this->makeBlockFormWidget($id, $block['type'], $block);
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('contentblocks');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
        $this->vars['formWidgets'] = $this->formWidgets;
        $this->vars['blockDetails'] = ContentBlockManager::instance()->listDetails();
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/contentblocks.css', 'hollingworth.pages');
        $this->addJs('js/contentblocks.js', 'hollingworth.pages');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return array_values($value ?? []);
    }

    protected function makeBlockFormWidget($id, $type, $value)
    {
        $contentBlockManager = ContentBlockManager::instance();

        $widget = $this->makeWidget(\Backend\Widgets\Form::class, [
            'model' => $this->model,
            'alias' => $this->alias.$id,
            'arrayName' => $this->getFieldName()."[{$id}]",
            'data' => $value,
            'blockType' => $type,
            'blockId' => $id,
            'blockDetails' => $contentBlockManager->getDetails($type),
            'fields' => $contentBlockManager->getFormFields($type),
        ]);

        $this->formWidgets[] = $widget;
        $widget->bindToController();

        return $widget;
    }

    //
    // AJAX Handlers
    //

    public function onAddBlock()
    {
        $type   = post('type');
        $values = post($this->getFieldName());        
        $id     = count($values);
        $widget = $this->makeBlockFormWidget($id, $type, []);

        return  $this->makePartial('contentblocks_block', [
            'widget' => $widget
        ]);
    }

    public function onRemoveBlock()
    {

    }
}
