@eventStore
Feature: Register new user

  Scenario: Create user and identity
    When i register a new user
    Then an user should be created
    And an identity should be created

  Scenario: Don´t create an identity if user was not created
    Given creating an user would fail
    When i register a new user
    Then an identity should not be created
