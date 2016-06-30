<?php

namespace Arrilot\Widgets;

use Arrilot\Widgets\Contracts\Widget;

abstract class AbstractWidget implements Widget
{
    const ID_PREFIX = 'widget-';

    /**
     * @var string
     */
    private $id;

    /**
     * The number of seconds before each reload.
     * False means no reload at all.
     *
     * @var int|float|bool
     */
    public $reloadTimeout = false;

    /**
     * The number of minutes before cache expires.
     * False means no caching at all.
     *
     * @var int|float|bool
     */
    public $cacheTime = false;

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->addCfg($key, $value);
        }
        $this->init();
    }

    /**
     * init the widget
     *
     * @return void
     */
    public function init()
    {

    }

    /**
     * Retrieve the configuration value from the widget, based
     * on the supplied key
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function cfg($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
        if ($this->isConfigProperty($key)) {
            return $this->$key;
        }
        return $default;
    }

    /**
     * Add a configuration into the widget
     *
     * @param string $key
     * @param mixed $value
     * @return nil|null
     */
    public function addCfg($key, $value)
    {
        if ($this->isConfigProperty($key)) {
            $this->$key = $value;
            return;
        }
        $this->config[$key] = $value;
    }

    /**
     * Retrieve the widget id
     *
     * @param  boolean $autoGenerate automatically generate the id if none is
     *                               already set
     * @return string
     */
    public function getId($autoGenerate = true)
    {
        if (!$this->id && $autoGenerate) {
            $this->id = self::ID_PREFIX . WidgetId::get();
        }
        return $this->id;
    }

    /**
     * Set the widget identifier
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Determine if the public property exists, and is public
     *
     * @param  string  $propertyName
     * @return boolean
     */
    private function isConfigProperty($propertyName)
    {
        try {
            $reflect = new \ReflectionClass($this);
            $property = $reflect->getProperty($propertyName);
            return $property->isPublic() && !$property->isStatic();
        } catch (\ReflectionException $e) {
            return false;
        }

    }

    /**
     * Placeholder for async widget.
     * You can customize it by overwriting this method.
     *
     * @return string
     */
    public function placeholder()
    {
        return '';
    }

    /**
     * Async and reloadable widgets are wrapped in container.
     * You can customize it by overriding this method.
     *
     * @return array
     */
    public function container()
    {
        return [
            'element'       => 'div',
            'attributes'    => 'style="display:inline" class="arrilot-widget-container"',
        ];
    }

    /**
     * Cache key that is used if caching is enabled.
     *
     * @param $params
     *
     * @return string
     */
    public function cacheKey(array $params = [])
    {
        return 'arrilot.widgets.'.serialize($params);
    }

    /**
     * Add defaults to configuration array.
     *
     * @param array $defaults
     */
    protected function addConfigDefaults(array $defaults)
    {
        $this->config = array_merge($this->config, $defaults);
    }
}
