
# How to contribute to the Todo project?

- Find all the useful files and their roles to contribute to the contribution of the project here
- For each file, an indication on functionalities to make or a bug to repair is indicate
- At the end of the file, find all the global features and optimization to perform 

## Configuration
For a general project configuration, located in the config / directory, refer to [Symfony Documentation] (https://symfony.com/doc/current/configuration.html).

## Controller :

**SecurityController:** This file handles authentication, it is linked with the file *security.yaml*.

**IndexController:** He return the homepage, on the homepage a user can consulted the list of tasks to be done, and create a new task. For admin user, he can create a new user.

***Functionnality to do:***

 - [ ] The link "View finished tasks", doesn't work

**TaskController:** 
- listAction(); 
	- He return all tasks (to do and finished)
- createAction():
	- Allows to create a task, each task created is attached to user who created, the attached form is localited in **src/form/TaskType.php**
-  editAction():
	- Allows to update a task, the updated task stay attached to user who created, the attached form is localited in **src/form/TaskType.php**
- toggleTaskAction()
	- Called when a task is marked like finished or when a task is passed form completed to complete.
- deletetaskAction()
	- Called when a task is deleted, subtlety, each task can deleted by is author, for tasks attached to 'Anonymous user' only an admin can delete it

***Optimizations to do***

 - [ ] Factoring the create and edit methods

 
**UserController:**
The view's linked to ***UserController*** is accessible only by admin users.
- listAction():
	- Return all users
- createAction():
	- Allow to create an user, the attached form is localited in **src/form/UserType.php**
- editAction():
	- Allow to edit an user, the attached form is localited in **src/form/UserType.php**

***Optimizations to do***

 - [ ] Factoring the create and edit methods

# Functionalities and optimizations global

 - [ ] For admin users, make a link for consult all users registered.
 - [ ] For tests file, factorize the recovery of users and tasks
 - [ ] Add a end date to finish a task