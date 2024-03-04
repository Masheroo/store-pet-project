docker-start:
	docker-compose -f ./docker/docker-compose.yml up -d

docker-stop:
	docker-compose -f ./docker/docker-compose.yml stop

php-bash:
	docker exec -it fpm bash

start-tests:
	php bin/phpunit

open-coverage:
	xdg-open ./tests/coverage/index.html

migrate:
	symfony console d:m:m --no-interaction
	symfony console d:m:m --env=test --no-interaction

fixtures-test:
	symfony console d:f:l --no-interaction --env=test

fixtures-current:
	symfony console d:f:l --no-interaction

fixtures-all: fixtures-test fixtures-current
