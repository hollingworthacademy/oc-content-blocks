<?php namespace Hollingworth\ContentBlocks;

use Backend;
use System\Classes\PluginBase;
use System\Classes\CombineAssets;

/**
 * ContentBlocks Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'ContentBlocks',
            'description' => 'No description provided yet...',
            'author'      => 'Hollingworth',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('$/hollingworth/contentblocks/formwidgets/contentblocks/assets/less/contentblocks.less');
        });
    }

    /**
     * Registers front-end components.
     * 
     * @return array
     */
    public function registerComponents()
    {
        return [
            \Hollingworth\ContentBlocks\Components\ContentBlocks::class => 'hollingworth_contentblocks'
        ];
    }

    /**
     * Registers back-end form widgets.
     * 
     * @return array
     */
    public function registerFormWidgets()
    {
        return [
            \Hollingworth\ContentBlocks\FormWidgets\ContentBlocks::class => 'hollingworth_contentblocks'
        ];
    }
}
