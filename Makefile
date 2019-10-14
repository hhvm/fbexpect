.PHONY: test hh_autoload composer

COMPOSER=~/.cache/composer/composer.phar

test: hh_autoload
	./vendor/bin/hacktest tests

lint: hh_autoload
	./vendor/bin/hhast-lint

autoload:
	./vendor/bin/hh-autoload

composer-fetch:
	test -f $(COMPOSER) || mkdir -p `dirname $(COMPOSER)`
	test -f $(COMPOSER) || wget -O $(COMPOSER) https://getcomposer.org/download/1.9.0/composer.phar

composer-install: composer-fetch
	php $(COMPOSER) install

composer-update: composer-fetch
	php $(COMPOSER) update

format:
	find . -iregex '.*\.\(php\|hack\)$$' -exec ./format.sh {} \;