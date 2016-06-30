<?php

namespace Arrilot\Widgets\Contracts;

interface Wrapper
{
    /**
     * Begin the widget
     *
     * @return Expression
     */
    abstract public function begin();

    /**
     * End the widget
     *
     * @param  array $params additional config to suplement the instanciated
     *                       widget
     * @return Expression    The result
     */
    abstract public function end(array $params = []);
}
