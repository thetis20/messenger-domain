# Messenger
## Context

This project is an application designed to explore concepts such as Clean Architecture, Test-Driven Development (TDD), Domain-Driven Design (DDD), and Hexagonal Architecture. These approaches aim to produce maintainable, scalable, and testable code.

## Technologies

 - **PHP** : Programming language used for business logic.
 - **Composer** : Dependency manager for PHP.
 - **PHPUnit** : Tool for unit testing.
 - **Docker** : Containerization platform to simplify application execution and isolation.

## Key Concepts
### 1. Clean Architecture

Clean Architecture is a set of principles designed to organize code in a way that it is independent of frameworks, databases, and user interfaces. It enables:

 - Separation of concerns.
 - Easier testing.
 - More comprehensible and maintainable code.

### 2. Test-Driven Development (TDD)

TDD is a development methodology based on writing tests before coding the functionality. The process follows three steps:

 - Write a test that fails.
 - Write the minimum code necessary to make the test pass.
 - Refactor the code while ensuring the tests still pass.

TDD improves code quality and reduces the number of bugs.

### 3. Domain-Driven Design (DDD)

DDD is a software design approach focusing on the business domain. It encourages collaboration between developers and domain experts to:

 - Model the domain effectively.
 - Use a common language.
 - Structure code around business concepts.

### 4. Hexagonal Architecture

Hexagonal Architecture, or Ports and Adapters Architecture, separates the core application logic from external interactions (UI, databases, APIs, etc.). It allows:

 - Easier testing of the application core.
 - Replacement of external components without impacting business logic.
 - Simplified application evolution.

## Installation and Usage

This project composer

1. Clone the repository:
   ```bash
   git clone git@github.com:thetis20/messenger-domain.git
   cd messenger-domain
   ```

2. Install composer dependencies:
   ```bash
   composer install
   ```

3. Run tests
   ```bash
   composer run-script tests
   ```

## Use Cases

### Creating Discussion Groups
A user can create a group and add other participants to it.

```mermaid
flowchart TD
    A[User] --> B[Enter group name]
    B --> C[Add members]
    C --> D[Create group]
    D --> E[Display group in the list]
```

### Sending Messages
A user can send a message in a discussion group.

```mermaid
flowchart TD
    A[User] --> B[Enter message]
    B --> C[Choose recipient]
    C --> D[Send the message]
    D --> E[Display send confirmation]
```

### Receiving Messages
A user receives messages sent by other participants.

```mermaid
flowchart TD
    A[Server] --> B[Receive a message]
    B --> C[Notify the recipient]
    C --> D[Display message in the application]
```

### Viewing Message History
A user can view the history of their conversations.

### Deleting Messages
A user can delete a message they have sent or received.

```mermaid
flowchart TD
    A[User] --> B[Select a message]
    B --> C[Confirm deletion]
    C -->|Confirmed| D[Delete the message]
    C -->|Not confirmed| E[Cancel deletion]
```

### Editing Messages
A user can edit a message they have sent.

```mermaid
flowchart TD
    A[User] --> B[Select a message to edit]
    B --> C[Enter new message]
    C --> D[Confirm edit]
    D -->|Confirmed| E[Update the message]
    D -->|Not confirmed| F[Cancel edit]
```

## License
This project is licensed under the MIT License. Please refer to the LICENSE file for more information.