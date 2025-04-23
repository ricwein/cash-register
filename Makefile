hello:
	docker compose -f compose.yaml -f compose.build.yaml build --pull
	docker compose -f compose.yaml -f compose.build.yaml push

dev:
	docker compose build --pull

clean:
	docker compose -f compose.yaml -f compose.build.yaml build --pull --no-cache
	docker compose -f compose.yaml -f compose.build.yaml push
