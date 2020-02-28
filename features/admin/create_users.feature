Feature: Admin manages users
  In order to facilitate different users accessing the system
  As an admin
  I can add new users

  Background:
    Given I have the fixture "core/app/fixtures/users.xml"
    And I login as an admin

  Scenario: Create a new user
    Given I am on "/admin/users"
    When I follow "New User"
    Then I should be on "/admin/users/create"

  Scenario: Save a new user
    When I go to "/admin/users/create"
    And I enter the following:
      | first_name | Dave |
      | last_name  | Lister |
      | email | dave@neonrain.com |
      | password | Wj6tNdQP3bnc22IQ |
      | password_confirm | Wj6tNdQP3bnc22IQ |
    And I press "Save"
    Then I should be on "/admin/users"
    And "Welcome to Starbug!" should be emailed to "dave@neonrain.com"

  Scenario: Save a new user without required fields
    When I go to "/admin/users/create"
    And I press "Save"
    And I should see the following errors:
      | first_name | This field is required |
      | last_name | This field is required |
      | email | This field is required |
      | password | This field is required |
