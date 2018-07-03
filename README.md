# Database Locking Demo
Contains demo code to lock database

## Dependencies

- git
- [composer](https://getcomposer.org/download/)
- docker
- [docker-compose](https://docs.docker.com/compose/)

## Install

```bash
$ git clone git@github.com:tienvx/db-locking-demo.git
$ cd db-locking-demo
$ composer install
$ cp .env.dist .env
$ docker-compose up
```

## Usage

```bash
$ php bin/console account:reset
$ php bin/console account:transfer Jack 9 Anne
$ php bin/console account:transfer Jack 99 Anne --lock=optimistic
$ php bin/console account:transfer-without-lock
$ php bin/console account:transfer-with-optimistic-lock
$ php bin/console account:transfer-with-pessimistic-lock
```

## License
db-locking-demo is available under the [MIT license](LICENSE).
