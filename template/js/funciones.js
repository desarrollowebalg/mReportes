/*
dw | AlxMoreno
function crearAjax(destino, componente){
	var dstn = eval( "$('#"+destino+"')" );
	dstn.html("<div id=\"vtaLoader\"><p>Cargando Información, espere por favor...</p><img src=public/images/wait.gif /></div>");
	var ajax = nuevoAjax();
	ajax.open("GET", "index.php?m=users&c="+componente,true);
	ajax.onreadystatechange=function() {
		if(ajax.readyState==4) {
			var result =ajax.responseText;
			alert(result);
			if(result != 0){
//				dstn.html(ajax.responseText);
				dstn.animate({"opacity":"0"},300, function(){ dstn.html(ajax.responseText); dstn.animate({"opacity":"1"},700) });
			}else{
				dstn.html("<br><br><br><br><center>La busqueda no obtuvo resultados</center>");
			}
		}
	}
	ajax.send();
}
*/
/*FUNCION CREAR-AJAX-PERFILES*/
function crearAjaxPerf(destino, componente){
	var dstn = eval( "$('#"+destino+"')" );
	//dstn.html("<div id=\"vtaLoader\"><p>Cargando Informaci&oacute;n, espere por favor...</p><img src=\"public/images/wait.gif\" /></div>");
	dstn.html("<div id=\"vtaLoader\" style=\"border:#3F0 solid 0px; height:180px;\"><p>Cargando Informaci&oacute;n, espere por favor...</p><img src=\"public/images/wait.gif\" /></div>");
	var ajax = nuevoAjax();
	ajax.open("GET", "index.php?m=users&c="+componente,true);
	ajax.onreadystatechange=function() {
		if(ajax.readyState==4) {
			var result =ajax.responseText;
			if(result != ""){
//				dstn.html(ajax.responseText);
				dstn.animate({"opacity":"0"},300, function(){ dstn.html(ajax.responseText); dstn.animate({"opacity":"1"},700) });
				if(document.getElementById('t_d').value=="E"){
				   setTimeout("llenar_cajitas();", 600);
				}
				
			}else{
				dstn.html("<br><br><br><br><center>No existen modulos para asignar</center>");
			}
		}
	}
	ajax.send();
}
/*FUNCION CREAR-AJAX DIRECCION*/
function crearAjaxDir(destino, componente){
	var dstn = eval( "$('#"+destino+"')" );
	dstn.html("<img src=public/images/loader.gif>");
	var ajax = nuevoAjax();
	ajax.open("GET", "index.php?m=clients&c="+componente,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			var result =ajax.responseText;
			if(result != 0){
				//dstn.animate({"opacity":"0"},300, function(){ dstn.html(ajax.responseText); dstn.animate({"opacity":"1"},300) });
				dstn.html(ajax.responseText);
			}else{
				dstn.html("La busqueda no obtuvo resultados");
			}
		}
	}
	ajax.send();
}

function crearAjaxSec(destino,seccion){
	var dstn = eval( "$('#"+destino+"')" );
	dstn.html("<img src=\"public/images/loader.gif\" />");
	var ajax = nuevoAjax();
	ajax.open("GET", "index.php?m=users&c=mUpdateSecs&sec="+seccion,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			var result =ajax.responseText;
			if(result != 0){
				//dstn.animate({"opacity":"0"},300, function(){ dstn.html(ajax.responseText); dstn.animate({"opacity":"1"},300) });
				dstn.html(ajax.responseText);
			}else{
				dstn.html("La busqueda no obtuvo resultados");
			}
		}
	}
	ajax.send();
}

function showProfiles(){
	//alert('fdfdfd');
	//alert($("#user_profile").val());
	document.getElementById('priv_id').value = document.getElementById('user_profile').value
	var profDest = "mGetProfiles&id_profile=" + $("#user_profile").val()+"&tipo="+document.getElementById('t_d').value;
	crearAjaxPerf('formPrivilegesInt', profDest);
	
	
	
	
}

function llenar_cajitas(){
	
  if(document.getElementById('priv_com').value == document.getElementById('priv_id').value){
	// alert(document.getElementById('priv_com').value+'='+ document.getElementById('priv_id').value);
     z=document.getElementById('tabla_x').getElementsByTagName('input');
     for(c=0;c<arreglo_marcas.length;c++){
		var partes = arreglo_marcas[c].split("@");
		if(partes[1]=="true"){
		 v = true;	
		}else{
		 v=false;	
		}
		  z[c].checked = v;	
		 //alert(partes[0]);
	
      }
  }
}
//----------------------------------------- funcion que quita marca a pricipal x columna si hay cuando menos una casilla no maracada.

function quitar_marca(d){
	 var parte = d.id.split('_');
	 document.getElementById(parte[1]).checked=false; 
	 var tb_rows=document.getElementById('tabla_x');
	 var total_chks = contar_chks(parte[1]);
   // alert(total_chks +'== '+tb_rows.rows.length);
     if(total_chks == ((tb_rows.rows.length-1))){
	   document.getElementById(parte[1]).checked=true; 
	  }else{
	   document.getElementById(parte[1]).checked=false; 
	 }
	
}

//----------------------------------------------------- funcion que busca checks marcados..
function contar_chks(d){
	var conta = 0;
    var x=document.getElementById('tabla_x').getElementsByTagName('input');
	for(h=3;h<x.length;h++){
	  var y = x[h].id.split('_');	
	  if(y[1]==d){ 
		if(x[h].checked==true){
			conta = conta +1; 
		}
	  }
	}
	return conta;
}


