function objetoAjax(){
    var xmlHttp;
     try {
		// Firefox, Opera 8.0+, Safari
			xmlHttp=new XMLHttpRequest();
			return xmlHttp;
	} catch (e) {
		// Internet Explorer
	 try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			return xmlHttp;
	} catch (e) {
				try {
						xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
						return xmlHttp;
				} catch (e) {
						alert("Tu navegador no soporta AJAX!");
						return false;
				  }
	}
 }
}

function asignaMesaLast(reservaId, index){
	let mesaSelIndex = document.getElementById("mesasDisp"+index).selectedIndex;
	let mesaSelData = document.getElementById("mesasDisp"+index).options[mesaSelIndex].value;	
	let answ = confirm("Esta Segoro de asignar la Reserva ("+reservaId+") a la Mesa "+mesaSelData);
	if(answ){
		datos = "Querys.php";
		ajax=objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		var CadenaEnv="guard=ARMN&mesa="+mesaSelData+"&reserva="+reservaId;
		ajax.send(CadenaEnv);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				var response = ajax.responseText.toString();
				alert(response);
				location.reload();
				
			}
	   }
	}else{

	}

	
}
function direcciona(pagina){
	document.location = pagina;
}
function ListaReservas(tipo){
	Fecha= document.getElementById("fecha").value;
	if(Fecha==""){
		alert("Debes indicar una fecha para la Reserva");
	}else{
		if(Fecha.substr(2,1)!='/'){
			Fecha=Fecha;
		}else{
	    	Fecha=Fecha.substr(6,4)+'-'+Fecha.substr(3,2)+'-'+Fecha.substr(0,2);
		}
		datos = "Querys.php";
		ajax=objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		var CadenaEnv="guard=BRF&fecha="+Fecha+"&tipo="+tipo;
		ajax.send(CadenaEnv);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			var ll = ajax.responseText.toString();
			document.getElementById("Respuesta").innerHTML=ll;
			
		}
		}
	}
}
function GoModificar(){
	res = document.getElementById("Reserva").value;
	fecha = document.getElementById("fecha").value;
	Mesa = document.getElementById("Mesa").value;
	if(fecha.substr(2,1)!='/'){
		fecha=CambiaFormatoFecha(fecha);
	}else{
	    fecha=fecha;
	}
	// window.top: si la mesa esta abierta en la ventana modal, el formulario ocupa la pagina completa
	window.top.location.href="reserva.php?fecha="+fecha+'&Tipo=U&Rnum='+res+'&MA='+Mesa;

}
function EntrMani(){
	cum = document.getElementById("cumpli").selectedIndex;
	cumpli = document.getElementById("cumpli").options[cum].value;
	Reserva = document.getElementById("Reserva").value;
	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var CadenaEnv="guard=ConfEntrM&cumpli="+cumpli+"&RNum="+Reserva;
	ajax.send(CadenaEnv);
	ajax.onreadystatechange=function() {
	    if (ajax.readyState==4) {
			var ll = ajax.responseText.toString();
			document.getElementById("resform").innerHTML=ll;
		}else{
			document.getElementById("resform").innerHTML='<img src="images/loading.gif">';		
		}
	}
}
function CambiaFormatoFecha(date){
	var fecha="";
	fecha = date.substr(8,2)+"/"+date.substr(5,2)+'/'+date.substr(0,4);
	return fecha;
}
function recuppass(){
		var form = document.getElementById("registro");
		form.submit();
}
function hdata(){
	var hdata= document.getElementById("hdata1").checked;
	if(hdata){
		document.getElementById("btnreg").disabled=false;
	}else{
		document.getElementById("btnreg").disabled=true;
	}
}

function ResevaForm(){
	let tipo = document.getElementById("tipo").value;
	let fecha;
	if(tipo=="I"){
		fecha = document.getElementById("dc").value;
		if(fecha.substr(2,1)!='/'){
			fecha = CambiaFormatoFecha(fecha);
		}else{
	      fecha = fecha;
		}
		document.getElementById("fechareserva").value=fecha;
		if(fecha.length<10){
			fecha = "";
		}else{
			fecha = fecha;
		}
	}else{
		fecha = document.getElementById("fechareserva").value;
	}
	let idLiv = document.getElementById("idLiving").value;
	let Perfil = document.getElementById("Perfil").value;
	let Reserva = document.getElementById("numReserva").value;
	if(fecha==""){
		document.getElementById("resform").innerHTML='<div class="container"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center;"><h4>Fecha Seleccionada es Incorrecta</h4><img class="resdia" src="images/duck.jpg"></div></div></div>';
	}else{
		datos = "Querys.php";
		ajax = objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		let cadenaEnv = "guard=ResForm&Fecha="+fecha+
		                       "&IdLiving="+idLiv+
							   "&Perfil="+Perfil+
							   "&tipo="+tipo+
							   "&RNum="+Reserva;
		ajax.send(cadenaEnv);
		ajax.onreadystatechange=function() {
			    if (ajax.readyState==4) {
					let response = ajax.responseText.toString();
					document.getElementById("resform").innerHTML = response;
				}else{
					document.getElementById("resform").innerHTML = '<img src="images/loading.gif">';		
				}
		}
	}
}
function PasaNombre(){

	id=document.getElementById("rtipocelabra").selectedIndex;
	valorid=document.getElementById("rtipocelabra").options[id].value;
	NReserva=document.getElementById("rname").value;
	if(valorid==1){
			document.getElementById("rhomena").value=NReserva;
			document.getElementById("rhomena").disabled=false;
	}else{
			document.getElementById("rhomena").value="";
			document.getElementById("rhomena").disabled=true;
	}
}

function BuscarReserva(){
	Reserva= document.getElementById("Rese").value;
	if(Reserva==""){
		alert("Debes indicar el No. de Reserva");
	}else{
		datos = "Querys.php";
		ajax=objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		var CadenaEnv="guard=BR&Reserva="+Reserva;
		ajax.send(CadenaEnv);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			var ll = ajax.responseText.toString();
			document.getElementById("Respuesta").innerHTML=ll;
			
		}
		}	
	}
}
function TrasladarGrupo(){
	reserva=document.getElementById("Reserva").value;
	MesaO=document.getElementById("Mesa").value;
	MesaD=document.getElementById("traslada").options[document.getElementById("traslada").selectedIndex].value; 
	Fecha=document.getElementById("fecha2").value;
	MRConse=document.getElementById("MRConse").value;
	if(MesaD==0){
		alert("Debes de Seleccionar una Mesa de Destino!");
	}else{
		datos = "Querys.php";
		ajax=objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		var CadenaEnv="guard=TMS&Reserva="+reserva+"&MesaO="+MesaO+"&MesaD="+MesaD+"&Fecha="+Fecha+"&MRConse="+MRConse;
		ajax.send(CadenaEnv);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			var ll = ajax.responseText.toString();
			if(ll==1){
				document.getElementById("respuesta").innerHTML='<p class="pbien">GRUPO TRASLADADO EXITOSAMENTE!';
			}else{
				console.log(ll);
				document.getElementById("respuesta").innerHTML='<p class="perror">HA OCURRIDO UN ERROR';
			}
		}else{
			document.getElementById("respuesta").innerHTML='<img src="images/loading.gif"';
		}
		}
	}
	
}
function ConfirmaRes(){
	var reserva=document.getElementById("reserva").value;
}
function LlamaMapa(){
	fecha=document.getElementById("dc").value;
	if(fecha.substr(2,1)!='/'){
		fecha2=CambiaFormatoFecha(fecha);
	}else{
      fecha2=fecha;
	}
	location.href="dashboard.php?fecha="+fecha2;
}
function ActMCompartida(){
	Reserva=document.getElementById("Reserva").value;
	CantPersonas=document.getElementById("ActCantPer").options[document.getElementById("ActCantPer").selectedIndex].value;
	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var CadenaEnv="guard=AMCP&Reserva="+Reserva+"&CantPer="+CantPersonas;
	ajax.send(CadenaEnv);
	ajax.onreadystatechange=function() {
	if (ajax.readyState==4) {
		var ll = ajax.responseText.toString();
		if(ll==1){
			alert("Cantidad de Personas Actualizadas Satisfactoriamente");
			location.reload(true);
		}else{
		}
	}
	}
}
function LiberaMesa(){
	Mesa = document.getElementById("Mesa").value;
	Fecha = document.getElementById("fecha2").value;
	Reserva = document.getElementById("Reserva").value;
	Mesas = document.getElementById("mesas").value;
	CadNueva1 = Mesas.replace(Mesa+",","");
	CadNueva = CadNueva1.replace(Mesa,"");
	p = confirm("Esta Seguro que desea Liberar la mesa?");
	if(p){
		tup="0";
		h=confirm("El Grupo Abandona el Establecimiento?");
		if(h){
			tup="1";
		}else{
			tup="0";
		}
		datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=LMM&Mesa="+Mesa+"&Fecha="+Fecha+"&Reserva="+Reserva+"&MesaN="+CadNueva+"&centi="+tup;
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
				  	if(ll==1){
				  		document.getElementById("respuesta").innerHTML='<p class="pbien">Mesa Liberada Satisfactoriamente<p/><input type="button" value="Volver" onClick="GoBack()">';
				  	}else{
				  		document.getElementById("respuesta").innerHTML='<p class="perror">Ha Ocurrido un Error<p/>--'+ll+'---<input type="button" value="Volver" onClick="GoBack()">';
				  	}
				}else{
					document.getElementById("respuesta").innerHTML='<img src="images/loading.gif">';
				}
				}

	}else{

	}
	
}
function LevantaReserva(){
	var nres=document.getElementById("totres").value;
	var fechaC= document.getElementById("fecha1").value;
	Cadena="";
	for(var i=1;i<=nres;i++){
	  check=document.getElementById("C"+(i)).checked;
	  if(check){
	  	valor=document.getElementById("C"+(i)).value;
	  	Cadena+=valor+",";
	  }else{

	  }
	}
	Cadena+="0";
	datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=LResScript&Fecha="+fechaC+"&ResMan="+Cadena;
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
					document.getElementById("resultado").innerHTML=ll;			  	
				  	}
				  }
}
function CargaReservasPendientes(){
	var fecha = document.getElementById("dc").value;
	if(fecha.substr(2,1)!='/'){
			fecha=CambiaFormatoFecha(fecha);
		}else{
	      fecha=fecha;
		}
	if(fecha==""){
		document.getElementById("resultado").innerHTML='<ul><p style="color:#ff0000;font-weight:bold;left:10px">Fecha seleccionada es incorrecta!</p></ul>';
	}
	else{

		datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				Cadena = "Fecha="+fecha;
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=LRes&"+Cadena;
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
					document.getElementById("resultado").innerHTML=ll;			  	
				  	}
				  }

	} 
}
function AlmacenaRMComparte(){
  var NForaneo="Mesa Compartida";
  var CantPer=document.getElementById("CFSe").value;
  if(CantPer==0){
  	alert("Debes de especificar el Numero de Personas");
  }else{
  	  var TipoCelebra="5";
	  var NombreHomena="";
	  var Observa="Mesa Compartida";
	  var Mesa = document.getElementById("Mesa").value;
	  var FechaReserva = document.getElementById("fecha").value;
	  if(FechaReserva.substr(2,1)!='/'){
			FechaReserva=CambiaFormatoFecha(FechaReserva);
		}else{
	      FechaReserva=FechaReserva;
		}
		
	  var IdLiving = document.getElementById("idLiv").value;
	  var IdCliVIP = document.getElementById("IdClvp").value;
	  var TipoAsigna = document.getElementById("TipoAsigna").value;
	  datos = "Querys.php";
	  ajax=objetoAjax();
	  ajax.open('POST', datos,true);
	  Cadena = "Mesa="+Mesa+"&Nombre="+
					   NForaneo+"&NumPer="+
					   CantPer+"&TipoCelebra="+
					   TipoCelebra+"&NombreH="+
					   NombreHomena+"&Observa="+
					   Observa+"&FechaRes="+
					   FechaReserva+"&IdLiving="+
					   IdLiving+"&IdCliVIP="+
					   IdCliVIP+"&TipoAsigna="+
					   TipoAsigna;
	  ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	  var CadenaEnv="guard=IRCVIP&"+Cadena;
	  ajax.send(CadenaEnv);
	  ajax.onreadystatechange=function() {
		  if (ajax.readyState==4) {
		    var ll = ajax.responseText.toString();
		    document.getElementById("respuesta").innerHTML=ll;
		  }else{
		  	document.getElementById("respuesta").innerHTML='<img src="images/loading.gif">';
		  }
	  }
  }
}

function AlmacenaRCBloq(){
  var NForaneo="Mesa Bloqueada";
  var CantPer="99";
  var TipoCelebra="5";
  var NombreHomena="";
  var Observa=document.getElementById("AMObse").value;
  var Mesa = document.getElementById("Mesa").value;
  var FechaReserva = document.getElementById("fecha").value;
  var IdLiving = document.getElementById("idLiv").value;
  var IdCliVIP = document.getElementById("IdClvp").value;
  var TipoAsigna = document.getElementById("TipoAsigna").value;
  if(FechaReserva.substr(2,1)!='/'){
			FechaReserva=CambiaFormatoFecha(FechaReserva);
		}else{
	      FechaReserva=FechaReserva;
		}
  datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				Cadena = "Mesa="+Mesa+"&Nombre="+
								 NForaneo+"&NumPer="+
								 CantPer+"&TipoCelebra="+
								 TipoCelebra+"&NombreH="+
								 NombreHomena+"&Observa="+
								 Observa+"&FechaRes="+
								 FechaReserva+"&IdLiving="+
								 IdLiving+"&IdCliVIP="+
								 IdCliVIP+"&TipoAsigna="+
								 TipoAsigna;
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=IRCVIP&"+Cadena;
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
				  	document.getElementById("respuesta").innerHTML=ll;
				  	}else{
				  		document.getElementById("respuesta").innerHTML='<img src="images/loading.gif">';
				  	}
				  }
}
function AlmacenaRCFora(){
  var NForaneo=document.getElementById("CFIn1").value;
  var CantPer=document.getElementById("CantPer").value;
  var TipoCelebra=document.getElementById("AMTCeb").value;
  var NombreHomena=document.getElementById("AMTHome").value;
  var Observa=document.getElementById("AMObse").value;
  var Mesa = document.getElementById("Mesa").value;
  var FechaReserva = document.getElementById("fecha").value;
  var IdLiving = document.getElementById("idLiv").value;
  var IdCliVIP = document.getElementById("IdClvp").value;
  var TipoAsigna = document.getElementById("TipoAsigna").value;
  if(FechaReserva.substr(2,1)!='/'){
			FechaReserva=CambiaFormatoFecha(FechaReserva);
		}else{
	      FechaReserva=FechaReserva;
		}
  if(NForaneo==""){
  	alert("Debes de escoger un Grupo");
  }else{
  	if(CantPer==0){
  		alert("Debes de Escoger el Tamano del Grupo");
  	}else{
  			    datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				Cadena = "Mesa="+Mesa+"&Nombre="+
								 NForaneo+"&NumPer="+
								 CantPer+"&TipoCelebra="+
								 TipoCelebra+"&NombreH="+
								 NombreHomena+"&Observa="+
								 Observa+"&FechaRes="+
								 FechaReserva+"&IdLiving="+
								 IdLiving+"&IdCliVIP="+
								 IdCliVIP+"&TipoAsigna="+
								 TipoAsigna;
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=IRCVIP&"+Cadena;
				//alert(CadenaEnv);
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
				  	document.getElementById("respuesta").innerHTML=ll;
				  	}else{
				  		document.getElementById("respuesta").innerHTML='<img src="images/loading.gif">';
				  	}
				  }
  		
  	}
  }
	
}
function GestionForaneo(){
	var CFS=document.getElementById("CFSe").options[document.getElementById("CFSe").selectedIndex];
	var CFSValor=CFS.value;
	var CFSTexto = CFS.text;
	if(CFSValor==0){
		alert("Debes de Seleccionar una Descripcion del Cliente");
		document.getElementById("CFIn1").value="";
		document.getElementById("CFIn1").disabled=true;	
	}else{
		if(CFSValor==1){
			document.getElementById("CFIn1").disabled=false;
			document.getElementById("CFIn1").value="";
			document.getElementById("CantPer").disabled=false;
		}else{
			if(CFSValor==21){
				document.getElementById("CFIn1").disabled=false;
				document.getElementById("CFIn1").value=CFSTexto;
				document.getElementById("CantPer").disabled=false;
				document.getElementById("CantPer").value=CFSValor;
				document.getElementById("cper").innerHTML="<h4>"+CFSValor+"</h4>";	
			}else{
				document.getElementById("CFIn1").disabled=true;
				document.getElementById("CFIn1").value=CFSTexto;
				document.getElementById("CantPer").disabled=true;
				document.getElementById("CantPer").value=CFSValor;
				document.getElementById("cper").innerHTML="<h4>"+CFSValor+"</h4>";
			}
			
		}
	}
	
}
function AlmacenaRCVIP(){
	Mesa = document.getElementById("Mesa").value;
	NombreCli = document.getElementById("AMCliN").value;
	CantPers = document.getElementById("CantPer").options[document.getElementById("CantPer").selectedIndex].value;
	TipoCelebra = document.getElementById("AMTCeb").value;
	NombreHomena = document.getElementById("AMTHome").value;
	Observa = document.getElementById("AMObse").value;
	FechaReserva = document.getElementById("fecha").value;
	IdLiving = document.getElementById("idLiv").value;
	IdCliVIP = document.getElementById("IdClvp").value;
	TipoAsigna = document.getElementById("TipoAsigna").value;
	if(FechaReserva.substr(2,1)!='/'){
			FechaReserva=CambiaFormatoFecha(FechaReserva);
		}else{
	      FechaReserva=FechaReserva;
		}
	if(NombreCli.trim()==""){
		alert("El Campo Nombre no puede estar Vacio")
	}else{
		if(CantPers==0){
			alert("Se debe de especificar la Cantidad de Personas");
		}else{
				datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				Cadena = "Mesa="+Mesa+"&Nombre="+
								 NombreCli+"&NumPer="+
								 CantPers+"&TipoCelebra="+
								 TipoCelebra+"&NombreH="+
								 NombreHomena+"&Observa="+
								 Observa+"&FechaRes="+
								 FechaReserva+"&IdLiving="+
								 IdLiving+"&IdCliVIP="+
								 IdCliVIP+"&TipoAsigna="+
								 TipoAsigna;
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=IRCVIP&"+Cadena;
				
				ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
				  	document.getElementById("respuesta").innerHTML=ll;
				  	}else{
				  	document.getElementById("respuesta").innerHTML='<img src="images/loading.gif">';
				  	}

				  }
			
		}
	}
	
}
function traeNombre(){
	m=document.getElementById("clivips").selectedIndex;
	document.getElementById("AMCliN").value=document.getElementById("clivips").options[m].text;
	document.getElementById("IdClvp").value=document.getElementById("clivips").options[m].value;
}
function ValidaCelebra(){
   var n=document.getElementById("AMTCeb").selectedIndex;
   var va=document.getElementById("AMTCeb").options[n].value;
   if(va==1){
   	 document.getElementById("AMTHome").disabled=false;
   }else{
   	document.getElementById("AMTHome").disabled=true;
   }
   
}
function AsignacionMesa(Tipo){
		var TipoA="";
		switch(Tipo){
			case 1:
				TipoA="V";
			break;
			case 2:
				TipoA="F";
			break;	
			case 3:
				TipoA="B";
			break;
			case 4:
				TipoA="K";
			break;		
		}
		document.getElementById("TipoAsigna").value=TipoA;
		var i;
   		for (i=0;i<document.tipoasigna.tcliente.length;i++){ 
      		if (document.tipoasigna.tcliente[i].checked){
      			Codigo=document.tipoasigna.tcliente[i].value;
      			fecha=document.getElementById("fecha").value;
      			if(fecha.substr(2,1)!='/'){
					fecha=CambiaFormatoFecha(fecha);
				}else{
	      			fecha=fecha;
				}
				datos = "Querys.php";
				ajax=objetoAjax();
				ajax.open('POST', datos,true);
				ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				var CadenaEnv="guard=CCliVip&Cod="+Codigo+"&fecha="+fecha;
			 	ajax.send(CadenaEnv);
				ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
				  	var ll = ajax.responseText.toString();
				  	document.getElementById("divCliVip").innerHTML=ll;
				   	}
				  }

      			
      			break;	
      		} 
         	 
   		} 
   	 
//} 

	}
function BClienteVip(){
	CBusq=document.getElementById("NCliente").value;
	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var CadenaEnv="guard=BCVIP&Nom="+CBusq;
 	ajax.send(CadenaEnv);
	ajax.onreadystatechange=function() {
	if (ajax.readyState==4) {
	  	var ll = ajax.responseText.toString();
	  	document.getElementById("divres").innerHTML=ll;
	   	}
	  }
}

function ActualizaVip(){
	idClienteVip=document.getElementById("IdClienteVip").value;
	idLiving=document.getElementById("IdLiving").value;
	Nombre=document.getElementById("name").value;
	Telefono=document.getElementById("telefono").value;
	Gen=document.getElementsByName("genero");
	Genero="O";
	for(i=0;i<Gen.length;i++){
		if(Gen[i].checked){
				Genero=Gen[i].value;
		}else{

		}
		
	}
	
	TRumba=document.getElementById("trumba").value;
	VObserva=document.getElementById("VObserva").value;
	NObserva=document.getElementById("Observa").value;
	variable=false;
	if(Nombre.trim()==""||(Nombre.trim().length<4)){
		alert("Nombre Invalido");
	}else{
		if(Telefono.trim()==""||Telefono.trim().length<4){
		alert("Numero Telefonico Invalido");
		}else{
			variable=true;
		}
 	}
	if(variable){
		datos = "Querys.php";
		ajax=objetoAjax();
		ajax.open('POST', datos,true);
		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		var CadenaEnv="guard=ACV&IdClienteVip="+idClienteVip
		                      +"&IdLiving="+idLiving
		                      +"&Nombre="+Nombre
		                      +"&Telefono="+Telefono
		                      +"&Genero="+Genero
		                      +"&TRumba="+TRumba
		                      +"&Telefono="+Telefono
		                      +"&VObserva="+VObserva
		                      +"&NObserva="+NObserva;
 		ajax.send(CadenaEnv);
		ajax.onreadystatechange=function() {
	  	if (ajax.readyState==4) {
	   		var ll = ajax.responseText.toString();
	   		if(ll==1){
	   		alert("Informacion Actualizada Satisfactoriamente!");
	   		window.location.assign("GestionCVP.php?idCliVip="+idClienteVip);
	   		}else{
	   			alert("Ha Ocurrido un Error: "+ll);
	   		}
	   		
		}
	 }	
	}else{

	}
	
}
function ClienteVIP(){

	indice = document.getElementById("clientesVIP").selectedIndex;
	if(indice>0){
	  document.getElementById("rname").disabled=true;
	  idcliente = document.getElementById("clientesVIP").value;
	  NombreCliente = document.getElementById("clientesVIP").options[indice].text; 
	  document.getElementById("rname").value=NombreCliente;
	  document.getElementById("idClienteVip").value=idcliente;
	}else{
		document.getElementById("rname").disabled=false;
		document.getElementById("rname").value=document.getElementById("nombreUsuario").value;
		document.getElementById("idClienteVip").value=0;
	}
	
}
function validaIgual(CMesas){
	var P=[];
	for(i=0;i<CMesas;i++){
		P[i]=document.getElementById("M"+(i+1)).value;
		P=P.sort();
	}
	P[CMesas]=99;
	var centi=true;
	for(i=0;i<CMesas;i++){
	  if(P[i]==P[(i+1)]&&P[i]!=0)
	   {
	   	centi=false;
	   	alert("La Mesa "+P[i]+" ya se encuentra asignada!");
		document.getElementById("M"+(i+1)).options[0].selected=true;
	   }else{

	   }
	}
	return centi;
}
function AsignaMesa1(CMesas){
 var CadenaR="";
 centi=false;
 for(var i=0;i<CMesas;i++){
 	P=document.getElementById("M"+(i+1)).value;
 	if(P==0){
 		centi=true;
 		alert("La Mesa "+(i+1)+" no se le le ha asignado un numero!");
 		i=CMesas;
 	}else{

 	}
 }
 if(!centi){
 MesasIguales = validaIgual(CMesas);
 if(MesasIguales){
 	var V=[];
 	for(var i=0;i<CMesas;i++){
 		P=document.getElementById("M"+(i+1)).value;
 		V[i]=P;
 	}
 	var res=document.getElementById("Reserva").value;
 	var FechaFN=document.getElementById("fecha").value;
 	if(FechaFN.substr(2,1)!='/'){
		FechaFN=CambiaFormatoFecha(FechaFN);
	}else{
      FechaFN=FechaFN;
	}
	var rE=document.getElementById("respecial").checked;
	var estado=document.getElementById("estado").value;
 	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var CadenaEnv="guard=AMR1&Fecha="+FechaFN+"&Reserva="+res+"&CMesas="+CMesas+"&AMesas="+V+"&TRes="+rE+"&Estado="+estado;
 	ajax.send(CadenaEnv);
	ajax.onreadystatechange=function() {
	  if (ajax.readyState==4) {
	   		var ll = ajax.responseText.toString();
	   		document.getElementById("MesaAs").innerHTML=ll;
	   		/*window.open("correoAsignaMesa.php?reserva="+TRes[0],"width=10,height=10");
	   		location.href="AsignaMesa.php";
			*/
		}
	 }	
 }else{

 }
 
}else{

}
}

function AsignaMesa(CMesas){
 var CadenaR="";
 centi=false;
 for(var i=0;i<CMesas;i++){
 	P=document.getElementById("M"+(i+1)).value;
 	if(P==0){
 		centi=true;
 		alert("La Mesa "+(i+1)+" no se le le ha asignado un numero!");
 		i=CMesas;
 	}else{

 	}
 }
 if(!centi){
 MesasIguales=validaIgual(CMesas);
 if(MesasIguales){
 	var V=[];
 	for(var i=0;i<CMesas;i++){
 		P=document.getElementById("M"+(i+1)).value;
 		V[i]=P;
 	}
 	var TV=document.getElementById("cliente").value;
 	var TRes=TV.split(";");
 	var FechaFN=document.getElementById("dc").value;
 	if(FechaFN.substr(2,1)!='/'){
		FechaFN=CambiaFormatoFecha(FechaFN);
	}else{
      FechaFN=FechaFN;
	}
	
 	var rE=document.getElementById("respecial").checked;
 	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var CadenaEnv="guard=AMR&Fecha="+FechaFN+"&Reserva="+TRes[0]+"&CMesas="+CMesas+"&REspecial="+rE+"&AMesas="+V;
 	ajax.send(CadenaEnv);
	ajax.onreadystatechange=function() {
	  if (ajax.readyState==4) {
	   		var ll = ajax.responseText.toString();
	   		document.getElementById("resultado").innerHTML=ll;
	   		window.open("correoAsignaMesa.php?reserva="+TRes[0],"width=10,height=10");
	   		location.href="AsignaMesa.php";

		}
	 }	
 }else{

 }
 
}else{

}

 
}
function AsignaMesas(){
	var cadena=document.getElementById("cliente").value;
	var fechaR=document.getElementById("dc").value;
	if(fechaR.substr(2,1)!='/'){
		fechaR=CambiaFormatoFecha(fechaR);
	}else{
      fechaR=fechaR;
	}
	t = cadena.split(";");
	Reserva = t[0];
	Personas = t[1];
	divisor=(Personas<10)?6:10;
	var cantMesas = (Math.floor(Personas/divisor)<1)?1:Math.floor(Personas/divisor);
	datos = "Querys.php";
	ajax=objetoAjax();
	ajax.open('POST', datos,true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	var Cadena=('guard=CAM&cantMesas='+cantMesas+'&fecha='+fechaR);
	ajax.send(Cadena);
	ajax.onreadystatechange=function() {
	  if (ajax.readyState==4) {
	   		var ll = ajax.responseText.toString();
	   		document.getElementById("gestion1").innerHTML=ll;
	   		document.getElementById("NumMesas").value = cantMesas;
	   	}
	  }

}
function CargaReservas(){
	var Fecha1= new Date();
	Fecha1.setHours(0,0,0,0);
	var fecha = document.getElementById("dc").value;
	if(fecha.substr(2,1)!='/'){
		fecha=CambiaFormatoFecha(fecha);
	}else{
      fecha=fecha;
	}
	if(fecha.trim()==""){
		document.getElementById("resultado").innerHTML='<p style="color:#FF0000;font-weight:bold;">Debes indicar una Fecha!!</p>';
	}else{
		var Fecha2= new Date(fecha.substr(6,4),fecha.substr(3,2)-1,fecha.substr(0,2));
		Fecha2.setHours(0,0,0,0);
		if(Fecha2<Fecha1){
			document.getElementById("resultado").innerHTML='<p style="color:#FF0000;font-weight:bold;">Fecha seleccionada Inferior al D&iacute;a de hoy!!</p>';
		}else{
			datos = "Querys.php";
		  	ajax=objetoAjax();
		  	ajax.open('POST', datos,true);
		  	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		  	var Cadena=('guard=CRF&Fecha='+fecha);
		  	ajax.send(Cadena);
		  	ajax.onreadystatechange=function() {
		   		if (ajax.readyState==4) {
		   			var ll = ajax.responseText.toString();
		   			document.getElementById("resultado").innerHTML=ll;
		   		}
		    }
		}
	
		
		
		
	}
	
}
function ConfirmaReserva(input){
		datos = "Querys.php";
	  	ajax=objetoAjax();
	  	ajax.open('POST', datos,true);
	  	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	  	var Cadena=('guard=CRE&NumReserva='+input);
	  	ajax.send(Cadena);
	  	ajax.onreadystatechange=function() {
	   		if (ajax.readyState==4) {
	   			var ll = ajax.responseText.toString();
	   			document.getElementById("respuesta").innerHTML=ll;
	   		}
	    }
}
function ValidaReserva(){
	var Reserva=document.getElementById("numreserva").value;
	if(Reserva==""){
		document.getElementById("respuesta").innerHTML='<p style="color:#FF0000;font-weight:bold;">El Campo N&uacute;mero de la Reserva no puede estar Vacio.</p>';
	}else{
		datos = "Querys.php";
	  	ajax=objetoAjax();
	  	ajax.open('POST', datos,true);
	  	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	  	var Cadena=('guard=CR&NumReserva='+Reserva);
	  	ajax.send(Cadena);
	  	ajax.onreadystatechange=function() {
	   		if (ajax.readyState==4) {
	   			var ll = ajax.responseText.toString();
	   			document.getElementById("respuesta").innerHTML=ll;
	   		}
	    }
	}
}
// Mensaje que queda en pantalla cuando una reserva se crea o se actualiza.
function mensajeReservaLista(accion, numero){
	return '<div class="alert alert-success" role="status"><strong>Reserva No. '+parseInt(numero, 10)+' '+accion.toLowerCase()+'.</strong> '
		+ 'Puedes verla, modificarla o cancelarla en <a href="consultaReservas.php">Consultar Reservas</a>, '
		+ 'o <a href="reserva2.php?Tipo=I&MA=0">hacer otra reserva</a>.</div>';
}
function Reservar(){
	var form = document.getElementById("reserva");
	var elements=form.elements.length;
	var K=0;
	var Perfil = document.getElementById("Perfil").value;
	for (var i = 0; i<elements; i++) {
		if(form.elements[i].value=='' && form.elements[i].hasAttribute('required')){
			K++;
		}else{
			K=K;
		}
    }
    if(K>0){
    	alert("Debes especificar un Nombre para la Reserva");
    }else{
    	var tipo=document.getElementById("tipoReservaK").value;
    	var Type="";
    	var NNumperson=0;
    	if(tipo=="I"){
    		Type="RR";
    		NNumperson = document.getElementById("cper").options[document.getElementById("cper").selectedIndex].value;
		
    	}else{
    		Type="RU";
    		NNumperson = document.getElementById("rnumperson").value;	
    	}
    	var Cadadicional = "";
    	if(Perfil==1){
    		var indiceVip = document.getElementById("clientesVIP").selectedIndex;
    		var idClienteVipA = document.getElementById("clientesVIP").options[indiceVip].value;
    		Cadadicional=idClienteVipA
    	}else{
    		Cadadicional="0";
    	}
    	var idLiv = document.getElementById("idLiving").value;
		var NReserva = document.getElementById("rname").value;
		var TReserva = document.getElementById("rtipocelabra").value;
		var FechaReserva = document.getElementById("fechareserva").value;
		var RHomenajeado = document.getElementById("rhomena").value;
		var RObserva = document.getElementById("robserva").value;
		var RNumReserva = document.getElementById("numReserva").value; 
		var RecibeCorreo = document.getElementById("news").checked;
		datos = "Querys.php";
	  	ajax=objetoAjax();
	  	ajax.open('POST', datos,true);
	  	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	  	var Cadena=('guard='+Type
	  			  +'&id='+idLiv
	  		      +'&NombreRese='+encodeURIComponent(NReserva)
	  		      +'&Numper='+NNumperson
	  		      +'&TipoR='+TReserva
	  		      +'&Fecha='+encodeURIComponent(FechaReserva)
	  		      +'&NHomena='+encodeURIComponent(RHomenajeado)
	  		      +'&Observa='+encodeURIComponent(RObserva)
	  		      +'&NumReserva='+RNumReserva
	  		      +'&ClienteVIP='+Cadadicional);
	  	ajax.send(Cadena);
	  	ajax.onreadystatechange=function() {
	   	if (ajax.readyState==4) {
		  var ll = ajax.responseText.toString();
		  Cad=ll.substring(0,3);
		  if(Cad=="EI1"){
		  	// el formulario ya fue reemplazado por el aviso de "procesando": se deja un mensaje con salida
		  	document.getElementById("resform").innerHTML='<div class="alert alert-warning" role="alert"><strong>Ya tienes una reserva igual para ese d&iacute;a.</strong> '
		  		+ 'Rev&iacute;sala en <a href="consultaReservas.php">Consultar Reservas</a> o <a href="reserva2.php?Tipo=I&MA=0">elige otra fecha</a>.</div>';
		  }else{
		  	if(Cad=="IC1"||Cad=="UC1"){
		  		var cadt="";
		  		if(Cad=="IC1"){
		  			cadt="Realizada";
		  		}else{
		  			cadt="Actualizada";
		  		}
		  		t=ll.split(";");
		  		if(RecibeCorreo){
		  			document.getElementById("resform").innerHTML=mensajeReservaLista(cadt, t[1]);
		  			location.href="correoReserva.php?reserva="+t[1];
		  			//location.href="reserva2.php?Tipo=I";
		  		}else{
		  			document.getElementById("resform").innerHTML=mensajeReservaLista(cadt, t[1]);
		  			//location.href="reserva2.php?Tipo=I";
		  		}
		  		
		  	}else{
		  		document.getElementById("resform").innerHTML='<p class="perror">Ha Ocurrido un Error Fatal '+ll+'<p/><br><br><br><br><br><br><br><br><br><br><br><br>';
		  	}
		  }
		 }else{
		 	document.getElementById("resform").innerHTML='<img src="images/loading.gif"><br>Un momento por favor estamos Procesando su solicitud<br>';
		 }
		}
	 }
}
function ValidaPass(){
	var form = document.getElementById("loger");
	var elements=form.elements.length;
	var K=0;
	for (var i = 0; i<elements; i++) {
		if(form.elements[i].value=='' && form.elements[i].hasAttribute('required')){
			K++;
		}else{
			K=K;
		}
    }
	if(K==0){
		user=document.getElementById("User").value;
		pass=document.getElementById("Pass").value;
		passCifrado=Base64.encode(pass);	
		datos = "Querys.php";
  		ajax=objetoAjax();
  		ajax.open('POST', datos,true);
  		ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  		ajax.send('guard=P&user='+user+"&passC="+passCifrado);
  		ajax.onreadystatechange=function() {
		   if (ajax.readyState==4) {
		   	 var ll = ajax.responseText.toString();
				switch(ll.trim()){
		   	    	case "E1":
	  					alert("Usuario no existe en la Base de Datos");
	  				break;
	  				case "E2":
	  					alert("Contraseña Incorrecta");
	  				break;
	  				case "E3":
	  					alert("La Cuenta aun no ha sido activada! Por favor revisa tu Correo para activarla");
	  				break;
	  				case "UO":
						location.href="welcome.php?login="+user;
	  				break;
	  				default:
	  					alert("Ha Ocurrido un error ("+ll+")");
	  				break;

	  			}
	  	    }	
		}
	}
	
}
function GoBack(){
	// dentro de la ventana modal del tablero, "Volver" la cierra
	if(window.parent!==window && typeof window.parent.cerrarModalMesa=="function"){
		window.parent.cerrarModalMesa();
		return;
	}
	history.back(1);
}
function GoPrin(){
	window.location.assign("index.html");
}
function checkMailExist(input, tipo){
  correo=input.value;
  datos = "Querys.php";
  ajax=objetoAjax();
  ajax.open('POST', datos,true);
  ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  ajax.send('guard=M&correo='+correo);
  var v=1;
  ajax.onreadystatechange=function() {
   if (ajax.readyState==4) {
	  var ll = ajax.responseText.toString();

	  if(tipo==1){
	  	if(parseInt(ll)!=0){
	  	input.setCustomValidity("El Correo Electronico ya se encuentra Registrado en nuestra Base de Datos");
	  	document.getElementById("mail2").value="";
	  	v=1;	
	  	}else{
	  		input.setCustomValidity("");
	  		document.getElementById("mail2").value="";
	  		v=0;
	  	}	
	  }else{
	  	if(parseInt(ll)!=0){
	  		input.setCustomValidity("");
	  		v=0;
	  	}else{
	  		//alert(ll+""+input.value);
	  		input.setCustomValidity("El Correo Electronico no se encuentra Registrado en nuestra Base de Datos");
	  		v=1;	
	  	}	
	  }
	  
    }
   }   
return v;
}
function checkMail(input,id){
	if(id==1){
		if(input.value!=document.getElementById("mail").value){
			input.setCustomValidity("Los Correos Electronicos ingresados no coinciden");
			return 1;
		}else{
			/*if(document.getElementById("VMail").value=="1"){
				document.getElementById("mail").setCustomValidity("Correo Ya registrado en la Base de Datos");
				document.getElementById("mail2").value="";	
				alert("Correo Ya registrado en la Base de Datos");
			}*/
			input.setCustomValidity("");
			return 0;
		}	
	}else{
		if(input.value!=document.getElementById("pass").value){
			input.setCustomValidity("Las Contraseñas no coinciden");
			return 1;
		}else{
			input.setCustomValidity("");
			return 0;
		}
	}
	
}
function RegistrarCV(){
	var form = document.getElementById("registro");
	var elements=form.elements.length;
	var K=0;
	for (var i = 0; i<elements; i++) {
		if(form.elements[i].value=='' && form.elements[i].hasAttribute('required')){
			K++;
		}else{
			K=K;
		}
    }
    if(K>0){
    	
    }else{
    	    var test= confirm("Esta Seguro de Que desea Almacenar esta Informacion?");
    	    if(test){
    	    	form.Confirma.value="T";
    	    	form.submit();
    	    }else{
    	    	form.Confirma.value="F";
    	    		
    	    }
      	}

}
function Registrar(){
	var form = document.getElementById("registro");
	var elements=form.elements.length;
	var K=0;
	for (var i = 0; i<elements; i++) {
		if(form.elements[i].value=='' && form.elements[i].hasAttribute('required')){
			K++;
		}else{
			K=K;
		}
    }
    if(K>0){
    	
    }else{
      		passCifrado=Base64.encode(form.pass.value);	
      		form.pass.value=passCifrado;
      		form.passc.value=passCifrado;
      		form.submit();	
      	}
}
function caracterraro(cad){
   var Vocales = new Array('*','+','=','.',';',':','"','-','%','$','!','@','#','^','&','(',')','/','{','}','[',']','|','\\','_','?','','ñ','Ñ',"'");
   var i=0;
   var c=0;
   while(Vocales[i]!=''){
       if(cad==Vocales[i]){
          c=1;
       }
       else{
       }
       i++;
   }
 return c;
}
function Minus(cadena){
    origen=cadena.value;
	cadena.value=origen.toLowerCase();
}
function Mayus(cadena){
	origen=cadena.value;
	n=origen.length-1;
	if(caracterraro(origen.charAt(n))==1){
	  po=origen.substring(0,n);
	  p=po;
	  cadena.value=p.toUpperCase();
	}else{
	  p=cadena.value;
	  cadena.value=p.toUpperCase();
	}
}
var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}
		return string;
	}
}
    // En la base las barras son B1..B16; en el tablero sus celdas son B01..B16.
    function idCeldaMesa(mesa){
        mesa = String(mesa).trim();
        if(/^B[0-9]$/.test(mesa)){
            mesa = "B0"+mesa.substr(1);
        }
        return mesa;
    }
    // Texto negro sobre colores claros y blanco sobre oscuros, para que el numero de mesa siempre se lea.
    function colorDeTexto(fondo){
        var hex = String(fondo).trim().replace("#","");
        if(!/^[0-9a-fA-F]{6}$/.test(hex)){
            return "white";
        }
        var r = parseInt(hex.substr(0,2),16), g = parseInt(hex.substr(2,2),16), b = parseInt(hex.substr(4,2),16);
        return (r*299 + g*587 + b*114)/1000 > 150 ? "black" : "white";
    }
    function pintar(mesa, color){
        var celda = document.getElementById(idCeldaMesa(mesa));
        if(!celda){
            return;
        }
        celda.style.background=String(color).trim();
        celda.style.color=colorDeTexto(color);
    }
    // Saca la mesa de la vista (la usan los filtros); iniciaSalon() la devuelve.
    function ocultarMesa(mesa){
        var celda = document.getElementById(idCeldaMesa(mesa));
        if(celda){
            celda.classList.add("oculta");
        }
    }
    function LanzaURL1(caso){
    	url="";
    	switch(caso){
    		case 1:
    			url="GestionSalon.php";
    		break;
    		case 2:
    			fecha=document.getElementById("fechadash").value;
    			url="ListaCumple.php?fecha="+fecha;
    		break;
    		case 3:
    			fecha=document.getElementById("fechadash").value;
    			url="ListaGrado.php?fecha="+fecha;
    		break;
    	}
    	window.location.href=url;
    }
    // Abre una pantalla del salon en la ventana modal del tablero (componente modal de Bootstrap 5), sin salir de el.
    function modalMesa(){
        var elemento = document.getElementById("modalMesa");
        if(!elemento.dataset.preparada){
            elemento.dataset.preparada = "1";
            // al cerrarse por cualquier via (X, Esc, clic fuera, Volver) el tablero se recarga para reflejar los cambios
            elemento.addEventListener("hidden.bs.modal", function(){
                document.getElementById("marcoMesa").src="about:blank";
                location.reload();
            });
        }
        return bootstrap.Modal.getInstance(elemento) || new bootstrap.Modal(elemento);
    }
    function abrirModalMesa(url, titulo){
        document.getElementById("marcoMesa").src=url;
        document.getElementById("tituloModalMesa").textContent=titulo || "Gestión de la mesa";
        modalMesa().show();
    }
    function cerrarModalMesa(){
        modalMesa().hide();
    }
    function lanzaURL(mesa){
        fechad=document.getElementById("fechadash").value;
        abrirModalMesa("GestionMesa.php?mesa="+mesa+"&fecha="+fechad, "Gestión de la mesa · "+fechad);
    }
    function lanzaURL3(reserva){
        abrirModalMesa("GestionMesaA.php?reserva="+reserva);
    }
    
    // Pide a Querys.php una lista "n;mesa;mesa;..." y entrega las mesas como arreglo.
    function pedirMesas(parametros, alTerminar){
        var peticion = objetoAjax();
        peticion.open('POST', "Querys.php", true);
        peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        peticion.onreadystatechange=function() {
            if (peticion.readyState==4) {
                var partes = peticion.responseText.toString().split(";");
                var total = parseInt(partes[0], 10) || 0;
                alTerminar(partes.slice(1, total+1));
            }
        }
        peticion.send(parametros);
    }
    // Deja a la vista solo las mesas libres.
    function Vacias(){
    	iniciaSalon();
        var fechad=document.getElementById("fechadash").value;
        pedirMesas("guard=BMOD&fecha="+fechad, function(ocupadas){
            for(var i=0;i<ocupadas.length;i++){
                ocultarMesa(ocupadas[i]);
            }
        });
    }
    // Deja a la vista, con su color, solo las mesas ocupadas que cumplen el filtro.
    function TipoRes(tipo){
        pintarSalon(function(ocupadas){
            var fechad=document.getElementById("fechadash").value;
            pedirMesas("guard=STRE&fecha="+fechad+"&tipo="+tipo, function(fuera){
                var visibles = {};
                for(var i=0;i<ocupadas.length;i++){
                    visibles[ocupadas[i]] = true;
                }
                for(var j=0;j<fuera.length;j++){
                    delete visibles[idCeldaMesa(fuera[j])];
                }
                var celdas = document.getElementsByClassName("mesa");
                for(var k=0;k<celdas.length;k++){
                    if(!visibles[celdas[k].id]){
                        ocultarMesa(celdas[k].id);
                    }
                }
            });
        });
    } 
    function iniciaSalon(){
        var celdas = document.getElementsByClassName("mesa");
        for(var i=0;i<celdas.length;i++){
            // sin estilos propios la celda vuelve al aspecto de mesa libre que define la hoja de estilos
            celdas[i].style.background="";
            celdas[i].style.color="";
            celdas[i].classList.remove("oculta");
        }
    }
    // Pinta cada mesa ocupada con el color de su tipo de asignacion.
    // alTerminar (opcional) recibe los ids de las celdas ocupadas.
    function pintarSalon(alTerminar){
    	iniciaSalon();
        var fechad = document.getElementById("fechadash").value;
        var peticion = objetoAjax();
        peticion.open('POST', "Querys.php", true);
        peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        peticion.onreadystatechange=function() {
            if (peticion.readyState==4) {
                var Cadena = peticion.responseText.toString().split(";");
                var nfilas = parseInt(Cadena[0].split("|")[0], 10) || 0;
                var ocupadas = [];
                for(var i=0;i<nfilas;i++){
                    var Cad1 = Cadena[(i+1)].split("|");
                    pintar(Cad1[0], Cad1[1]);
                    ocupadas.push(idCeldaMesa(Cad1[0]));
                }
                if(typeof alTerminar=="function"){
                    alTerminar(ocupadas);
                }
            }
        }
        peticion.send("guard=TMCD&fecha="+fechad);
    }
function control(comp){
	var comp1="";
	switch(comp){
		case 1:
		comp1="CFSe";
		break;
		case 2:
		comp1="CantPer";
	}
	var val=document.getElementById(comp1).value;
	document.getElementById("cper").innerHTML="<h4>"+val+"</h4>";
}
function mesaInamovible(){
	variable = document.getElementById("myonoffswitch").checked;
	res = document.getElementById("Reserva").value;
	fecha = document.getElementById("fecha").value;
	Mesa = document.getElementById("Mesa").value;
	tipoR =document.getElementById("tipof").value;
	if(fecha.substr(2,1)!='/'){
		fecha=CambiaFormatoFecha(fecha);
	}else{
	    fecha=fecha;
	}
	datos = "Querys.php";
  	ajax=objetoAjax();
  	ajax.open('POST', datos,true);
  	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  	ajax.send('guard=INMM&mesa='+Mesa+"&reserva="+res+"&tipof="+tipoR);
  	ajax.onreadystatechange=function() {
	   if (ajax.readyState==4) {
	   	 var ll = ajax.responseText.toString();
	   	 if(ll==1){

	   	 }else{
	   	 	alert("Ocurrio un problema al bloquear la mesa"+ll);
	   	 }
	    }
	}
}
function prueba(comp){
	var comp1="";
	switch(comp){
		case 1:
		comp1="CFSe";
		break;
		case 2:
		comp1="CantPer";
		break;
		case 3:
		comp1="CantPer";
		break;
	}
	var val=document.getElementById(comp1).value;
	document.getElementById("cper").innerHTML="<h4>"+val+"</h4>";
}