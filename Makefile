install:
	composer install

lint:
	composer exec phpcs -- --standard=PSR12 bin src

test:
	composer test
