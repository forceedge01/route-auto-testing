<?php

namespace AutoRoute;

use AutoRoute\ValidatorFactory;
use AutoRoute\Validator\NotHave500Error;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Mink\Mink;
use Exception;

/**
 * AutoRouteTesting class.
 * 
 * The aim of this context is to get routing automation automated. This should be able to accept
 * to register a routing system.
 * 
 * - Check that a page containing all links work correctly.
 * - Check that a navigation element containing all links work correctly.
 * - Feed routing from a file to check all links.
 */
class AutoRouteTestingContext implements MinkAwareContext
{
    private $validatorFactory;

    private $validationRules = [];

    public function __construct()
    {
        $this->validatorFactory = new ValidatorFactory();
    }

    /**
     * @BeforeScenario
     */
    public function resetRules()
    {
        $this->validationRules = [];
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
     * @param string $class
     */
    public function addValidationRule($class)
    {
        $this->validationRules[] = $class;

        return $this;
    }

    /**
     * @Then all links should work on the page
     */
    public function testAllLinks()
    {
        $this->addValidationRule(NotHave500Error::class);
        $currentUrl = $this->mink->getSession()->getCurrentUrl();
        $links = $this->mink->getSession()->getPage()->findAll('css', 'a');
        $error = false;

        $linksOnPage = [];
        foreach ($links as $link) {
            $linksOnPage[$link->getText()] = $link->getAttribute('href');
        }

        foreach ($linksOnPage as $linkName => $linkHref) {
            try {
                $this->testLink($linkName, $linkHref);
            } catch (Exception $e) {
                echo '[ERROR]: ' . $e->getMessage() . ', text: ' . $linkName . ', href: ' . $linkHref . PHP_EOL . PHP_EOL;
                $error = 1;
            }
        }

        if ($error) {
            throw new Exception('Errors were found above.');
        }
    }

    /**
     * @param string $linkText
     * @param string $href
     *
     * @return void
     */
    public function testLink($linkText, $href)
    {
        if (! empty($href)) {
            $this->mink->getSession()->visit($href);
            echo 'Testing link: ' . $linkText . ', link: ' . $href . PHP_EOL;

            foreach ($this->validationRules as $rule) {
                $this->validatorFactory->get($rule)->validate($this->mink);
            }
        }
    }

    /**
     * @Then the page should :arg1
     */
    public function validateThat($arg1)
    {
        $class = $this->getClass($arg1);

        if (! class_exists($class)) {
            throw new Exception("Class '$class' not found.");
        }

        return $this->validatorFactory->get($class)->validate($this->mink);
    }

    /**
     * @Then the page should:
     */
    public function validateThatMultiple(TableNode $arg1)
    {
        foreach ($arg1 as $validate) {
            $this->validateThat($validate);
        }
    }

    private function getClass($class)
    {
        return __NAMESPACE__ . '\\Validator\\' . $this->humanToClassValidatorName($class);
    }

    private function humanToClassValidatorName($arg1)
    {
        return str_replace(' ', '', ucwords($arg1));
    }
}
