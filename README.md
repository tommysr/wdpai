# Chain Quest Application

Chain Quest is an application that allows creators to create quizzes with multiple questions. Users can then play these quizzes, and the creator, after the quiz ends, distributes the shared pool amount between addresses given by users during quiz start. Everything is managed by an admin who publishes quests after creation. Creators can then generate reports with user wallet addresses and answers to each question.

## Table of Contents
1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Database Design and Structure](#database-design-and-structure)
4. [Design Patterns Used](#design-patterns-used)
5. [Installation](#installation)
6. [Screenshots](#screenshots)
7. [License](#license)

## Features

1. Users can register into the system and start playing quests right away.
2. Application has a system of permissions:
   - Users can play only published quests and those they didn't play/abandon before.
   - Users are forced to end/abandon the quest after starting it.
   - Creators, after approval by the system administrator, can create quests and update them.
   - Administrators can approve quests created by creators, allowing users to find them.
3. Users can choose top-rated quests and recommended ones.
4. Recommended quests are provided by the system using a memory-based collaborative filtering service.
5. Question types include read text, single choice, and multiple choice.
6. A rating system forces users to rate the quest after completing it from 1 to 5.
7. Users see their progress and points accrued after each question and after ending the quest.
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

![ERD](ERD.png)

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
2. Clone the repository and navigate to the project directory.
3. Use Docker Compose to build and start the services:
   ```sh
   docker-compose up --build
   chmod o+w public/uploads 
   ```
4. Import the database schema and data from the provided SQL dump into your PostgreSQL instance.
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

Screenshots of the application's user interface can be found in the `screenshots` directory.

## License

This project is licensed under the [MIT License](LICENSE.md). - see the file for details.