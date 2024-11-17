build:
	docker build --debug . --tag=messenger-domain
test:
	docker run -v ./:/app messenger-domain php bin/phpunit
install:
	docker run -v ./:/app messenger-domain composer install
exec:
	docker run -v ./:/app messenger-domain $(CMD)