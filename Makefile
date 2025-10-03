.SILENT:
.ONESHELL:
SHELL = /bin/bash
MAKEFLAGS += --no-print-directory

TERMINAL = docker compose exec php bash
NODE = docker compose run --rm -it --service-ports node
NODE_RUN = docker compose run --rm -it node
SYMFONY = docker compose run --rm --entrypoint symfony php
COMPOSER = docker compose run --rm php composer
NPM = docker compose run --service-ports --rm -it --entrypoint npm node

shell:
	$(TERMINAL)
shell-node:
	$(NODE_RUN)

update:
	$(COMPOSER) update
	$(NPM) update

cc:
	$(SYMFONY) console cache:clear

start:
	docker compose up -d nginx php db adminer mailpit
stop:
	docker compose stop
down:
	docker compose down
restart: down start
remove:
	docker compose down -v
erase:
	docker compose down -v --rmi all

install: install-precondition install-image install-deps recreate-db build-assets
rebuild: remove install-precondition install-image
reinstall: remove install

install-precondition:
	@if [ ! -f .env ]; then\
		echo "Copy and adjust values .env.sample => .env";\
		exit 1;\
	fi

install-image:
	docker compose build --pull php node
	docker compose pull db adminer mailpit
	$(MAKE) start
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") > /dev/null 2>&1 || true

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
