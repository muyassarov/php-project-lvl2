install:
	composer install

lint:
	composer exec phpcs -- --standard=PSR12 bin src tests

lint-fix:
	composer exec phpcbf -- --standard=PSR12 src bin tests

test:
	composer test

test-coverage:
	composer test-coverage
