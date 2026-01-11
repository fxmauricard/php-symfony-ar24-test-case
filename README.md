# AR24 Test Case

## About
A simple mockup that demonstrates AR24 integration in a modern PHP/Symfony application context.

- Created using the Symfony Docker template (https://github.com/dunglas/symfony-docker). 
- Added Symfony CLI in Dockerfile for local development convenience (https://symfony.com/download).
- Started with Symfony 8.0 skeleton (https://symfony.com/doc/current/setup.html). 
- Symfony Maker Bundle is included for code generation (https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html).
- Uses Doctrine ORM for database interactions (https://www.doctrine-project.org/projects/orm.html). 
- Uses API Platform 4.2 to expose domain entities as a REST API (https://api-platform.com/docs/).
- Uses Symfony Fixtures to load sample domain entities (https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html).
- Integrates with AR24 API using a custom API client. (https://developers.ar24.fr/doc/?php#introduction).

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+).
2. Run `docker compose build --pull --no-cache` to build fresh images.
3. Run `docker compose up --wait` to set up and start the application.
4. Run `docker compose exec php bash -c "php bin/console doctrine:fixtures:load"` to load default user and domain entities fixtures.
5. Run `docker compose exec php bash -c "php bin/console lexik:jwt:generate-keypair"` to generate JWT keys for API authentication.
6. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334).
7. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

- Default Symfony "Welcome" page is still available at `https://localhost/`.
- Domain entities are exposed as a REST API using API Platform (https://localhost/api).
- Swagger UI is available at`https://localhost/api/docs for easy exploration and testing of the API.


- API is secured using JWT authentication. 
  - Run `curl --insecure -X POST -H "Content-Type: application/json" https://localhost/api/login_check -d '{"username":"admin@example.com","password":"password"}'` to get a JWT token for the default admin user created by the fixtures.
  - Use the returned token in the `Authorization: Bearer <token>` header to access the API endpoints. It can be also used in API Platform's built-in Swagger UI at `https://localhost/api/docs` using the **[ Authorize ]** button.


- Added a simple domain "Test Case" that simulate a simple revaluation of the rent of a `Lease` contract, including the corresponding `Tenant`. AR24 integration is needed by the domain for the `RentRevaluationNotification` entity.


- Implemented these AR24 API calls and added the according Symfony commands:
  * `ar24:attachment:list-by-registered-mail-id`  List all AR24 attachments for a mail
  * `ar24:attachment:list-by-user-id`             List all AR24 attachments for a user
  * `ar24:attachment:upload`                      Upload an AR24 attachment from a file
  * `ar24:registered-mail:get-by-id`              Get an AR24 registered mail by ID
  * `ar24:registered-mail:list`                   List all AR24 registered mails for a user
  * `ar24:registered-mail:send`                   Send an AR24 registered mail from a JSON file
  * `ar24:user:create`                            Create an AR24 user from a JSON file
  * `ar24:user:get-by-email`                      Get an AR24 user by email
  * `ar24:user:get-by-id`                         Get an AR24 user by ID
  * `ar24:user:list`                              List all AR24 users

## License

AR24 Test Case is available under the MIT license.

## Credits

Created by François-Xavier MAURICARD.
