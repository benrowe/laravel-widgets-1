<?php

namespace Arrilot\Widgets;

use Illuminate\Support\HtmlString;
use InvalidArgumentException;

/**
 * Represents the widget after it has been executed.
 *
 * This expression allows the widets to be outputted (aka echo'd) or
 * allows access to the public methods of the widget inside the view.
 *
 * @package Arrilot\Widgets
 */
class Expression extends HtmlString
{
    protected $widget;

    /**
     * Constructor
     * Recieve necessary dependancies
     *
     * @param string             $html
     * @param BaseAbstractWidget $widget
     */
    public function __construct($html, AbstractWidget $widget)
    {
        $this->widget = $widget;
        parent::__construct($html);
    }

    /**
     * Retrieve the instance of the widget this Expression Represents
     *
     * @return BaseAbstractWidget
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * Magic method implementation
     * Expose the methods of the widget through this expression
     *
     * @param  string $method the method name to try and call
     * @param  array $params params the method requires
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function __call($method, array $params = [])
    {
        if (is_callable($this->widget, $method)) {
            return call_user_func_array([$this->widget, $method], $params);
        }
        throw new InvalidArgumentException(
            sprintf(
                '"%s" does not have a method of "%s"',
                get_class($this->widget),
                $method
            )
        );
    }
}
