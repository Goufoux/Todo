Feature:
    When user create a task
    Task to be attach to this user

  Scenario: Task attached to user
    When user create task
    Then task author must be this user
