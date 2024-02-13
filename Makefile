docker-start:
	docker-compose -r ./docker/docker-compose.yml up -d

docker-stop:
	docker-compose -r ./docker/docker-compose.yml stop

php-bash:
	docker exec -it fpm bash

tests:
	php bin/phpunit

open-coverage:
	xdg-open ./tests/coverage/index.html
