<?php

use AutoRoute\AutoRouteTestingContext;
use AutoRoute\Validator\EndWithHtmlTag;
use AutoRoute\Validator\NotHave500Error;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Mink\Mink;
use Genesis\TestRouting\Routing;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, MinkAwareContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $path = __DIR__ . '/../../Routing.php';
        Routing::setAllRoutesFromExternalSource(require $path, function($pathInfo, $pathName) {
            if ($pathInfo['method'] === Routing::METHOD_GET) {
                return [$pathName, $pathInfo['path']];
            }
        });
    }

    /**
     * @param Mink $mink
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;

        return $this;
    }

    /**
     * @param array $minkParameters
     */
    public function setMinkParameters(array $minkParameters)
    {
        $this->minkParameters = $minkParameters;

        return $this;
    }

    /**
     * @Then test all links from routing file
     */
    public function testAllRoutes()
    {
        $routes = Genesis\TestRouting\Routing::getRoutes();
        $autoRouteContext = new AutoRouteTestingContext();
        $autoRouteContext->setMink($this->mink);
        $autoRouteContext->setMinkParameters($this->minkParameters);

        $autoRouteContext->addValidationRule(NotHave500Error::class);
        $autoRouteContext->addValidationRule(EndWithHtmlTag::class);

        foreach ($routes as $routeName => $routeLink) {
            $autoRouteContext->testLink($routeName, $routeLink);
        }
    }
}
