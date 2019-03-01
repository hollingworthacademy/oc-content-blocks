<?php namespace Hollingworth\ContentBlocks\Components;

use Cms\Classes\ComponentBase;
use Hollingworth\ContentBlocks\Classes\ContentBlockManager;

class ContentBlocks extends ComponentBase
{
    public $blocks = [];

    public function componentDetails()
    {
        return [
            'name'        => 'ContentBlocks Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function onRender()
    {
        $this->blocks = [];
        $blocks = $this->property('blocks') ?? [];

        foreach ($blocks as $id => $block) {
            if (! $className = ContentBlockManager::instance()->resolveName($block['type'])) {
                continue;
            }

            $alias = $this->alias.'_'.$id;
            $this->blocks[] = $alias;

            $this->addComponent($className, $alias, $block);
        }
    }
}
