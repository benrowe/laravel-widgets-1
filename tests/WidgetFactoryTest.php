<?php

namespace Arrilot\Widgets\Test;

use Arrilot\Widgets\Factories\WidgetFactory;
use Arrilot\Widgets\Test\Dummies\TestCachedWidget;
use Arrilot\Widgets\WidgetGroup;

class WidgetFactoryTest extends TestCase
{
    /**
     * @var WidgetFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new WidgetFactory(new TestApplicationWrapper());
    }

    public function testItThrowsExceptionForBadWidgetClass()
    {
        $this->setExpectedException('\Arrilot\Widgets\Misc\InvalidWidgetClassException');

        $this->factory->run('testBadSlider');
    }

    public function testWidgetsCanBeRunFromDefaultNamespace()
    {
        $output = $this->factory->run('testDefaultSlider');

        $this->assertEquals('Default test slider was executed with $slides = 6', $output);
    }

    public function testWidgetConfigCanBePartlyOverwritten()
    {
        $output = $this->factory->run('slider', ['slides' => 5]);

        $this->assertEquals('Slider was executed with $slides = 5 foo: bar', $output);
    }

    public function testWidgetConfigCanBeFullyOverwritten()
    {
        $output = $this->factory->run('slider', ['slides' => 5, 'foo' => 'Taylor']);

        $this->assertEquals('Slider was executed with $slides = 5 foo: Taylor', $output);
    }

    public function testWidgetsCanBeRunWithAdditionalParams()
    {
        $output = $this->factory->run('testWidgetWithParamsInRun', ['slides' => 5], 'asc');

        $this->assertEquals('TestWidgetWithParamsInRun was executed with $flag = asc', $output);
    }

    public function testWidgetsCanBeRunUsingMagicMethod()
    {
        $output = $this->factory->testWidgetWithParamsInRun(['slides' => 5], 'asc');

        $this->assertEquals('TestWidgetWithParamsInRun was executed with $flag = asc', $output);
    }

    public function testItCanRunWidgetsUsingFQCN()
    {
        $output = $this->factory->run('\Arrilot\Widgets\Test\Dummies\TestDefaultSlider');

        $this->assertEquals('Default test slider was executed with $slides = 6', $output);
    }

    public function testItCanRunNestedWidgets()
    {
        $output = $this->factory->run('Profile\TestNamespace\TestFeed');

        $this->assertEquals('Feed was executed with $slides = 6', $output);
    }

    public function testItCanRunNestedWidgetsUsingDotNotation()
    {
        $output = $this->factory->run('profile.testNamespace.testFeed');

        $this->assertEquals('Feed was executed with $slides = 6', $output);
    }

    public function testItCanRunMultipleWidgetsDuringASingleRequest()
    {
        $output = $this->factory->run('slider');

        $this->assertEquals('Slider was executed with $slides = 6 foo: bar', $output);

        $output = $this->factory->run('slider', ['slides' => 5]);

        $this->assertEquals('Slider was executed with $slides = 5 foo: bar', $output);

        $output = $this->factory->run('testDefaultSlider');

        $this->assertEquals('Default test slider was executed with $slides = 6', $output);
    }

    public function testItCanRunReloadableWidgets()
    {
        $output = $this->factory->run('testRepeatableFeed');

        $this->assertEquals(
            '<div id="arrilot-widget-container-1" style="display:inline" class="arrilot-widget-container">Feed was executed with $slides = 6'.
            '<script type="text/javascript">setTimeout( function() { $(\'#arrilot-widget-container-1\').load(\'/arrilot/load-widget\', '.$this->javascriptDataStub('TestRepeatableFeed').') }, 10000)</script>'.
            '</div>', $output);
    }

    public function testWidgetContainerCanBeCustomized()
    {
        $output = $this->factory->run('testWidgetWithCustomContainer');

        $this->assertEquals(
            '<p id="arrilot-widget-container-1" data-id="123">Dummy Content'.
            '<script type="text/javascript">setTimeout( function() { $(\'#arrilot-widget-container-1\').load(\'/arrilot/load-widget\', '.$this->javascriptDataStub('TestWidgetWithCustomContainer').') }, 10000)</script>'.
            '</p>', $output);
    }

    public function testItCanCacheWidgets()
    {
        $output = $this->factory->run('testCachedWidget', ['foo' => 'bar']);

        $key = 'arrilot.widgets.'.serialize(['testCachedWidget', ['foo' => 'bar']]);
        $widget = new TestCachedWidget();

        $this->assertEquals('Cached output. Key: '.$key.', minutes: '.$widget->cacheTime, $output);
    }

    public function testItGrantsAccessToWidgetGroup()
    {
        $groupObject = $this->factory->group('sidebar');

        $expectedObject = new WidgetGroup('sidebar', new TestApplicationWrapper());

        $this->assertEquals($expectedObject, $groupObject);
    }
}