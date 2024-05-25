.SILENT:
.ONESHELL:
SHELL = /bin/bash
TERMINAL = docker compose exec php bash
NODE = docker compose run --rm --service-ports node
SYMFONY = docker compose run --rm --entrypoint symfony php
COMPOSER = docker compose run --rm php composer
NPM = docker compose run --rm --entrypoint npm node

config:
	docker compose -f compose.yaml -f .devcontainer/compose.yaml config

shell:
	$(TERMINAL)
shell-node:
	$(NODE)

start:
	docker compose up -d nginx php db adminer mailpit
stop:
	docker compose stop
down:
	docker compose down
erase:
	docker compose down -v --rmi all

install: install-container install-deps

install-container:
	docker compose build php node adminer || exit 1
	docker compose up -d nginx php db adminer mailpit || exit 1
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-deps:
	$(COMPOSER) install
	$(NPM) ci

update:
	$(COMPOSER) update
	$(NPM) update
