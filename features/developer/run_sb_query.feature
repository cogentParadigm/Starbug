Feature: Developer runs query command
  In order to run queries through the database abstraction layers
  As a Developer
  I can use the query command

  Scenario: Select all from users
    When I run "php sb query users"
    Then I should see "SELECT `users`.* FROM `sb_users` AS `users`" in the output

  Scenario: Select all from users with where condition
    When I run "php sb query users where:id=1"
    Then I should see "SELECT `users`.* FROM `sb_users` AS `users` WHERE id=1" in the output

  Scenario: Select id and email from users
    When I run "php sb query users select:id,email"
    Then I should see "SELECT id,email FROM `sb_users` AS `users`" in the output

  Scenario: Select id and email from users with where condition
    When I run "php sb query users where:id=1 select:id,email"
    Then I should see "SELECT id,email FROM `sb_users` AS `users` WHERE id=1" in the output
