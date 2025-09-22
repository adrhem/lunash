## Lunash

Lunash is a simple dashboard for monitoring and managing your Docker containers using `docker-compose`. It provides an easy-to-use interface to view the status of your apps, start and stop them, and view their logs.

It also allows you to look for updates for your Docker images and update them with a single click searching for the latest versions on Docker Hub and GitHub Container Registry.

## Table of Contents

-   [Why Lunash?](#why-lunash)
-   [Development](#development)
    -   [Using Docker-compose](#using-docker-compose)
    -   [Running Locally](#running-locally)

## Why Lunash?

We know that there are many tools available for managing Docker containers like Watchtower, Portainer, or even Docker Desktop. However, we created Lunash to provide a lightweight and straightforward solution specifically for users who prefer using `docker-compose` for managing their applications.
We don't want to replace existing tools but rather offer an alternative that focuses on simplicity and ease of use for `docker-compose` users.

## Development

We provide a docker image for development and a docker-compose file to run the application. However, due to limitations with sharing files between the host and the container, we recommend running the application locally in the meantime we find a better solution.

### Using Docker-compose

-   Download repo.
-   Copy the `.env.example` file to `.env` and configure the environment variables as needed.
-   Run `docker-compose build` to build the image.
-   Run `docker-compose up -d` to start the application. It will also pull the MongoDB image if you don't have it already.
-   Navigate to `http://localhost:8080` in your web browser to access the application.

### Running Locally

-   Make sure you have:
-   -   PHP 8.1 or higher and Composer installed on your machine.
-   -   Docker and Docker Compose installed and running with a few containers to manage.
-   -   MongoDB installed and its pecl extension enabled in your PHP installation. You can find the installation instructions [here](https://www.php.net/manual/en/mongodb.installation.php).
-   Clone the repository to your local machine.
-   Copy the `.env.example` file to `.env` and configure the environment variables as needed.
-   Navigate to the project directory and run `composer install` to install the dependencies.
-   Generate the application key with `php artisan key:generate`.
-   Run the database migrations and seeds with `php artisan migrate` and `php artisan db:seed`.
-   Run the project with artisan: `php artisan serve`. (It supports valet too)
-   Navigate to `http://localhost:8000` in your web browser to access the application.

### Troubleshooting

I highly recommend changing the `credsStore` in your Docker config file (`~/.docker/config.json`) to any docker-credential-helper or remove it completely if you are using Docker Desktop. This is because the default `desktop` credential store does not work well with php exec functions, which are used in this project to interact with Docker. If you don't change this, you may encounter issues when trying to manage your Docker containers through Lunash.
