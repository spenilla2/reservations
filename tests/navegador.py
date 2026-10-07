# Cliente minimo de WebDriver con la libreria estandar: maneja Firefox sin ventana a traves de geckodriver.
import json, subprocess, time, urllib.request, base64, atexit
class Nav:
    def __init__(self, puerto=4455, ancho=1500, alto=1300):
        self.base = 'http://127.0.0.1:%d' % puerto
        self.proc = subprocess.Popen(['/snap/bin/geckodriver', '--port', str(puerto)], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        atexit.register(self.cerrar)
        for _ in range(50):
            try: self._http('GET', '/status'); break
            except Exception: time.sleep(0.2)
        r = self._http('POST', '/session', {'capabilities': {'alwaysMatch': {'moz:firefoxOptions': {'args': ['-headless', '--width=%d' % ancho, '--height=%d' % alto]}, 'unhandledPromptBehavior': 'accept'}}})
        self.sid = r['value']['sessionId']
    def _http(self, metodo, ruta, datos=None):
        req = urllib.request.Request(self.base + ruta, method=metodo, data=None if datos is None else json.dumps(datos).encode(), headers={'Content-Type': 'application/json'})
        try:
            return json.loads(urllib.request.urlopen(req, timeout=60).read())
        except urllib.error.HTTPError as e:
            return json.loads(e.read())
    def ir(self, url): return self._http('POST', '/session/%s/url' % self.sid, {'url': url})
    def js(self, script, *args): return self._http('POST', '/session/%s/execute/sync' % self.sid, {'script': script, 'args': list(args)}).get('value')
    def url(self): return self._http('GET', '/session/%s/url' % self.sid)['value']
    def foto(self, ruta):
        open(ruta, 'wb').write(base64.b64decode(self._http('GET', '/session/%s/screenshot' % self.sid)['value']))
    def cerrar(self):
        try: self._http('DELETE', '/session/%s' % self.sid)
        except Exception: pass
        try: self.proc.terminate()
        except Exception: pass
    def entrar(self, usuario, clave='Prueba123', base='http://localhost:8080'):
        self.ir(base + '/index.html')
        r = self.js("""var x=new XMLHttpRequest(); x.open('POST','Querys.php',false);
            x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            x.send('guard=P&user='+encodeURIComponent(arguments[0])+'&passC='+encodeURIComponent(btoa(arguments[1]))); return x.responseText.trim();""", usuario, clave)
        self.ir(base + '/welcome.php')
        return r
