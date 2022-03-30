.PHONY: help

help:
	@printf "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)\n"

release/php-extras.tar.gz: ## Make release
	mkdir -p release
	tar -zcf release/php-extras.tar.gz *.php
