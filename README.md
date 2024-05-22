## General info
Use the existing codebase any way you like, but do try to complete the following assignments.
Commit your solution and send it the same way you got it. We will use it as a kick-off point for our interview and 
discuss architecture decisions, code performance and solution approaches.

It would be appreciated in the real world, but you can skip writing documentation. 

### Running project
This project is a simplified app, that runs in CLI mode. 

If you are running PHP 8.1 and _composer_ locally, you can do the `composer install` from the `app` folder and then do 
`vendor/bin/phpunit` for running tests and `bin/app filter` for running the parser command.

If you prefer running things in Docker, we have provided a `docker-compose.yml` file, as well as `Makefile` for shorthand methods.

## Assignments

### Notice ###
Please be advised that your skills in GIT will be assessed too, 
so please init git repo and let us see your workflow in those commits 

#### TASK 1 ####
### Write command ###
Update existing command (App/Infrastructure/MainCommand) that will accept argument "type" based onb which it will analyze dataset

### Analyze dataset
Given the `var/input.jsonl` dataset, can you write the code that does analysis and gives us the following answers:
1. All persons that are older then 20 and younger then 60
2. ... that live in London
3. ... that does not have any children or pets
4. That fullfil all of the above
5. That fullfil any of the above
Conditions: 
Please write and use trait Filter that will be used for these answers 
Please try to write it as wide as possible (configurable)

#### TASK 2 ####
### Write class Database ###
that will be able to 
1. Make localhost mysql connection
2. Create table for data save from .jsonl file
3. Make insert of one or more rows
4. Delete one or more rows
5. Drops the table

### Write class Seeder ###
that will extend class Database and that will be able to:
1. Make import of selected (or all) applying filters trait from TASK 1
2. Make rollback of last import
Condition: 
Please write and use helper class FiledMap for field mapping

#### TASK 3 ####
### Write new command ###
that will make import of rows from jsonl file and that will accept argument "type" which will determine import criteria

#### EXTRA EFFORT ####
Write tests for two commands

