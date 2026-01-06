# AR24 Test Case

## About
A simple mockup that demonstrates AR24 integration in a modern PHP/Symfony application context.

- Created using the Symfony Docker template (https://github.com/dunglas/symfony-docker). 
- Added Symfony CLI in Dockerfile for local development convenience (https://symfony.com/download).
- Started with Symfony 8.0 skeleton (https://symfony.com/doc/current/setup.html).

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+).
2. Run `docker compose build --pull --no-cache` to build fresh images.
3. Run `docker compose up --wait` to set up and start the application.
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334).
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

Implementetd these API-calls and added the according Symfony commandds:
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

AR24 Test Case is available under the GPL3 License.

## Credits

Created by François-Xavier MAURICARD.
