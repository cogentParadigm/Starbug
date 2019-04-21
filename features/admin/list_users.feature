Feature: Admin manages users
  In order to facilitate different users accessing the system
  As an admin
  I can see a list of users

  Background:
    Given I login as an admin

  Scenario: View users
    When I go to "/admin/users"
    And I should see a paged grid with columns:
      | First Name | Last Name | Email | Last Visit | Groups | Status | Options |
    And I should see "New User"

  Scenario: Export users
    When I go to "/admin/users"
    And I export "users" by clicking "Export CSV"
    Then I should get a CSV similar to:
      | Id | First Name | Last Name | Email | Last Visit | Owner | Created | Modified | Deleted | Groups |