<?php
	/*
	*CONFIGURACION DE LAS VARIABLES PARA LAS CLAUSULA WHERE DE LAS CONSULTAS
	*/

function reconstruyeArreglo($elementos,$valores,$idCliente,$idUsuario){
  	
   $configuracionParametros = array(
		"@USUARIOSISTEMA"		=>	"USUARIOSISTEMA",
		"@FECHAINICIAL"			=>	"widgetFechaInicial",
		"@HORAINICIAL"			=>	"widgetCboHoraInicial_1",
		"@MININICIAL"			=>	"widgetCboHoraFinal_1",
		"@FECHAFINAL"			=>	"widgetFechaFinal",
		"@HORAFINAL"			=>	"widgetCboHoraInicial_2",
		"@MINFINAL"				=>	"widgetCboHoraFinal_2",
		"@UNIDADES"				=>	"cboWidgetGruposUnidades",
		"@USUARIOSEVIDENCIAS"	=>	"cboWidgetUsuariosEvidencias",
		"@PDICLIENTE"			=>	"cboWidgetPDICliente",
		"@GEOCERCA"				=>	"cboWidgetGeocercaCliente",
		"@RECURSOHUMANO"		=>	"cboWidgetRecursoHumanoCliente",
		"@CUESTIONARIO"			=>	"cboWidgetCuestionariosCliente",
		"@CODEQUIPO" 		    =>	"cboWidgetEquipo",
		"@LASTCLIENTE"          =>  obtenerHistLastNombre($idCliente,'l'),
		"@HISTCLIENTE"          =>  obtenerHistLastNombre($idCliente,'h'),
		"@IDCLIENTE"            =>  $idCliente
	);
	
  foreach($configuracionParametros as $indice =>$valor){	 
     for($e=0;$e<count($elementos);$e++){
		 if($valor == $elementos[$e]){
			 $configuracionParametros[$indice] = $valores[$e];  
		 }
	 }
  }
  
  return $configuracionParametros;
}




//*******************************************  Funcion para buscar en el arreglo la coincidencia de indice	
	 
function buscarCadena($arreglo,$texto,$operador){
  $cadena_nueva = '';
  $bandera = 0;
	 foreach($arreglo as $indices => $valores){
		   
		   	if($bandera===0){
				$cadena_nueva = str_replace($indices,$valores,$texto);
				$bandera=1;
			}else{
			    $cadena_nueva = str_replace($indices,$valores,$cadena_nueva);
			}

    }
  return $cadena_nueva;
}

//*******************************************  Funcion obtener nombre de historico o last
 function obtenerHistLastNombre($id_client,$tipo){
		$id_client = (int)$id_client;	
		$table_name = '';		
		if (strlen($id_client) < 5) {
	        $table_name = str_repeat('0', (5-strlen($id_client)));
	    }
   if($tipo == 'h'){		
    	return 'HIST'.$table_name . $id_client;
   }else{
	   	return 'LAST'.$table_name . $id_client;
   }

}

?>