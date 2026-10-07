// Pantalla de inicio de sesion: envia el login a Querys.php (guard=P) y rota la cartelera.
// Usa Base64 de scripts.js, la misma codificacion con la que se guardaron las claves.
(function(){
	var formulario = document.getElementById("loger");
	var aviso = document.getElementById("aviso");
	var boton = document.getElementById("botonr");
	var clave = document.getElementById("Pass");
	var verClave = document.getElementById("verClave");

	var MENSAJES = {
		E1: "No encontramos una cuenta con ese correo.",
		E2: "La contraseña no es correcta.",
		E3: "Tu cuenta aún no está activada. Revisa tu correo para activarla."
	};

	function mostrarAviso(texto){
		aviso.textContent = texto;
		aviso.hidden = false;
	}

	function ocupado(si){
		boton.disabled = si;
		boton.textContent = si ? "Ingresando…" : "Ingresar";
	}

	formulario.addEventListener("submit", function(evento){
		evento.preventDefault();
		aviso.hidden = true;
		var usuario = document.getElementById("User").value.trim().toLowerCase();
		if(usuario == "" || clave.value == ""){
			mostrarAviso("Escribe tu correo y tu contraseña.");
			return;
		}
		ocupado(true);
		var peticion = new XMLHttpRequest();
		peticion.open("POST", "Querys.php", true);
		peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticion.onreadystatechange = function(){
			if(peticion.readyState != 4){
				return;
			}
			var respuesta = peticion.responseText.toString().trim();
			if(respuesta == "UO"){
				location.href = "welcome.php";
				return;
			}
			ocupado(false);
			mostrarAviso(MENSAJES[respuesta] || "No pudimos iniciar sesión. Intenta de nuevo en un momento.");
		};
		peticion.send("guard=P&user=" + encodeURIComponent(usuario) + "&passC=" + encodeURIComponent(Base64.encode(clave.value)));
	});

	verClave.addEventListener("click", function(){
		var oculta = clave.type == "password";
		clave.type = oculta ? "text" : "password";
		verClave.textContent = oculta ? "Ocultar" : "Mostrar";
		verClave.setAttribute("aria-pressed", oculta ? "true" : "false");
	});

	// En escritorio el cursor queda en el correo. En celular no: el foco bajaria la pagina y taparia los afiches.
	if(window.matchMedia("(min-width: 861px)").matches){
		document.getElementById("User").focus();
	}

	// --- Cartelera: una imagen a la vez, cambia sola cada 5 segundos ---
	var cartelera = document.getElementById("cartelera");
	var imagenes = cartelera.getElementsByTagName("img");
	var nota = document.getElementById("carteleraNota");
	var puntos = document.getElementById("carteleraPuntos");
	var actual = 0;
	var reloj = null;

	function mostrar(indice){
		imagenes[actual].classList.remove("activa");
		puntos.children[actual].classList.remove("activo");
		actual = (indice + imagenes.length) % imagenes.length;
		imagenes[actual].classList.add("activa");
		puntos.children[actual].classList.add("activo");
		var texto = imagenes[actual].getAttribute("data-nota");
		nota.textContent = texto || "";
		nota.hidden = !texto;
	}

	function programar(){
		clearInterval(reloj);
		reloj = setInterval(function(){ mostrar(actual + 1); }, 5000);
	}

	function armarPuntos(){
		puntos.innerHTML = "";
		actual = 0;
		for(var i = 0; i < imagenes.length; i++){
			var punto = document.createElement("button");
			punto.type = "button";
			punto.setAttribute("aria-label", "Ver imagen " + (i + 1) + " de " + imagenes.length);
			punto.addEventListener("click", (function(indice){
				return function(){ mostrar(indice); programar(); };
			})(i));
			puntos.appendChild(punto);
		}
		mostrar(0);
		programar();
	}
	armarPuntos();

	// Si hay eventos creados en "Eventos" (de hoy en adelante), la cartelera muestra sus imagenes frontales
	// en lugar de las fijas de index.html.
	var pedido = new XMLHttpRequest();
	pedido.open("POST", "Querys.php", true);
	pedido.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	pedido.onreadystatechange = function(){
		if(pedido.readyState != 4 || pedido.status != 200){
			return;
		}
		var eventos;
		try{
			eventos = JSON.parse(pedido.responseText);
		}catch(error){
			return;
		}
		if(!eventos || eventos.length == 0){
			return;
		}
		while(imagenes.length > 0){
			cartelera.removeChild(imagenes[0]);
		}
		for(var i = 0; i < eventos.length; i++){
			var imagen = document.createElement("img");
			imagen.src = eventos[i].frente;
			imagen.alt = eventos[i].nombre + ": " + eventos[i].fechaTexto + " en Living";
			cartelera.insertBefore(imagen, nota);
		}
		armarPuntos();
	};
	pedido.send("guard=EVT");
})();
