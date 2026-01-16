.PHONY: help test-up test-down test clean

help:
	@printf "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)\n"

release/php-extras.tar.gz: ## Make release
	mkdir -p release
	tar -zcf release/php-extras.tar.gz *.php

test-up: ## Install dependencies
	docker compose up -d
	docker compose exec php bash -c "composer install"

test-down: ## Tear down tests
	docker compose down

test: test-up ## Run PHPUnit tests
	docker compose exec php /app/vendor/bin/phpunit

clean: test-down ## Cleanup release and vendor
	rm -rf release vendor
