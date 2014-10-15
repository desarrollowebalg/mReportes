function rep_abrir_modulo(m){
	$.ajax({
		type: "POST",
        url: "index.php?m="+m+"&c=default",
        data: "",
        success: function(datos){
			if(datos!=0){
				$("#rep_content").html(datos);
			    //$("#example").treeview();
			}else{
				$("#rep_content").html("No se han Creado un menú");
			}
			
        }
	});
}
/*
 *@name 	Funcion para hacer las peticiones ajax
 *@author	Gerardo Lara
 *@date		26 - Agosto - 2014
*/
function ajaxReportes(accion,c,parametros,divCarga,divResultado,tipoPeticion){
	$.ajax({
		url: "index.php?m=mReportes&c="+c,
		type: tipoPeticion,
		data: parametros,
		beforeSend:function(){ 
			$("#"+divCarga).show().html("Procesando Informacion ..."); 
		},
		success: function(data) {
			$("#"+divCarga).hide();
			controladorAcciones(accion,data,divResultado);
		},
		timeout:90000000,
		error:function() {
		    $("#"+divCarga).hide();
		    $("#error").show();
		    $("#error_mensaje").html('Ocurrio un error al procesar la solicitud.');
		}
	});
}
/*
 *@name 	Funcion controlar las acciones dependiendo de la accion pedida
 *@author	Gerardo Lara
 *@date		6 - Mayo - 2014
*/
function controladorAcciones(accion,datos,divResultado){
    switch(accion){
		case "cargarWidget":
	    	$("#"+divResultado).show().html(datos);
		break;
    }
}
function muestraWidget(widget){
	switch(widget){
		case "fechaHora":
			parametros="action=cargarWidget&widget="+widget;
		break;
		case "gruposUnidades":
			idCliente=$("#hdnIdCliente").val();
			idUsuario=$("#hdnIdUsuario").val();
			parametros="action=cargarWidget&widget="+widget+"&idCliente="+idCliente+"&idUsuario="+idUsuario;
		break;
	}
	//ajaxReportes(accion,c,parametros,divCarga,divResultado,tipoPeticion)
	ajaxReportes("cargarWidget","controladorWidgets",parametros,"cargador2","rep_content","POST");
}

