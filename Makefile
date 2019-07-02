.PHONY: qa lint cs csf phpstan tests coverage-clover coverage-html

all:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' | xargs

vendor: composer.json composer.lock
	composer install

# QA

qa: cs phpstan

lint: vendor
	vendor/bin/parallel-lint src tests --blame

cs: vendor
	vendor/bin/phpcs --cache=tmp/codesniffer.dat --standard=ruleset.xml --colors -nsp src tests

csf: vendor
	vendor/bin/phpcbf --cache=tmp/codesniffer.dat --standard=ruleset.xml --colors -nsp src tests

phpstan: vendor
	vendor/bin/phpstan analyse -l 7 -c phpstan.src.neon src
	vendor/bin/phpstan analyse -l 1 -c phpstan.tests.neon tests

# Tests

tests: vendor
	vendor/bin/phpunit

coverage-clover: vendor
	phpdbg -qrr vendor/bin/phpunit --coverage-clover tmp/coverage.xml

coverage-html: vendor
	phpdbg -qrr vendor/bin/phpunit --coverage-html tmp/coverage-html
