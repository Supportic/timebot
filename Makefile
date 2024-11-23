.SILENT:
.ONESHELL:
SHELL = /bin/bash
TERMINAL = docker compose exec php bash
NODE = docker compose run --rm -it --service-ports node
NODE_RUN = docker compose run --rm -it node
SYMFONY = docker compose run --rm --entrypoint symfony php
COMPOSER = docker compose run --rm php composer
NPM = docker compose run --service-ports --rm -it --entrypoint npm node

config:
	docker compose -f compose.yaml -f .devcontainer/compose.yaml config

shell:
	$(TERMINAL)
shell-node:
	$(NODE_RUN)

start:
	docker compose up -d nginx php db adminer mailpit
stop:
	docker compose stop
down:
	docker compose down
erase:
	docker compose down -v --rmi all

update:
	$(COMPOSER) update
	$(NPM) update

cl:
	$(SYMFONY) console cache:clear

install: install-container install-deps recreate-db build-assets

install-container:
	docker compose build php node adminer || exit 1
	docker compose up -d nginx php db adminer mailpit || exit 1
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-deps:
	$(COMPOSER) install
	$(NPM) ci --no-audit --verbose
	# $(NPM) ci --no-audit --loglevel=silly

build-assets:
	$(NPM) run build
watch-assets:
	$(NPM) run dev

recreate-db:
	$(SYMFONY) console doctrine:database:drop --if-exists --no-interaction --force --connection=default
	$(SYMFONY) console doctrine:database:create --connection=default
	$(SYMFONY) console doctrine:schema:create --em=default
	$(SYMFONY) console doctrine:fixtures:load --no-interaction

reset-db:
	$(SYMFONY) console doctrine:schema:drop --em=default --full-database --force
	$(SYMFONY) console doctrine:schema:create --em=default
	$(SYMFONY) console doctrine:fixtures:load --no-interaction

create-migration:
	$(SYMFONY) console doctrine:fixtures:load --no-interaction
	$(SYMFONY) console doctrine:migrations:migrate --no-interaction
