# Laravel + Minio + Docker example

This is an example of how to use Minio with Laravel and Docker, using https everywhere with self signed SSL certificates (aka : Hard Mode).

It supports Temporary URLs to access the files stored in Minio, and temporary Upload URLs to upload files directly to Minio.

## Requirements
* [Docker](https://docs.docker.com/get-docker/) and [Docker-Compose](https://docs.docker.com/compose/install/) (included with Docker Desktop)
* [mkcert](https://github.com/FiloSottile/mkcert) (or your own self signed SSL certificates)

## Overview

This project uses Docker-Compose to run the following services:
  * **haproxy** : A reverse proxy that handles SSL termination and forwards requests to the appropriate service (Laravel or Minio). It could easily be replaced with [Traefik](https://github.com/traefik/traefik), [Caddy](https://github.com/caddyserver/caddy), or NginX.
  * **php** : A PHP container running Laravel 11 through `php artisan serve`
  * **minio** : Our S3 compatible object storage server

We use three different domains to access the services:
  * `https://minio-console.test` : The Minio web console
  * `https://minio-api.test` : The Minio API
  * `https://laravel.test` : The Laravel application

The only port that's exposed from the Docker containers is port 443 on the HAproxy container. It means that requests to all domains need to be in HTTPS and will go through the reverse proxy.

Since we want to use https everywhere, it also means that requests to https://minio-api.test from **within** the docker network needs to be forwared to that container (this is done with `haproxy.network.default.aliases` in the docker-compose file).


## Installation

1. Clone the repository
2. Create an SSL certificate with mkcert, combine the certificate and key into an `haproxy.pem` file, and place it in them in the `.docker/certs` folder along with the `rootCA.pem`. You can do this by running the following commands:
    ```bash
    mkcert -install
    mkcert -cert-file minio-cert.pem -key-file minio-key.pem minio-console.test minio-api.test laravel.test
    cat minio-cert.pem minio-key.pem > .docker/certs/haproxy.pem
    cp $(mkcert -CAROOT)/rootCA.pem .docker/certs/
    ```
3. Add the following to your hosts file :
    ```
    127.0.0.1      minio-console.test
    127.0.0.1      minio-api.test
    127.0.0.1      laravel.test
   ```
4. Run `docker-compose up -d`
5. Run `docker-compose exec "php composer install && php /var/www/html/artisan key:generate && php /var/www/html/artisan migrate"`
6. Create a bucket called `mybucket`. You can do it either : 
   * Through the API : Run `docker-compose exec minio mc mb myminio/mybucket` in your terminal.
   * Through the web UI : Go to `https://minio-console.test` in your browser, login with the credentials (`admin`/`password`), and create the bucket.
7. Open `https://laravel.test` in your browser, you should be able to upload a png file to the bucket through a temporary upload URL, and then see it displayed through a temporary URL.