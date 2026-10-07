# SIRR — Sistema para el Registro de Reservas

Aplicación web de reservas de Living Night Club: los clientes reservan mesa y el personal confirma llegadas, asigna mesas y administra el salón y los eventos.

PHP 7.4 sin framework, MySQL/MariaDB, y en el navegador Bootstrap 3 con JavaScript propio.

## Qué hace

- **Clientes:** registro, inicio de sesión, recuperación de contraseña, crear, modificar y cancelar reservas.
- **Administración:** confirmar reservas, asignar y trasladar mesas, levantar reservas que no llegaron, listados del día y clientes VIP.
- **Tablero del salón:** plano de mesas por fecha con filtros y gestión de cada mesa en una ventana.
- **Eventos:** conciertos con imagen frontal y de precios; alimentan la cartelera del inicio de sesión y el formulario de reserva.

## Estructura

```
├── *.php                archivos de entrada de 4 líneas; la lógica está en app/
├── app/
│   ├── rutas.php        qué controlador atiende cada página y cada acción, y con qué nivel de acceso
│   ├── nucleo/          base de datos, sesión, despachador, entrada, subida de imágenes
│   ├── modelos/         todo el SQL
│   ├── controladores/   Acceso, Reserva, Gestion, Salon, ClienteVip, Evento
│   └── vistas/          HTML
├── css/ js/ images/     recursos públicos
├── sql/                 migraciones, para ejecutar en orden sobre la base existente
├── docker/              entorno local
└── tests/               pruebas de regresión y de navegador
```

Las páginas se atienden por su nombre de archivo (`dashboard.php`, `reserva2.php`, …) y las acciones AJAX por `Querys.php?guard=<acción>`. Ambas tablas están en `app/rutas.php`, que es el mejor punto de partida para leer el código.

Cada ruta declara su nivel de acceso: `publico`, `sesion` (cualquier usuario conectado) o `admin`. Una acción que no esté en la tabla se trata como de administrador.

## Entorno local

Requiere [podman](https://podman.io/).

```bash
docker/levantar.sh          # crea o arranca la app y la base
docker/levantar.sh parar    # los detiene; los datos se conservan
docker/levantar.sh borrar   # elimina contenedores y datos
```

- App: http://localhost:8080/
- Base: `127.0.0.1:3307`, usuario `sirr`, clave `sirr_local`, base `livingni_living`

El repositorio no incluye datos. Hay que cargar un volcado de la base y después las migraciones:

```bash
podman exec -i sirr-db mariadb -usirr -psirr_local livingni_living < data/livingni_living.sql
for f in sql/*.sql; do podman exec -i sirr-db mariadb -usirr -psirr_local livingni_living < "$f"; done
```

La carpeta `data/` está fuera de git porque los volcados traen datos de clientes.

En local no se envían correos: el contenedor no tiene servidor de correo.

## Pruebas

Ambas reinician la base local con `data/livingni_living.sql`, las migraciones y `tests/datos_prueba.sql`.

```bash
tests/regresion.sh /tmp/salida   # 150 peticiones: todas las páginas y acciones como visitante, cliente y administrador
python3 tests/tablero.py         # tablero del salón en Firefox sin ventana (necesita geckodriver)
```

`regresion.sh` guarda cada respuesta en la carpeta indicada; para detectar cambios se comparan dos corridas con `diff -r`.

Usuarios de prueba, con clave `Prueba123`: `admin.prueba@example.com` y `cliente.prueba@example.com`.

## Despliegue

1. Copiar `sirr-config.ejemplo.php` como `sirr-config.php` **un nivel arriba** de la carpeta pública y poner ahí las credenciales de la base.
2. Ejecutar las migraciones de `sql/` que falten.
3. Subir todo menos `docker/`, `tests/`, `sql/` y `data/`.
4. Dar permiso de escritura a `images/Eventos` y comprobar que el límite de subida de PHP sea de al menos 5 MB.

El servidor debe ser Apache 2.4: los `.htaccess` bloquean el acceso web a `app/` y a las carpetas de desarrollo.
