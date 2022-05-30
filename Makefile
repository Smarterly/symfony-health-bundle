.ONESHELL:
SHELL:=/usr/bin/env sh
ROOT_DIR:="$(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))"
ENV_LOCAL_FILE:=".env.local"

DOCKER_COMPOSE:=docker-compose -f docker-compose.yml --env-file=${ENV_LOCAL_FILE}
DOCKER_LOG_LEVEL:=INFO
DOCKER_BUNDLE_CONTAINER:="bundle"
.DEFAULT: help
.PHONY: help
ifndef VERBOSE
.SILENT:
endif

.test-env-local:
	echo "Creating ${ENV_LOCAL_FILE} if it doesn't exist";
	bash -c "[[ -f ${ENV_LOCAL_FILE} ]] || make .create-env-local";

.create-env-local:
	echo '${ENV_LOCAL_FILE} not found, creating...';
	bash -c "cp .env ${ENV_LOCAL_FILE}";
	echo '\nHOST_NGINX_PORT=9010' >> ${ENV_LOCAL_FILE};

setup: ENV_LOCAL = 'dev'
setup: .test-env-local init

build-all: .down
	$(DOCKER_COMPOSE) build --parallel;

build-app:
	echo 'Building `app` image...';
	$(DOCKER_COMPOSE) build $(DOCKER_BUNDLE_CONTAINER);

init: up craft

.up-quiet: DOCKER_LOG_LEVEL = CRITICAL
.up-quiet:
	@$(MAKE) .up >/dev/null;

.up:
	$(DOCKER_COMPOSE) --log-level $(DOCKER_LOG_LEVEL) up -d;

.down:
	$(DOCKER_COMPOSE) down --remove-orphans;

down:
	echo 'Bringing down services...';
	$(MAKE) .down;

up:
	echo 'Bringing up services...';
	$(MAKE) .up;

reload: down up

logs:
	$(DOCKER_COMPOSE) logs -f -t;

run: reload logs

login:
	 $(DOCKER_COMPOSE) exec $(DOCKER_BUNDLE_CONTAINER) /bin/sh;

.composer-%: .up-quiet
	$(DOCKER_COMPOSE) exec $(DOCKER_BUNDLE_CONTAINER) composer $*;

install:
	$(MAKE) .composer-install;

update:
	echo 'Running composer update...'
	$(MAKE) .composer-update;

metrics:
	echo 'Generating metrics...'
	$(MAKE) .composer-metrics;

phpcs:
	$(MAKE) .composer-phpcs;

phpcbf:
	$(MAKE) .composer-phpcbf;

behat:
	echo 'Running composer behat...'
	$(MAKE) .composer-behat;

phpunit:
	echo 'Running composer phpunit...'
	$(MAKE) .composer-phpunit;

psalm:
	echo 'Running composer psalm...'
	$(MAKE) .composer-psalm;

infection:
	$(MAKE) .composer-infection;

test:
	echo 'Running all tests defined in `composer test`'
	$(MAKE) .composer-test;

fix:
	echo 'Fixing errors...'
	$(MAKE) .composer-fix;

quality:
	echo 'Checking branch for quality...'
	$(MAKE) .composer-quality;

.craft:
	@echo "\033[92mCrafting excellence...\033[0m"

craft: .craft test quality

console: .composer-console

health: APP_ENV = prod
health: .composer-health

brew:
	brew bundle install;

commit: craft metrics
	git add docs/metrics;
	git commit;

