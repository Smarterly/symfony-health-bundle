Feature:
  So that my application adds value to the business
  As a Cushoneer
  I want my service to report it's health.

  Scenario: Service is healthy
    Given that all dependencies are healthy
    When I ask what the health of the service is
    Then I am informed the service is healthy.

  Scenario: One dependency is unhealthy
    Given that one dependency is unhealthy
    When I ask what the health of the service is
    Then I am informed the service is unhealthy.
