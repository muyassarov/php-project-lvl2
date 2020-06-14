install:
	composer install

lint:
	composer exec phpcs -- --standard=PSR12 bin src tests

lint-fix:
	composer exec phpcbf -- --standard=PSR12 src bin tests

test:
	composer exec phpunit tests

test-coverage:
	composer exec -- phpunit --coverage-clover tests/outputs/coverage.xml tests
