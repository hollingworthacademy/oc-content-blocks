<?php namespace Hollingworth\ContentBlocks\Classes;

use App;
use System\Classes\PluginManager;
use Cms\Classes\ComponentManager;
use October\Rain\Exception\SystemException;
use October\Rain\Support\Str;

class ContentBlockManager
{
    use \October\Rain\Support\Traits\Singleton;
    use \System\Traits\ConfigMaker;

    protected $codeMap;

    protected $classMap;

    protected $details;

    public function listContentBlocks()
    {
        if ($this->codeMap) {
            return $this->codeMap;
        }

        $componentManager = ComponentManager::instance();
        $pluginManager = PluginManager::instance();
        $plugins = $pluginManager->getPlugins();
        $this->codeMap = [];

        foreach ($plugins as $plugin) {
            if (!method_exists($plugin, 'registerContentBlocks')) {
                continue;
            }
            
            $blocks = $plugin->registerContentBlocks();

            if (!is_array($blocks)) {
                continue;
            }

            foreach ($blocks as $className => $code) {
                $componentManager->registerComponent($className, $code, $plugin);
                $this->codeMap[$code] = Str::normalizeClassName($className);
            }
        }        

        return $this->codeMap;
    }

    public function listDetails()
    {
        if ($this->details) {
            return $this->details;
        }

        $codeMap = $this->listContentBlocks();
        $details = [];

        foreach ($codeMap as $code => $className) {
            $details[$code] = $this->makeContentBlock($className)->componentDetails();
        }

        return $this->details = $details;
    }

    public function hasContentBlock($name)
    {
        return (bool) $this->resolve($name);
    }

    /**
     * Returns a class name from a component code.
     * 
     * @param string $name A component class name or component code.
     * @return string|null The class name or null.
     */
    public function resolveName($name)
    {
        $blocks = $this->listContentBlocks();

        if (isset($blocks[$name])) {
            return $blocks[$name];
        }

        if ($this->classMap === null) {
            $this->classMap = array_flip($blocks);
        }

        $className = Str::normalizeClassName($name);

        if (isset($this->classMap[$className])) {
            return $className;
        }

        return null;
    }

    /**
     * Makes an instance of a content block component.
     * 
     * @param string $name A class name or component code.
     * @param null|\Cms\Classes\CodeBase $cmsObject
     * @param array $properties The component properties.
     * @return \Cms\Classes\ComponentBase
     */
    public function makeContentBlock($name, $cmsObject = null, $properties = [])
    {
        if (! $className = $this->resolveName($name)) {
            throw new SystemException(sprintf(
                'Class name not registered for block "%s".',
                $name
            ));
        }

        if (! class_exists($className)) {
            throw new SystemException(sprintf(
                'Block class "%s" not found.',
                $className
            ));
        }

        $block = App::make($className, [$cmsObject, $properties]);
        $block->name = $name;

        return $block;
    }

    /**
     * Loads the form fields from config file.
     * 
     * @param string $name
     * @return array
     */
    public function getFormFields($name)
    {
        if (! $className = $this->resolveName($name)) {
            throw new SystemException(sprintf(
                'Class name not registered for block "%s".',
                $name
            ));
        }

        if (! class_exists($className)) {
            throw new SystemException(sprintf(
                'Block class "%s" not found.',
                $className
            ));
        }

        $path = $this->guessConfigPathFrom($className, '/fields.yaml');
        
        return (array) $this->makeConfig($path);
    }

    /**
     * Gets the details of a content block.
     * 
     * @param string $name A class name or component code.
     * @return array
     */
    public function getDetails($name)
    {
        if (! $className = $this->resolveName($name)) {
            throw new SystemException(sprintf(
                'Class name not registered for block "%s".',
                $name
            ));
        }

        if (! class_exists($className)) {
            throw new SystemException(sprintf(
                'Block class "%s" not found.',
                $className
            ));
        }        

        $details = $this->listDetails();
        $code = $this->classMap[$className];

        return $details[$code] ?? null;
    }
}