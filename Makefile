install:
	composer install

test:
	composer exec phpcs -- --standard=PSR12 gendiff src
