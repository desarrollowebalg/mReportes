/**/
var ide = 0;
function init(){
	//document.getElementById('fitro').style.display=OCULTO;
	$("#filtro").css("display","");	
	tofiltro();
}

function tofiltro(){
	var radio1 = document.getElementById("rd1").checked;
	var radio2 = document.getElementById("rd2").checked;
	document.getElementById("txtfil").value = "";
	if(radio1 == true){
		//document.getElementById("fitro").style.display=OCULTO;
		$("#filtro").css("display","");
		r_filtro();
	}else if(radio2 == true){
		//document.getElementById("fitro").style.display=VISIBLE;
		$("#filtro").css("display","");
	}
}


/*-----------------------------------  modificado  -------------------------------------------- */
function r_filtro(){
	var filt = 0; 
	/*	var cbo_filt = document.getElementById("cbo_filtro").value;*/
    var text	 = document.getElementById("txtfil").value;
	var destino = document.getElementById('list_formas');
	var desc = document.getElementById("info_formas");
	desc.innerHTML="";
	destino.innerHTML="<br><br><br><br><center>Cargando Unidades, espere porfavor..."+"<br><br><img src=public/images/wait.gif> </center>";
	var ajax = nuevoAjax();
		ajax.open("GET", "index.php?m=unidades&c=mGetUnits&txtfil="+text,true);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
				var result =ajax.responseText;
				//alert(result);
				if(result != 0){
					destino.innerHTML = ajax.responseText;
					tigra_tables('MyData', 0, 0, '#ffffff', '#DDD', '#AEC7F3', '#5283D8');
					contextMenu();
				}else{
					destino.innerHTML="<br><br><br><br><center>La busqueda no obtuvo resultados</center>";
				}
			}			
		}		
	ajax.send(null);		
}

/*----------------------------------- -------------------------------------------- */

function change_id(id){
	ide = id;
}

function muestra_info(){
	if(ide>0){
		var destino = document.getElementById("info_formas");
		var ajax = nuevoAjax();
		ajax.open("GET", "index.php?m=users&c=mGetDescUsers&idUser="+ide,true);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {				
				var result =ajax.responseText;
				if(result != 0){
		       		destino.innerHTML = ajax.responseText;			
				}else{
					destino.innerHTML="<br><br><br><br><center>La busqueda no obtuvo resultados</center>";
				}
			}			
		}		
		ajax.send(null);
	}
}
//________________________________________________________________

function contextMenu() {
	// Show menu when #myDiv is clicked
	$("#myDiv").contextMenu({
		menu: 'myMenu'
	},
	function(action, el, pos) {
		if(ide != 0){
			if(action == "edit"){
				//muestra_editar_form(ide);									
			} else if(action == "new"){
				nuevo_medio();
			} else if(action == "act_desact"){
				//alert('act_desact');
					 change_estatus(ide);
				
			} else if(action == "chan_pas"){
				//alert('chan_pas');
				change_pass();
			} else if(action == "delete"){
				//alert('delete');
			    delete_user(ide);
			}

		}else{
			alert("Favor de seleccionar un usuario de la lista");
		}
	});																	
}

//________________________________________________________________

function delete_user(ide){
	var resp= confirm("Realmente desea eliminar este usuario");
	if(resp== true)
	{
	  //alert(ide);
	var ajax = nuevoAjax();
		ajax.open("GET", "index.php?m=users&c=mDeleteUser&idUser="+ide,true);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {				
				var result =ajax.responseText;
				if(result != 0){
					alert("El usuario se ha eliminado correctamente");
					init();
				}else{
					destino.innerHTML="<br><br><br><br><center>La busqueda no obtuvo resultados</center>";
				}
			}			
		}		
		ajax.send(null);
   }
} 

//-------------------------------------------------------------
function change_estatus(ide){
		var ajax = nuevoAjax();
		ajax.open("GET", "index.php?m=users&c=mActiveUsuarios&idUser="+ide,true);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {				
				var result =ajax.responseText;
				if(result != 0){
					alert("El estatus se ha editado correctamente");
					init();
				}else{
					destino.innerHTML="<br><br><br><br><center>La busqueda no obtuvo resultados</center>";
				}
			}			
		}		
		ajax.send(null);
   }

//-------------------------------------------------------------
function change_pass(){
	var ajax = nuevoAjax();
	var title = '';
	var name  =	''; 					  
	var href  = 'index.php?m=users&c=mChange_Pass&height=90&width=650&modal=true';
	var alt   = '';
	var rel   = true;
	var t = title || name || null;
	var a = href  || alt;
	var g = rel   || false;
	tb_show(t,a,g);
}

//----------------------------------------------------------------

function val_chge_pass()
{
	var pas  = document.getElementById("pas_chg").value;
	var pas_confir =document.getElementById("pas_ch_conf").value;
	if(pas!=""){
		if(pas.length>4){
			if(pas_confir!=""){
				pass_iquals(pas,pas_confir);
		  } else {alert("Favor de confirmar la contraseña");}
		}else{ alert("La contraseña debe ser mayor a 4 digitos");}
	}else{ alert("Favor de ingresar una contraseña");}
		
  }

//---------------------------------------------------------------


function  pass_iquals(pas,pas_confir)
{
	if(pas == pas_confir)
	{
		envia_chge_pass(pas,pas_confir);
	}
	  else {alert("Las contraseñas no coinciden, favor de intentar mas tarde");}
}

 ///----------------------------------------------------------------------
 
function oculta_ch_pass()
{
	document.getElementById("respuesta_chan_pass").style.display="none";
}
 ///----------------------------------------------------------------------

function envia_chge_pass(pas,pas_confir){
		var destino = document.getElementById("respuesta_chan_pass");
		destino.innerHTML="<img src=public/images/wait.gif>";
		destino.style.display="block";
		var ajax = nuevoAjax();
		ajax.open("GET", "index.php?m=users&c=mUpdatePass&idUser="+ide+"&pas="+pas+"&pas_confir="+pas_confir,true);
		ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {				
				var result =ajax.responseText;
				if(result!= 0){
		       		//destino.innerHTML = ajax.responseText;
					alert("La contraseña se ha cambiado correctamente");
					tb_remove();
					init();
				}else{
					destino.innerHTML="<br><br><br><br><center>La busqueda no obtuvo resultados</center>";
				}
			}			
		}		
		ajax.send(null);
   }
 ///----------------------------------------------------------------------  
function nuevo_medio(){
	var ajax = nuevoAjax();
	var title = '';
	var name  =	''; 					  
	var href  = 'index.php?m=unidades&c=mNewUnits&height=290&width=395&modal=true&tipo=N';
	var alt   = '';
	var rel   = true;
	var t = title || name || null;
	var a = href  || alt;
	var g = rel   || false;
	tb_show(t,a,g);
}

function editar(){
	//alert(ide);
	if(ide != 0){ muestra_editar_form(ide);	}
	else{ alert("Favor de seleccionar una unidad de la lista");}
}

/*  +++++++++++++++++++++++++++++++++++++++++++ DANIEL ++++++++++++++++++++++++++++++++++++  */

function muestra_editar_form(ide){
	//alert('hola edicion');
	if(ide>0){
		var title = '';
		var name  =	''; 					  
		var href  = 'index.php?m=unidades&c=mUpdateUnits&idUnit='+ide+'&height=280&width=395&modal=true&tipo=E';
		var alt   = '';
		var rel   = true;
		var t = title || name || null;
		var a = href  || alt;
		var g = rel   || false;
		tb_show(t,a,g);
	}else{
		alert("Favor de Seleccionar una Unidad de la Lista.");
	}
}
//--------------------  funcion que marca/quita marca los checks

function checkTodos(chk,pID){
	//alert('hola:'+chk.id+'-'+pID);
	
	$( "#" + pID + " :checkbox").attr('checked', $('#' +chk.id).is(':checked')); 
	
}

//------------------------------------------- funcion que marca/quita marca los checks de columna
function checkPartes(chk){
	var x = document.getElementById('tabla_x').getElementsByTagName('input');
	for(h=3;h<x.length;h++){
		var c= x[h].id.split('_');
		if(c[1]==chk.id){
		   if(chk.checked==true){	
			x[h].checked = true;
		   }else{
		     x[h].checked = false;  
		   }
		}
	}
}

//--------------------------------------  funcion valida check para guardar pass

function envio_comandos(x){
 if(x.checked==true){
  var caja = '<input type="text" id="caja_comandos" class="textIn" onkeypress="return validarIE(event)"/>';
  $("#caja").html(caja);
  $("#clave").html('Clave');
  $("#kja_2_comandos").val(1);
  
 }else{
	 var caja = '<input type="text" id="caja_comandos" class="textIn" style=" display:none;" value=""/>';
     $("#caja").html(caja);	
     $("#clave").html('');
     $("#kja_2_comandos").val(0);

 }
	
}
//-------------------------------------  Daniel ------------------------

function valida_datos_e(){
 
  var fl = document.getElementById('fl').value;
  var mark  = document.getElementById('mark').value;
  var model = document.getElementById('model').value;
  var tipo = document.getElementById('tipo').value;
  var des   = document.getElementById('des').value;
  var INEI  = document.getElementById('INEI').value;
  
  if(fl == 0){
	  alert("Debe de Elejir una Flota");
	  return false;
  }
  
   if(mark == 0){
	  alert("Debe de Elejir una Marca");
	  return false;
   }
 	if(model == 0){
	  alert("Debe de Elejir un M\u00f3delo");
	  return false;
   }   
   
	if(tipo == 0){
	  alert("Debe de Elejir un Tipo");
	  return false;
   }      

 	if(des == ""){
	  alert("Debe de ingresar una Descripci\u00f3n");
	  return false;
   }

 	if(INEI == ""){
	  alert("Debe de ingresar un IMEI");
	  return false;
   }
  
  var destino1 = document.getElementById('mns');				
  destino1.innerHTML='';
  destino1.innerHTML='Guardando Datos... <img src="public/images/wait.gif" align="middle" title="Cargando datos" />';
  
 var ajax = nuevoAjax();
 var a=document.getElementById('act').value;
 var b=document.getElementById('fl').value;
 var c=document.getElementById('model').value;
 var d=document.getElementById('des').value;
 var e=document.getElementById('INEI').value;
 var f=document.getElementById('tipo').value;
 var s=document.getElementById('segundos').value;
 //alert(ide);
  ajax.open("GET", "index.php?m=unidades&c=mSaveUnits&act="+a+"&fleet="+b+"&model="+c+"&des="+d+"&inei="+e+"&tipo="+f+"&bandera=2&ide="+ide+"&seg="+s,true);
  ajax.onreadystatechange=function() {
  	 if (ajax.readyState==4) {
						
	     var result =ajax.responseText;
						
			if(result!= 0){
			      alert('La informacion se ha Guardado correctamente');
					var x_cadena = ajax.responseText;
					    r_filtro();
						tb_remove(); 	
							
						}else{
							 alert('Ha ocurrido un error, intentelo m\u00e1s Tarde');
						}
				}	
 
  }
  

 ajax.send(null);
}


//--------------------------------------  funcion cargar select model (Rodwyn)---------------------------//
function model(x){
 var destino1 = document.getElementById('modelo');				
 destino1.innerHTML='<select><option>cargando...</option></select>';	
  var ajax = nuevoAjax();
 
  ajax.open("GET", "index.php?m=unidades&c=query_model&mark="+x,true);
  ajax.onreadystatechange=function() {
  	 if (ajax.readyState==4) {
						
	     var result =ajax.responseText;
						
			if(result!= 0){
					// alert('La informacion se ha Guardado correctamente');
						  //  destino1.innerHTML = ajax.responseText;
					destino1.innerHTML = ajax.responseText;	
							
						}else{
							 destino1.innerHTML='No se encontro informaci&oacute;n';
						}
				}	
 
  }
  

 ajax.send(null);	
	}
//--------------------------------------  funcion validar datos y guardar tNewUnits (Rodwyn)---------------------------//	
function datos_tnu(){
	if(document.getElementById('fl').value==0){
		alert('Debe seleccionar una flota');
		return false;
	}
	if(document.getElementById('mark').value==0){
		alert('Debe seleccionar una marca');
		return false;
	}
	if(document.getElementById('model').value==0){
		alert('Debe seleccionar un modelo');
		return false;
	}	
	if(document.getElementById('des').value.length==0){
		alert('Debe escribir una descripci\u00f3n');
		document.getElementById('des').value='';
		document.getElementById('des').focus();
		return false;
		}
	if(document.getElementById('inei').value.length==0){
		alert('Debe escribir una clave o INEI');
		document.getElementById('inei').value='';
		document.getElementById('inei').focus();
		return false;
		}	
  var destino1 = document.getElementById('mns');				
  destino1.innerHTML='';
  destino1.innerHTML='Guardando Datos...<img src="public/images/wait.gif" align="middle" title="Cargando datos" />';
  
 var ajax = nuevoAjax();
 var a=document.getElementById('ac').value;
 var b=document.getElementById('fl').value;
 var c=document.getElementById('model').value;
 var d=document.getElementById('des').value;
 var e=document.getElementById('inei').value;
 var f=document.getElementById('tipo').value;
 var s=document.getElementById('segundos').value;
 
  ajax.open("GET", "index.php?m=unidades&c=mSaveUnits&act="+a+"&fleet="+b+"&model="+c+"&des="+d+"&inei="+e+"&tipo="+f+"&bandera=1&seg="+s,true);
  ajax.onreadystatechange=function() {
  	 if (ajax.readyState==4) {
						
	     var result =ajax.responseText;
						
			if(result!= 0){
				
					//var x_cadena = ajax.responseText;
				    
				alert('La informacion se ha Guardado correctamente');
						  //  destino1.innerHTML = ajax.responseText;
						 r_filtro();
						tb_remove();			
							
						}else{
							 destino1.innerHTML='No se encontro informaci&oacute;n';
						}
				}	
 
  }
  

 ajax.send(null);	
	
	}

//--------------------------------------  funcion buscar imei (Rodwyn)---------------------------//	
function buscar_imei(x){
var destinoz = document.getElementById('txt');	
	if(document.getElementById('espejo').value!="" && document.getElementById('espejo').value!=x){
	if(document.getElementById('inei').value==""){
		alert('Debe escribir una clave o IMEI');
		return false;
	}
	
	
 // alert(d);

		 destinoz.innerHTML='<h5 style="color:#0000ff;"><img width="12" height="12" src="public/images/lg.gif"/> Verificando...</h5>';	
		 	
		  var ajax = nuevoAjax();
		  ajax.open("GET", "index.php?m=unidades&c=mQueryImei&imei="+x,true);
		  ajax.onreadystatechange=function() {
				 if (ajax.readyState==4) {
									
					 var result =ajax.responseText;
						//alert(result);			
						 if(result!= 0){
destinoz.innerHTML = '<h5 style="color:#cc3300;">Ya existe la clave o IMEI<input type="text" size="2" style="border:#fff solid 1px;"/></h5>';
							//document.getElementById('valida_sn').value=1;
						 }else{
							 destinoz.innerHTML='<h5 style="color:#008000;">Disponible<input type="text" size="2" style="border:#fff solid 1px;"/></h5>';
							
							// document.getElementById('valida_sn').value=0;
						 }
				 }	
		 
		  }
		 ajax.send(null);	
}else{
		 destinoz.innerHTML="";
		}			


	}