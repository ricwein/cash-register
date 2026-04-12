.PHONY: build
build:
	docker compose build --pull

release:
	docker compose -f compose.yaml -f compose.build.yaml -f compose.build-multiarch.yaml build --pull

release-clean:
	docker compose -f compose.yaml -f compose.build.yaml -f compose.build-multiarch.yaml build --pull --no-cache

push:
	docker compose -f compose.yaml push

backup:
	@if docker compose ps db | grep -q "Up"; then \
		echo "Using running DB container..."; \
		docker compose exec db sh -c 'mariadb-dump -h 127.0.0.1 -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MARIADB_DATABASE"' > backup.sql; \
	else \
		echo "Starting temporary DB container..."; \
		docker compose up -d db; \
		docker compose exec db sh -c 'until mariadb -h 127.0.0.1 -u root -p"$$MYSQL_ROOT_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do sleep 1; done'; \
		docker compose exec db sh -c 'mariadb-dump -h 127.0.0.1 -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MARIADB_DATABASE"' > backup.sql; \
		docker compose down; \
	fi

update:
	git pull; \
    docker compose pull; \
    docker compose down && docker compose up -d
