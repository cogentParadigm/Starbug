Feature: Developer runs query command
  In order to run queries through the database abstraction layers
  As a Developer
  I can use the query command

  Scenario: Select all from users
    When I run "php sb query users"
    Then the output should match the pattern "/SELECT `users`.* FROM `[a-z]+_users` AS `users`/"

  Scenario: Select all from users with where condition
    When I run "php sb query users where:id=1"
    Then the output should match the pattern "/SELECT `users`.* FROM `[a-z]+_users` AS `users` WHERE id=1/"

  Scenario: Select id and email from users
    When I run "php sb query users select:id,email"
    Then the output should match the pattern "/SELECT id,email FROM `[a-z]+_users` AS `users`/"

  Scenario: Select id and email from users with where condition
    When I run "php sb query users where:id=1 select:id,email"
    Then the output should match the pattern "/SELECT id,email FROM `[a-z]+_users` AS `users` WHERE id=1/"
