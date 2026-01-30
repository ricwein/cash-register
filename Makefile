.PHONY: build
build:
	docker compose build --pull

release:
	docker compose -f compose.yaml -f compose.build.yaml build --pull

release-clean:
	docker compose -f compose.yaml -f compose.build.yaml build --pull --no-cache

push:
	docker compose -f compose.yaml -f compose.build.yaml push
