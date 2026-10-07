#!/usr/bin/env bash
# Prueba de regresion: reinicia la base local con datos conocidos, recorre todas las paginas y acciones
# y guarda cada respuesta en un directorio. Comparar dos corridas con: diff -r dirA dirB
#   tests/regresion.sh <directorio_salida>
set -uo pipefail
cd "$(dirname "$0")/.."
SALIDA="${1:?uso: tests/regresion.sh <directorio_salida>}"
B=http://localhost:8080
DBROOT="podman exec -i sirr-db mariadb -uroot -proot_local"
rm -rf "$SALIDA"; mkdir -p "$SALIDA"
JAR=$(mktemp -d)

# --- base de datos en estado conocido
$DBROOT -e "DROP DATABASE IF EXISTS livingni_living; CREATE DATABASE livingni_living; GRANT ALL ON livingni_living.* TO 'sirr'@'%';"
$DBROOT livingni_living < data/livingni_living.sql
$DBROOT livingni_living < sql/2026-10-05_claves.sql
$DBROOT livingni_living < sql/2026-10-06_mesa_91.sql
$DBROOT livingni_living < tests/datos_prueba.sql

N=0
# pedir <nombre> <cookies: -|c|a> <argumentos curl...>
pedir(){
	local nombre="$1" quien="$2"; shift 2
	N=$((N+1))
	local archivo; archivo=$(printf '%s/%03d_%s.txt' "$SALIDA" "$N" "$nombre")
	local cookies=()
	[ "$quien" != "-" ] && cookies=(-b "$JAR/$quien" -c "$JAR/$quien")
	curl -s "${cookies[@]}" -w '\n[HTTP %{http_code}] [redirige: %{redirect_url}]\n' "$@" \
		| sed -E 's/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/<FECHAHORA>/g; s/&copy; [0-9]{4}/\&copy; <ANIO>/g' > "$archivo"
}
Q(){ pedir "$1" "$2" -X POST "$B/Querys.php" "${@:3}"; }
P64=$(printf 'Prueba123' | base64)
D=12/12/2026; DY=2026-12-12

# --- publico
pedir registro - "$B/registro.php"
pedir rpas - "$B/rpas.php"
pedir hdata - "$B/hdata.php"
pedir restablecer_malo - "$B/restablecer.php?token=zzz"
pedir restablecer_bueno - "$B/restablecer.php?token=tokendeprueba123"
Q M_existe - -d guard=M -d correo=admin.prueba@example.com
Q M_noexiste - -d guard=M -d correo=nadie@example.com
Q P_noexiste - -d guard=P -d user=nadie@example.com -d passC=x
Q P_inyeccion - -d guard=P --data-urlencode "user=' OR '1'='1" -d passC=x
Q P_malaclave - -d guard=P -d user=admin.prueba@example.com -d passC=x
Q P_inactivo - -d guard=P -d user=inactivo.prueba@example.com --data-urlencode "passC=$P64"
Q P_anterior - -d guard=P -d user=anterior.prueba@example.com --data-urlencode "passC=$P64"
pedir active_1 - "$B/active.php?id=902"
pedir active_2 - "$B/active.php?id=902"
Q RCE_existe - -d guard=RCE -d mail=cliente.prueba@example.com
Q RCE_noexiste - -d guard=RCE -d mail=nadie@example.com
Q RST_malo - -d guard=RST -d token=zzz --data-urlencode "pass=$P64"
Q R_registro - -d guard=R --data-urlencode "name=Nuevo d'Prueba" -d apell=Usuario -d genero=M -d fechanac=1999-09-09 -d mail=Nuevo.Prueba@example.com -d Ocupacion=2 -d telefono=310 -d trumba=2 --data-urlencode "pass=$P64" -d news=S
pedir welcome_sinlogin - "$B/welcome.php?login=admin.prueba@example.com"
pedir main_sinsesion - "$B/main.php"
pedir dashboard_sinsesion - "$B/dashboard.php?fecha=$DY"
Q CR_sinsesion - -d guard=CR -d NumReserva=101
Q vacio_sinsesion -

# --- cliente
Q P_cliente c -d guard=P -d user=cliente.prueba@example.com --data-urlencode "passC=$P64"
pedir welcome_cliente c "$B/welcome.php?login=admin.prueba@example.com"
pedir main_cliente c "$B/main.php"
pedir reserva2_cliente c "$B/reserva2.php?Tipo=I&MA=0"
pedir consulta_cliente c "$B/consultaReservas.php"
pedir reserva_propia c "$B/reserva.php?fecha=$D&Tipo=U&Rnum=106&MA="
pedir reserva_propia_mesa c "$B/reserva.php?fecha=$D&Tipo=U&Rnum=101&MA=10"
pedir reserva_ajena c "$B/reserva.php?fecha=$D&Tipo=U&Rnum=102&MA="
pedir correoReserva_propia c "$B/correoReserva.php?reserva=106"
pedir correoReserva_ajena c "$B/correoReserva.php?reserva=102"
Q ResForm_cli_sabado c -d guard=ResForm --data-urlencode "Fecha=$D" -d IdLiving=900 -d Perfil=1 -d tipo=I -d RNum=0
Q ResForm_cli_domingo c -d guard=ResForm --data-urlencode "Fecha=13/12/2026" -d IdLiving=901 -d Perfil=0 -d tipo=I -d RNum=0
Q ResForm_cli_lunes c -d guard=ResForm --data-urlencode "Fecha=14/12/2026" -d IdLiving=901 -d Perfil=0 -d tipo=I -d RNum=0
Q ResForm_cli_pasada c -d guard=ResForm --data-urlencode "Fecha=01/01/2020" -d IdLiving=901 -d Perfil=0 -d tipo=I -d RNum=0
Q ResForm_cli_festivo c -d guard=ResForm --data-urlencode "Fecha=08/12/2026" -d IdLiving=901 -d Perfil=0 -d tipo=I -d RNum=0
Q ResForm_cli_U c -d guard=ResForm --data-urlencode "Fecha=$D" -d IdLiving=901 -d Perfil=0 -d tipo=U -d RNum=106
Q RR_cliente c -d guard=RR -d id=900 --data-urlencode "NombreRese=Mesa de O'Brien \"x\"" -d Numper=6 -d TipoR=1 --data-urlencode "Fecha=$D" -d NHomena=Ana --data-urlencode $'Observa=linea1\nlinea2; DROP TABLE x' -d ClienteVIP=900
Q RR_duplicada c -d guard=RR -d id=901 -d NombreRese=ANIVERSARIO -d Numper=2 -d TipoR=2 --data-urlencode "Fecha=$D" -d NHomena= -d Observa= -d ClienteVIP=0
Q RU_propia c -d guard=RU -d id=901 --data-urlencode "NombreRese=Aniversario 2" -d Numper=4 -d TipoR=2 --data-urlencode "Fecha=$D" -d NHomena=Los dos -d Observa=ventana -d NumReserva=106
Q RU_ajena c -d guard=RU -d id=901 -d NombreRese=HACK -d Numper=4 -d TipoR=2 --data-urlencode "Fecha=$D" -d NHomena=x -d Observa=x -d NumReserva=102
pedir E_ajena c "$B/Querys.php?guard=E&Reserva=102"
pedir E_propia c "$B/Querys.php?guard=E&Reserva=108"
Q CR_cliente c -d guard=CR -d NumReserva=101
Q BRF_cliente c -d guard=BRF -d fecha=$DY -d tipo=1
for pg in "dashboard.php?fecha=$DY" ConfReserva.php "ListarReservas.php?tipo=1" "GestionMesa.php?mesa=10&fecha=$DY" "reservoir.php?fecha=$DY&orden=1" "ListaCumple.php?fecha=$DY"; do
	pedir "cliente_en_$(echo "$pg" | cut -d. -f1)" c "$B/$pg"
done
pedir consulta_cliente_despues c "$B/consultaReservas.php"

# --- administrador: paginas
Q P_admin a -d guard=P -d user=admin.prueba@example.com --data-urlencode "passC=$P64"
pedir welcome_admin a "$B/welcome.php"
for pg in main.php "dashboard.php?fecha=$DY" "dashboard.php?fecha=$D" "dashboard.php?fecha=2026-12-20" "ListaCumple.php?fecha=$DY" "ListaGrado.php?fecha=2026-12-20" \
          "ListarReservas.php?tipo=1" "ListarReservas.php?tipo=2" ConfReserva.php AsignaMesa.php levantaReservas.php GestionSalon.php \
          "GestionMesa.php?mesa=10&fecha=$DY" "GestionMesa.php?mesa=14&fecha=$DY" "GestionMesa.php?mesa=97&fecha=$DY" "GestionMesa.php?mesa=13&fecha=$DY" "GestionMesa.php?mesa=12&fecha=$DY" \
          "GestionMesaA.php?reserva=100" "GestionMesaA.php?reserva=102" "reservoir.php?fecha=$DY&orden=1" "reservoir.php?fecha=$D&orden=2" \
          "Listados.php?fecha=$DY&tipo=K" "Listados.php?fecha=$DY&tipo=Cu" "Listados.php?fecha=$DY&tipo=N" "Listados.php?fecha=$DY&tipo=C" \
          consultaReservas.php "reserva2.php?Tipo=I&MA=0" "reserva.php?fecha=$D&Tipo=U&Rnum=101&MA=10" Salon.php; do
	pedir "admin_$(echo "$pg" | tr -c 'A-Za-z0-9\n' '_')" a "$B/$pg"
done

# --- administrador: consultas
Q BRF_1 a -d guard=BRF -d fecha=$DY -d tipo=1
Q BRF_2 a -d guard=BRF -d fecha=$DY -d tipo=2
Q BRF_vacio a -d guard=BRF -d fecha=2027-01-01 -d tipo=1
Q BR_existe a -d guard=BR -d Reserva=101
Q BR_noexiste a -d guard=BR -d Reserva=99999
Q CR_conmesa a -d guard=CR -d NumReserva=101
Q CR_sinmesa a -d guard=CR -d NumReserva=100
Q CR_efectiva a -d guard=CR -d NumReserva=102
Q CR_noexiste a -d guard=CR -d NumReserva=99999
Q CR_inyeccion a -d guard=CR --data-urlencode "NumReserva=101 OR 1=1"
Q CRF a -d guard=CRF --data-urlencode "Fecha=$D"
Q CRF_vacio a -d guard=CRF --data-urlencode "Fecha=01/01/2027"
Q CAM a -d guard=CAM -d cantMesas=2 --data-urlencode "fecha=$D"
Q BCVIP a -d guard=BCVIP -d Nom=vip
Q BCVIP_todos a -d guard=BCVIP -d Nom=
for c in 1 2 3 4 9; do Q CCliVip_$c a -d guard=CCliVip -d Cod=$c -d fecha=$DY; done
Q LRes a -d guard=LRes --data-urlencode "Fecha=$D"
Q LRes_vacio a -d guard=LRes --data-urlencode "Fecha=01/01/2027"
Q TMCD a -d guard=TMCD -d fecha=$DY
Q BMOD a -d guard=BMOD -d fecha=$DY
for t in 1 2 3 4 5 6; do Q STRE_$t a -d guard=STRE -d fecha=$DY -d tipo=$t; done
Q ResForm_admin a -d guard=ResForm --data-urlencode "Fecha=$D" -d IdLiving=900 -d Perfil=1 -d tipo=I -d RNum=0
Q ResForm_admin_U a -d guard=ResForm --data-urlencode "Fecha=$D" -d IdLiving=900 -d Perfil=1 -d tipo=U -d Rnum=101
Q desconocida a -d guard=NOEXISTE

# --- administrador: cambios
Q RR_admin a -d guard=RR -d id=900 -d NombreRese=DelAdmin -d Numper=4 -d TipoR=2 --data-urlencode "Fecha=$D" -d NHomena= -d Observa= -d ClienteVIP=900
Q AMR a -d guard=AMR --data-urlencode "Fecha=$D" -d Reserva=100 -d CMesas=2 -d REspecial=false -d AMesas=15,16
Q AMR1_estado1 a -d guard=AMR1 --data-urlencode "Fecha=$D" -d Reserva=106 -d CMesas=1 -d AMesas=17 -d TRes=true -d Estado=1
Q AMR1_estado3 a -d guard=AMR1 --data-urlencode "Fecha=$D" -d Reserva=102 -d CMesas=1 -d AMesas=B2 -d TRes=false -d Estado=3
Q CRE a -d guard=CRE -d NumReserva=101
Q ConfEntrM a -d guard=ConfEntrM -d cumpli=S -d RNum=101
Q AMCP_grande a -d guard=AMCP -d Reserva=104 -d CantPer=12
Q AMCP_chica a -d guard=AMCP -d Reserva=101 -d CantPer=5
Q INMM_1 a -d guard=INMM -d mesa=10 -d reserva=101 -d tipof=C
Q INMM_2 a -d guard=INMM -d mesa=10 -d reserva=101 -d tipof=CI
Q TMS a -d guard=TMS -d Reserva=105 -d MesaO=P1 -d MesaD=P2 -d Fecha=$DY -d MRConse=204
Q IRCVIP a -d guard=IRCVIP -d Mesa=P3 -d FechaRes=$DY --data-urlencode "Nombre=Grupo D'Angelo" -d NumPer=5 -d TipoCelebra=2 -d NombreH=Ella -d Observa=obs -d IdLiving=900 -d IdCliVIP=900 -d TipoAsigna=V
Q IRCVIP_ocupada a -d guard=IRCVIP -d Mesa=P3 --data-urlencode "FechaRes=$D" -d Nombre=Otro -d NumPer=5 -d TipoCelebra=1 -d NombreH= -d Observa= -d IdLiving=900 -d IdCliVIP=0 -d TipoAsigna=F
Q IRCVIP_cumple a -d guard=IRCVIP -d Mesa=P4 --data-urlencode "FechaRes=$D" -d Nombre=Cumple -d NumPer=5 -d TipoCelebra=1 -d NombreH=El -d Observa= -d IdLiving=900 -d IdCliVIP=0 -d TipoAsigna=F
Q ARMN a -d guard=ARMN -d mesa=18 -d reserva=500
Q ARMN_ocupada a -d guard=ARMN -d mesa=18 -d reserva=501
Q LMM_det1 a -d guard=LMM -d Mesa=13 -d Fecha=$DY -d Reserva=107 -d MesaN= -d centi=1
Q LMM_det0 a -d guard=LMM -d Mesa=15 -d Fecha=$DY -d Reserva=100 -d MesaN=16 -d centi=0
Q RCV_si a -d guard=RCV -d Confirma=T -d identLiv=900 --data-urlencode "name=NUEVO VIP D'X" -d genero=F -d telefono=312 -d trumba=2 -d Observa=primera
Q RCV_no a -d guard=RCV -d Confirma=F -d identLiv=900 -d name=NO -d genero=F -d telefono=312 -d trumba=2 -d Observa=x
Q ACV a -d guard=ACV -d IdClienteVip=900 -d IdLiving=900 -d Nombre=VIP\ CAMBIADO -d Genero=F -d TRumba=3 -d Telefono=399 --data-urlencode "VObserva=[2026-01-01 10:00:00]: creado" -d NObserva=segunda
Q ACV_sinobs a -d guard=ACV -d IdClienteVip=1000 -d IdLiving=900 -d Nombre=OTRO -d Genero=M -d TRumba=1 -d Telefono=1 -d VObserva=igual -d NObserva=
pedir E_admin a "$B/Querys.php?guard=E&Reserva=106"
Q LResScript a -d guard=LResScript -d Fecha=$DY -d ResMan=100,0
Q RST_bueno - -d guard=RST -d token=tokendeprueba123 --data-urlencode "pass=$(printf 'OtraClave1' | base64)"
Q RST_reusado - -d guard=RST -d token=tokendeprueba123 --data-urlencode "pass=$P64"
Q P_clave_nueva - -d guard=P -d user=cliente.prueba@example.com --data-urlencode "passC=$(printf 'OtraClave1' | base64)"
pedir dashboard_final a "$B/dashboard.php?fecha=$DY"
pedir reservoir_final a "$B/reservoir.php?fecha=$DY&orden=1"
pedir desconexion a "$B/desconexion.php"
pedir main_tras_salir a "$B/main.php"

# --- estado final de la base
N=$((N+1))
podman exec sirr-db mariadb -usirr -psirr_local livingni_living -e "
	SELECT * FROM tblreservas ORDER BY 1;
	SELECT * FROM tblmesareserva ORDER BY 1;
	SELECT * FROM tblclientesvip WHERE IDCLIENTEVIP>=900 ORDER BY 1;
	SELECT NUMIDENTLIV,STRNOMBRES,STRAPELLIDOS,STRGENERO,DTMFECHNAC,STRMAIL,NUMCODOCUPACION,NUMTIPORUMBA,STRPASSWORD LIKE '\$2y\$%' AS con_hash,STRRECNOTICIAS,STRACTIVO FROM tblusuarios WHERE NUMIDENTLIV>=900 ORDER BY 1;
	SELECT * FROM tblusuariosperfiles WHERE NUMIDENTLIV>=900 ORDER BY 1;
	SELECT NUMIDENTLIV, COUNT(*) FROM tblrecuperaclave GROUP BY 1;" \
	| sed -E 's/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/<FECHAHORA>/g' > "$(printf '%s/%03d_estado_final_bd.txt' "$SALIDA" "$N")"
rm -rf "$JAR"
echo "$N respuestas en $SALIDA"
