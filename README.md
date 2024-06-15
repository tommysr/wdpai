# Chain Quest Application

Chain Quest is an application that allows creators to create quizzes related to blockchain with multiple questions. Users can then play these quizzes, and after the quiz ends, the creator distributes the shared pool amount between the addresses given by users during the quiz start. Everything is managed by an admin who publishes quests after creation. Creators can then generate reports with user wallet addresses and answers to each question.

## Table of Contents
1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Database Design and Structure](#database-design-and-structure)
4. [Design Patterns Used](#design-patterns-used)
5. [Installation](#installation)
6. [Screenshots](#screenshots)
7. [License](#license)

## Features

1. Users can register in the system and start playing quests right away.
2. The application has a system of permissions:
   - Users can play only published quests and those they haven't played or abandoned before.
   - Users are forced to end or abandon the quest after starting it.
   - Creators, after approval by the system administrator, can create and update quests.
   - Administrators can approve quests created by creators, allowing users to find them.
3. Users can choose top-rated quests and recommended ones.
4. Recommended quests are provided by the system using a memory-based collaborative filtering service.
5. Question types include read text, single choice, and multiple choice.
6. A rating system forces users to rate the quest after completing it from 1 to 5.
7. Users see their progress and points accrued after each question and upon ending the quest.
8. Users can see their percentile score.
9. Users can change their password on the profile page.
10. Users can see their results for each quest and rank based on quizzes done.
11. Middleware authentication:
    - Authentication: Used in middleware to prevent users from entering routes unauthenticated.
12. Middleware authorization:
    - Role-based: Determines if users can access routes based on their roles using ACL for each route registered.
    - Quest-based: Used by creators and users to prevent users from entering other routes during gameplay which they need to first end. Creators also use this to prevent them from modifying quests already published and from editing/viewing quests not created by them.
13. Admin inspection - Admin can preview quests with questions to check the validity of the quest before publishing it.
14. Support for multiple wallets for each blockchain.


## Technologies Used

- PHP
- HTML
- CSS
- JavaScript
- NGINX
- PostgreSQL
- Docker
- PHPUnit testing tool

The app is designed to be responsive and the backend is designed to comply with SOLID principles.

## Database Design and Structure

The application uses a PostgreSQL database with tables storing quests, questions with options, users and their roles, user wallets, ratings, user responses for each question, and user progress. Due to the recommendation feature, there is a need to store a similarities matrix for calculating recommendations.

### Tables:
- **blockchains**: Stores blockchain information.
- **options**: Stores possible answers to questions.
- **pictures**: Stores URLs of pictures.
- **quest_progress**: Tracks user progress in quests.
- **questions**: Stores questions for each quest.
- **quests**: Stores quests created by users.
- **ratings**: Stores user ratings for quests.
- **roles**: Defines user roles.
- **similarities**: Stores similarity scores for recommendations.
- **tokens**: Stores token information.
- **user_responses**: Stores responses given by users.
- **users**: Stores user information.
- **wallets**: Stores user wallets and associated blockchain information.

### ERD:
![ERD](erd.png)

## Design Patterns Used

This project leverages both the Model-View-Controller (MVC) architecture and the Repository pattern to optimize data handling:

- **Models**: Define the data structure and its related business logic.
- **Repositories**: Manage data operations, acting as an intermediary between the models and the database, ensuring that data interactions are handled cleanly and maintainably.
- **Controllers**: Handle incoming requests, interacting with models or repositories to retrieve data and return responses.
- **Views**: Present data to the user, rendering the final output on the screen.

Additionally, the Singleton design pattern is utilized for managing the database connections:
- **Singleton Pattern**: Ensures a single instance of the Database class exists, promoting a single shared connection to the database which optimizes resource usage and maintains consistent access.

Other patterns used include:
- **Adapter Pattern**: Used for dispatching login actions.
- **Strategy Pattern**: Used in the recommendation system to choose between similarity and prediction strategies. Strategies and adapters utilize the factory creational pattern for easy creation and registration in the factory registry.
- **Middleware Pattern**: Used for managing authorization/authentication requests before running actual actions in routes. This pattern is a reverse chain of responsibility pattern.
- **Chain of Responsibility Pattern**: Used in validation of inputs, allowing chaining of validation rules and processing them altogether.
- **Builder Pattern**: Used to create complicated quests with all the questions and options in one model to allow service-side to easily process them after receiving them in the controller actions.

## Installation

To install and run the project, follow these steps:
1. Ensure Docker is installed on your machine.
2. Clone the repository and navigate to the project directory, make sure to create uploads directory.
3. Make sure you have public/uploads directory created.
4. Modify permissions:
   ```sh
   chmod 777 public/uploads
   ```
3. Use Docker Compose to build and start the services:
   ```sh
   docker-compose up --build
   chmod 777 public/uploads 
   ```
5. Import the database schema and data from the provided SQL dump into your PostgreSQL instance:
   ```sh
   docker compose cp sql_dump.sql db:sql_dump.sql
   docker compose exec db sh
   psql -U "your_user" -d "your_database" -f objects.sql
   ```
5. Modify the initialize method in DefaultDBConfig.php to suit your environment settings, specifically for database connections:
   ```php
    $this->setValue(self::USERNAME_KEY, 'docker');
    $this->setValue(self::PASSWORD_KEY, 'docker');
    $this->setValue(self::HOST_KEY, 'db');
    $this->setValue(self::DATABASE_KEY, 'db');
    $this->setValue(self::PORT_KEY, '5432');
    ```
7. Access the application via the provided URL in your web browser.
8. You can explore routes, to promote users to creators, default admin credentials are: admin:admin.

## Screenshots

Screenshots of different views for both desktop and mobile are provided below.

### User Main Views

#### Desktop
![User Main Desktop](./screenshots/user_main_desktop.png)

#### Mobile
![User Main Mobile](./screenshots/user_main_mobile.png)

### Wallet Select Views

#### Desktop
![Wallet Select Desktop](./screenshots/wallet_select_desktop.png)

#### Mobile
![Wallet Select Mobile](./screenshots/wallet_select_mobile.png)



### Dashboard Views

#### Desktop
![Dashboard Desktop](./screenshots/dahboard_desktop.png)

#### Mobile
![Dashboard Mobile](./screenshots/dashboard_mobile.png)


### Single Choice Views

#### Desktop
![Single Choice Desktop](./screenshots/single_choice_desktop.png)

#### Mobile
![Single Choice Mobile](./screenshots/single_choice_mobile.png)


### Multiple Choice Views

#### Desktop
![Multiple Choice Desktop](./screenshots/multiple_choice_desktop.png)

#### Mobile
![Multiple Choice Mobile](./screenshots/multiple_choice_mobile.png)

### Quest Summary Views

#### Desktop
![Quest Summary Desktop](./screenshots/quest_summary_desktop.png)

#### Mobile
![Quest Summary Mobile](./screenshots/quest_summary_mobile.png)

### Question Summary Views

#### Desktop
![Question Summary Desktop](./screenshots/question_summary_desktop.png)

#### Mobile
![Question Summary Mobile](./screenshots/question_summary_mobile.png)

### Read Text Views

#### Desktop
![Read Text Desktop](./screenshots/read_text_desktop.png)

#### Mobile
![Read Text Mobile](./screenshots/read_text_mobile.png)

### Admin Views

#### Desktop
![Admin View 1](./screenshots/admin_2_desktop.png)
![Admin View 2](./screenshots/admin_desktop.png)
![Admin View 3](./screenshots/admin_view_desktop.png)

### Creator Page Views

#### Desktop
![Creator Page Desktop](./screenshots/creator_page_desktop.png)

#### Mobile
![Creator Page Mobile](./screenshots/creator_page_mobile.png)

### Create/Edit Views

#### Desktop
![Create/Edit Desktop](./screenshots/create_edit_desktop.png)

More screenshots in screenshots directory.

## License

This project is licensed under the [MIT License](LICENSE.md). - see the file for details.