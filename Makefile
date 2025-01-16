# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec -ti php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build build-no-cache up up-detach start start-detach start-fresh down logs sh bash enter inside container composer vendor compinstall sf cc cache ddc create ddd drop migration dmm migrate dfl fixtures test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build

build-no-cache: ## Builds the Docker images from scratch with --no-cache
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub with logs
	@$(DOCKER_COMP) up

up-detach: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

start-detach: build up-detach ## Build and start the containers in detached mode

start-fresh: build-no-cache up ## Build and attempt to pull a newer version of images, then start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs (when detached mode)
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Get inside main container
	@$(PHP_CONT) /bin/bash

enter: bash ## Alias for `bash` shortcut

inside: bash ## Alias for `bash` shortcut

container: bash ## Alias for `bash` shortcut

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)


## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

compinstall: c=install ## Install composer packages based on composer.lock (env=dev)
compinstall: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

cache: cc ## Alias for `cc` shortcut

ddc: c=doctrine:database:create ## Create database with Doctrine command
ddc: sf

create: ddc ## Alias for `ddc` shortcut

ddd: c=doctrine:database:drop --force ## Force to drop database with Doctrine command
ddd: sf

drop: ddd ## Alias for `ddd` shortcut

migration: c=make:migration ## Create a new migration based on database changes
migration: sf

dmm: c=doctrine:migrations:migrate ## Process migrations with Doctrine command to amend database
dmm: sf

migrate: dmm ## Alias for `dmm` shortcut

dfl: c=doctrine:fixtures:load --no-interaction ## Load App fixtures directly with no confirmation
dfl: sf

fixtures: dfl ## Alias for `dfl` shortcut
