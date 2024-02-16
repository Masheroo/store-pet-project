docker-start:
	docker-compose -f ./docker/docker-compose.yml up -d

docker-stop:
	docker-compose -f ./docker/docker-compose.yml stop

php-bash:
	docker exec -it fpm bash

tests:
	php bin/phpunit

open-coverage:
	xdg-open ./tests/coverage/index.html

migrate:
	symfony console d:m:m --no-interaction
	symfony console d:m:m --env=test --no-interaction