Feature:
  Given I am on the inevitabletech.uk site
  As a client
  I want to have all my pages without any 500 errors

  Scenario:
    When I am on "/"
    Then all links should work on the page

  Scenario:
    When I am on "/blog/"
    Then all links should work on the page

  Scenario:
    When I am on "/"
    Then test all links from routing file