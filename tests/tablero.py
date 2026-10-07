#!/usr/bin/env python3
# Prueba en navegador del tablero del salon (dashboard.php): estado inicial y cada boton de filtro.
# Requiere los contenedores arriba y Firefox con geckodriver. Reinicia la base local con los datos de prueba.
#   python3 tests/tablero.py
import os, subprocess, sys, time
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from navegador import Nav

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
BASE = 'http://localhost:8080/'
FECHA = '2026-12-12'

def reiniciar_bd():
    root = ['podman', 'exec', '-i', 'sirr-db', 'mariadb', '-uroot', '-proot_local']
    subprocess.run(root + ['-e', "DROP DATABASE IF EXISTS livingni_living; CREATE DATABASE livingni_living; GRANT ALL ON livingni_living.* TO 'sirr'@'%';"], check=True)
    for archivo in ('data/livingni_living.sql', 'sql/2026-10-05_claves.sql', 'sql/2026-10-06_mesa_91.sql', 'tests/datos_prueba.sql'):
        subprocess.run(root + ['livingni_living'], stdin=open(os.path.join(RAIZ, archivo)), check=True)

# Una mesa esta "a la vista" si no esta oculta; "con color" si ademas tiene un fondo de asignacion.
ESTADO = """var r={visibles:[], color:[]};
[].forEach.call(document.getElementsByClassName('mesa'), function(e){
    var s=getComputedStyle(e);
    if(s.visibility!='hidden'){ r.visibles.push(e.id); if(s.backgroundColor!='rgba(0, 0, 0, 0)'){ r.color.push(e.id); } }
});
return r;"""

fallos = []
def comprobar(nombre, obtenido, esperado):
    bien = sorted(obtenido) == sorted(esperado)
    print(('  ok    ' if bien else '  FALLA ') + nombre + ('' if bien else ': se obtuvo %s, se esperaba %s' % (sorted(obtenido), sorted(esperado))))
    if not bien:
        fallos.append(nombre)

reiniciar_bd()
n = Nav()
assert n.entrar('admin.prueba@example.com') == 'UO'
n.ir(BASE + 'dashboard.php?fecha=' + FECHA); time.sleep(1.5)
n.js("window.__errores=[]; window.onerror=function(m){ window.__errores.push(String(m)); };")
todas = n.js("return [].map.call(document.getElementsByClassName('mesa'), function(e){ return e.id; });")
ocupadas = ['10', '11', '12', '13', 'B01', 'P1']   # segun tests/datos_prueba.sql

def pulsar(boton):
    n.js("var texto=arguments[0]; [].forEach.call(document.querySelectorAll('button'), function(b){ if(b.innerText.trim()==texto){ b.click(); } });", boton)
    time.sleep(1.2)
    return n.js(ESTADO)

e = n.js(ESTADO)
comprobar('al cargar: todas las mesas a la vista', e['visibles'], todas)
comprobar('al cargar: las ocupadas tienen color', e['color'], ocupadas)
comprobar('Vacias: solo las libres', pulsar('Vacias')['visibles'], [m for m in todas if m not in ocupadas])
for boton, esperadas in [('Reservas Especiales', ['13']), ('Reservas Confirmadas', ['B01']), ('Reservas Pendientes', ['10', '13']),
                         ('Mesas Compartidas', ['12']), ('Grupo Inamovible', []), ('Cumpleaños', ['P1'])]:
    e = pulsar(boton)
    comprobar(boton + ': mesas a la vista', e['visibles'], esperadas)
    comprobar(boton + ': conservan su color', e['color'], esperadas)
e = pulsar('Estado Actual')
comprobar('Estado Actual tras filtrar: todas a la vista', e['visibles'], todas)
comprobar('Estado Actual tras filtrar: las ocupadas con color', e['color'], ocupadas)
libre = n.js("var s=getComputedStyle(document.getElementById('14')); return [getComputedStyle(document.body).backgroundColor, s.color, s.borderTopColor];")
comprobar('tema oscuro: fondo negro, mesa libre con texto y borde blancos', libre, ['rgb(0, 0, 0)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)'])
# --- la mesa se gestiona en una ventana modal, sin salir del tablero
n.js("document.getElementById('14').click();"); time.sleep(1.5)
modal = n.js("var m=document.getElementById('modalMesa'), f=document.getElementById('marcoMesa'); return [m.classList.contains('show'), location.pathname, f.contentWindow.location.pathname+f.contentWindow.location.search, f.contentDocument.body.innerText.indexOf('Mesa 14')>=0, getComputedStyle(f.contentDocument.body).backgroundColor];")
comprobar('clic en mesa 14: abre la ventana sin salir del tablero', [str(x) for x in modal], ['True', '/dashboard.php', '/GestionMesa.php?mesa=14&fecha=' + FECHA, 'True', 'rgb(0, 0, 0)'])
# dentro de la ventana: mesa compartida para 4 personas y "Volver"
n.js("var w=document.getElementById('marcoMesa').contentWindow; w.document.tipoasigna.tcliente[2].checked=true; w.AsignacionMesa(4);"); time.sleep(1.2)
n.js("var w=document.getElementById('marcoMesa').contentWindow; w.document.getElementById('CFSe').value=4; w.AlmacenaRMComparte();"); time.sleep(1.5)
asignada = n.js("return document.getElementById('marcoMesa').contentDocument.getElementById('respuesta').innerText.indexOf('MESA ASIGNADA SATISFACTORIAMENTE')>=0;")
comprobar('asignar desde la ventana', [str(asignada)], ['True'])
n.js("document.getElementById('marcoMesa').contentWindow.GoBack();"); time.sleep(2.5)
n.js("window.__errores=[]; window.onerror=function(m){ window.__errores.push(String(m)); };")
tras = n.js("return [!document.getElementById('modalMesa').classList.contains('show'), location.pathname, getComputedStyle(document.getElementById('14')).backgroundColor!='rgba(0, 0, 0, 0)'];")
comprobar('Volver cierra la ventana y el tablero muestra la mesa 14 ocupada', [str(x) for x in tras], ['True', '/dashboard.php', 'True'])

comprobar('sin errores de JavaScript', n.js("return window.__errores;"), [])
# --- listados: incluyen las reservas activas y las efectivas, con o sin mesa
def filas(url):
    n.ir(BASE + url); time.sleep(0.8)
    return n.js("return [].slice.call(document.querySelectorAll('table tr'), 1).map(function(f){ return f.cells[1].innerText.trim()+' | '+f.cells[4].innerText.trim()+' | '+f.cells[5].innerText.trim(); });")
comprobar('listado de cumpleanos', filas('ListaCumple.php?fecha=' + FECHA), ['CUMPLE DE PEDRO | P1 | EFECTIVA', 'FIESTA ANA | Sin asignar | ACTIVA'])
comprobar('listado de grados', filas('ListaGrado.php?fecha=' + FECHA), ['GRADO DE LUIS | 10 | ACTIVA'])
comprobar('listado sin reservas', filas('ListaCumple.php?fecha=2027-01-01'), [])
n.cerrar()
print('%d fallo(s)' % len(fallos))
sys.exit(1 if fallos else 0)
