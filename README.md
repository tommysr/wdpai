# Chain Quest Application

Chain Quest is a comprehensive application designed to facilitate the creation, management, and participation in interactive quizzes. It offers a rich set of features for users, creators, and administrators, ensuring an engaging and seamless experience for all stakeholders.

## Table of Contents
1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Database Design and Structure](#database-design-and-structure)
4. [Design Patterns](#design-patterns)
5. [Installation](#installation)
6. [Screenshots](#screenshots)
7. [License](#license)

## Features

1. **User Registration and Authentication**: Users can register for an account and authenticate securely to access quest-related functionalities.
2. **Quest Creation and Management**:
    - Creators can design quests with multiple questions, specifying various question types such as text, single-choice, and multiple-choice.
    - Quests can be published after approval by administrators.
3. **Quest Participation**:
    - Users can explore and play quests, adhering to permission rules based on quest status and previous interactions.
    - Quest progress is tracked, and users can view their points and percentile score.
4. **Quest Recommendation**:
    - Users are presented with top-rated and recommended quests based on collaborative filtering algorithms.
5. **Rating System**:
    - Users are prompted to rate quests upon completion, contributing to quest rankings.
6. **Administration and Moderation**:
    - Administrators oversee the approval process for quests and have access to administrative features for quest management.
    - Quests can be inspected before publication to ensure quality and validity.
7. **User Profile Management**:
    - Users can manage their profiles, change passwords, and view their quest results and rankings.
8. **Middleware Authorization**:
    - Role-based and quest-based authorization mechanisms ensure that users, creators, and administrators have appropriate access levels.
9. **Support for Multiple Wallets**:
    - Each blockchain supported by the application allows users to manage multiple wallets.

## Technologies Used

Chain Quest leverages a modern technology stack to deliver a robust and scalable solution:

- **Backend**:
    - PHP for server-side logic.
    - PostgreSQL as the relational database management system.
    - NGINX as the web server.
    - Docker for containerization and deployment.
    - PHPUnit for PHP unit testing.
- **Frontend**:
    - HTML, CSS, and JavaScript for user interface development.
- **Design Patterns**:
    - Model-View-Controller (MVC), Repository, Singleton, Adapter, Strategy, Factory, Middleware, Chain of Responsibility, and Builder patterns for efficient and maintainable code architecture.

## Database Design and Structure

The database design follows best practices for data integrity and efficiency:

- Tables such as `users`, `roles`, `quests`, `ratings`, `user_responses`, `quest_progress`, `similarities`, and `pictures` store essential data related to users, roles, quests, ratings, quest progress, recommendations, and multimedia assets.
- Foreign key constraints and unique constraints ensure data consistency and enforce relationships between entities.

![Database ERD](ERD.png)

## Design Patterns

Chain Quest incorporates various design patterns to optimize code organization, scalability, and extensibility:

- **Model-View-Controller (MVC)**: Separates application logic into model, view, and controller components, ensuring a clear separation of concerns.
- **Repository Pattern**: Abstracts data access logic from business logic, promoting code reusability and maintainability.
- **Singleton Pattern**: Ensures a single instance of critical classes, such as database connections, exists throughout the application's lifecycle.
- **Adapter Pattern**: Facilitates integration of diverse components and services, such as authentication and recommendation systems.
- **Strategy Pattern**: Enables dynamic selection of algorithms and behaviors, such as similarity calculation methods in recommendation services.
- **Factory Pattern**: Simplifies object creation, allowing for easy instantiation of related objects based on varying conditions.
- **Middleware Pattern**: Implements cross-cutting concerns, such as authentication and authorization, in a modular and reusable manner.
- **Chain of Responsibility Pattern**: Chains together processing logic, allowing multiple handlers to process requests sequentially until one successfully handles the request.
- **Builder Pattern**: Constructs complex objects, such as quests with multiple questions and options, in a step-by-step manner, providing flexibility and ease of use.

## Installation

To set up and run Chain Quest locally, follow these steps:

1. Ensure Docker is installed on your machine.
2. Clone the repository to your local environment.
3. Navigate to the project directory.
4. Use Docker Compose to build and start the application services:
    ```bash
   docker-compose up --build
    ```
5. Import the database schema and data from the provided SQL dump into your PostgreSQL instance.
6. Modify the database connection settings in the application configuration files, if necessary, to match your environment.
7. Access the application via the provided URL in your web browser.

## Screenshots

Screenshots of the application's user interface can be found in the `screenshots` directory.

## License

This project is licensed under the [MIT License](LICENSE.md). Feel free to modify and distribute the application according to the terms of the license.

For more details, refer to the [LICENSE](LICENSE.md) file.