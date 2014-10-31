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
/**@name 	Funcion para hacer las peticiones ajax*/
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
			controladorAccionesReportes(accion,data,divResultado);
		},
		timeout:90000000,
		error:function(error) {
		    $("#"+divCarga).hide();
		    $("#error").show();
		    $("#error_mensaje").html(error);
		}
	});
}
/**@name 	Funcion controlar las acciones dependiendo de la accion pedida*/
function controladorAccionesReportes(accion,datos,divResultado){
    switch(accion){
		case "cargarWidget":
	    	$("#"+divResultado).show().html(datos);
		break;
		case "mostrarPlantillaReporte":
			$("#"+divResultado).show().html(datos);
		break;
		case "mostrarReporte":
			$("#"+divResultado).show().html(datos);
		break;
    }
}
/**@name 	Funcion controlar las peticiones de los reportes*/
function cargaElementosReporte(idReporte){
	idCliente=$("#hdnIdCliente").val();
	idUsuario=$("#hdnIdUsuario").val();
	parametros="action=mostrarPlantillaReporte&idReporte="+idReporte+"&idCliente="+idCliente+"&idUsuario="+idUsuario;
	//se manda a armar la estructura para el reporte
	ajaxReportes("mostrarPlantillaReporte","controlador",parametros,"cargador2","rep_content","POST");
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
		case "usuarios":
			idCliente=$("#hdnIdCliente").val();
			idUsuario=$("#hdnIdUsuario").val();
			parametros="action=cargarWidget&widget="+widget+"&idCliente="+idCliente+"&idUsuario="+idUsuario;
		break;
	}
	//ajaxReportes(accion,c,parametros,divCarga,divResultado,tipoPeticion)
	ajaxReportes("cargarWidget","controladorWidgets",parametros,"cargador2","rep_content","POST");
}
function extraerReporte(parametros){
	idReporteOpcion=$("#hdnIdReportesOpcion").val();//se extrae el idReporteOpcion
	//alert("Parametros = "+parametros+"\n\n"+"id reporteopcion = "+idReporteOpcion);
	idCliente=$("#hdnIdCliente").val();
	idUsuario=$("#hdnIdUsuario").val();
	elementosAnalizar=$("#hdnElementosAnalizar").val();
	var parametrosR="action=mostrarReporte&parametros="+parametros+"|||"+idReporteOpcion+"&idCliente="+idCliente+"&idUsuario="+idUsuario+"&elementosAnalizar="+elementosAnalizar;
	//alert(parametrosR)
	ajaxReportes("mostrarReporte","controlador",parametrosR,"cargador2","tabReporteResumen","GET");
}
