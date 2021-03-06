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

[Transfer using optimistic lock](http://localhost/transfer/Jack/1/Anne)

```bash
$ docker exec -it db-locking-demo_app_1 /bin/bash
$ php bin/console account:reset
$ php bin/console account:transfer Jack 9 Anne
$ php bin/console account:transfer Jack 99 Anne --lock=0 # 0 for none, 2 for pessimistic read, 4 for pessimistic write
$ php bin/console account:transfer-without-lock # disable xdebug to see results
$ php bin/console account:transfer-with-pessimistic-write-lock
$ php bin/console account:transfer-with-pessimistic-read-lock
```

## License
db-locking-demo is available under the [MIT license](LICENSE).
