#!/usr/bin/env bash
# Levanta SIRR en local con podman: un contenedor con la app y otro con la base de datos.
#   docker/levantar.sh          crea o arranca los contenedores
#   docker/levantar.sh parar    los detiene (los datos se conservan)
#   docker/levantar.sh borrar   elimina contenedores y datos de la base
set -euo pipefail
cd "$(dirname "$0")/.."

RED=sirr
APP=sirr-app
DB=sirr-db
VOLUMEN=sirr-db-datos
PUERTO_APP=8080
PUERTO_DB=3307

case "${1:-}" in
	parar)
		podman stop "$APP" "$DB"
		exit 0
		;;
	borrar)
		podman rm -f "$APP" "$DB" 2>/dev/null || true
		podman volume rm "$VOLUMEN" 2>/dev/null || true
		podman network rm "$RED" 2>/dev/null || true
		exit 0
		;;
esac

podman network exists "$RED" || podman network create "$RED" >/dev/null
podman build -q -t sirr-app-img docker/ >/dev/null

# Base de datos. Los .sql de docker/initdb/ se cargan solo la primera vez (volumen vacio).
if podman container exists "$DB"; then
	podman start "$DB" >/dev/null
else
	podman run -d --name "$DB" --network "$RED" \
		-p "127.0.0.1:$PUERTO_DB:3306" \
		-e MARIADB_ROOT_PASSWORD=root_local \
		-e MARIADB_DATABASE=livingni_living \
		-e MARIADB_USER=sirr \
		-e MARIADB_PASSWORD=sirr_local \
		-e TZ=America/Bogota \
		-v "$VOLUMEN":/var/lib/mysql \
		-v "$PWD/docker/initdb":/docker-entrypoint-initdb.d:ro,Z \
		docker.io/library/mariadb:10.6 >/dev/null
fi

# App: el codigo se monta en vivo, y la configuracion un nivel arriba de la carpeta publica.
if podman container exists "$APP"; then
	podman start "$APP" >/dev/null
else
	# keep-id hace que Apache (www-data, uid 33) sea tu usuario: asi puede guardar las imagenes de eventos en images/Eventos
	podman run -d --name "$APP" --network "$RED" \
		--userns=keep-id:uid=33,gid=33 --user 0:0 \
		-p "127.0.0.1:$PUERTO_APP:80" \
		-v "$PWD":/var/www/html:Z \
		-v "$PWD/docker/sirr-config.php":/var/www/sirr-config.php:ro,Z \
		sirr-app-img >/dev/null
fi

echo "App:  http://localhost:$PUERTO_APP/"
echo "Base: 127.0.0.1:$PUERTO_DB  (usuario sirr / sirr_local, base livingni_living)"
