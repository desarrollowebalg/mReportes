<?php
/**
*@description       Clase para el manejo de los diferentes reportes en la plataforma
*@copyright         Air Logistics & GPS S.A. de C.V.  
*@author 			Gerardo Lara
*@version 			1.0.0
*@fecha 			14 Octubre 2014
*/

class reportes{
	private $objDb;
	private $objBdG;
	private $conn;
	private $host;
	private $port;
	private $bname;
	private $user;
	private $pass;
	private $elementos 	=	array();
	private $valores	=	array();
	private $idCliente	= 	"";
	private $idUsuario  =	"";

	function __construct() {
  		include "config/database.php";
  		$this->host=$config_bd['host'];
  		$this->port=$config_bd['port'];
  		$this->bname=$config_bd['bname'];
  		$this->user=$config_bd['user'];
  		$this->pass=$config_bd['pass'];
   	}

   	private function iniciarConexionDb(){
   		$objBd=new sql($this->host,$this->port,$this->bname,$this->user,$this->pass);
   		return $objBd;
   	}

   	private function iniciarConexionDbGrid(){
   		/*$objBdG=mysql_connect($this->host,$this->user,$this->pass);
   		if($objBdG){
   			@mysql_select_db($this->bname)or die("Error al conectar con la base de datos");
   		}*/
   		$objBdG=new mysqli($this->host,$this->user,$this->pass,$this->bname);
   		return $objBdG;
   	}

   /*	private function ejecutaOperacion($sqlReporte,$pag,$tipoSQL){
   		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");

   		$registrosAMostrar="20";
   		if($pag==0){
   			$registrosAEmpezar=($pag-1) * $registrosAMostrar;
   			$pagAct=$pag;
   		}else{
   			$registrosAEmpezar=0;
   			$pagAct=1;
   		}
   		//se evalua el tipo de SQL
   		if($tipoSQL=="Q"){

   		}else if($tipoSQL=="P"){

   		}
   		//se ejecuta el Query o procedimiento


   	}*/

   	private function evaluaParametro($strParametro){
		include "configuracionVariables.php";
		$valor="";
		echo "<br>".$strParametro;
		//$strParametro=explode(" ",$strParametro);
		$parametrosCampo=explode(" ",$strParametro);
	/*	echo "<pre>";
		print_r($parametrosCampo);
		echo "</pre>";*/
		
		for($i=0;$i<count($parametrosCampo);$i++){
			
			if($parametrosCampo[$i]=="@USUARIOSISTEMA"){
				$valor=$this->idUsuario;
			}else{
				if(array_key_exists($parametrosCampo[$i], $configuracionParametros)){
					/*echo "<br>Posicion valor: ".$posicionValor=$configuracionParametros[$parametrosCampo[$i]];
					echo "<br>Indice valor: ".$indiceValor=array_search($posicionValor, $this->elementos);
					echo "<br>valor: ".$valor.=$this->valores[$indiceValor];*/
					$posicionValor=$configuracionParametros[$parametrosCampo[$i]];
					$indiceValor=array_search($posicionValor, $this->elementos);
					$valor.=$this->valores[$indiceValor];
					if($parametrosCampo[$i]=="@FECHAINICIAL" || $parametrosCampo[$i]=="@FECHAFINAL"){
						$valor.=" ";
					}
					if($parametrosCampo[$i]=="@HORAINICIAL" || $parametrosCampo[$i]=="@HORAFINAL"){
						$valor.=":";
					}
				}	
			}
			

		}
		
		/*echo "<pre>";
		print_r($configuracionParametros);
		echo "</pre>";*/
		if($valor=="S/N"){ $valor="0"; }
		return $valor;
   	}

   	public function construyeSQLReporte($parametros,$elementosAnalizar,$idCliente,$idUsuario){
   		include "configuracionVariables.php";
   			
		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
		//se separan los parametros de la opcion del reporte
		
		//echo 	$parametros.'#'.$elementosAnalizar;
		$parametros=explode("|||",$parametros);

		/*echo "<pre>";
		print_r($parametros);
		echo "</pre>";*/
		//se trabaja para el orden de los paramatros en el array
		
		$elementos = explode(",",$elementosAnalizar);
		$valores   = explode("||",$parametros[0]);
	    $this->elementos = $elementos;
        $this->valores=$valores;
		
/*		echo "<pre>";
		print_r($elementos);
		echo "</pre>";
				
		echo "<pre>";
		print_r($valores);
		echo "</pre>";*/
		
		
		
		$this->idCliente=$idCliente;
		$this->idUsuario=$idUsuario;

		/*$select="";
		$where=" WHERE ";
		$operadorTemporal="";*/
	
		$cadena = '';
		$arregloFinal = array();
		$arregloFinal = reconstruyeArreglo($this->elementos,$this->valores,$this->idCliente,$this->idUsuario);
		
	//	echo print_r($arregloFinal);
		//***************************************************************************************************************  se extrae el cuerpo SQL
		
		/* $sqlCuerpo="SELECT ID_SQL,SQLTEXTO,TIPO
		FROM ADM_REPORTES_OPCION_SQL INNER JOIN ADM_REPORTES_SQL ON ADM_REPORTES_OPCION_SQL.ID_REPORTES_SQL=ADM_REPORTES_SQL.ID_SQL
		WHERE ADM_REPORTES_OPCION_SQL.ID_REPORTES_OPCION='".$parametros[1]."'";*/
		
	  $sqlCuerpo="	SELECT  B.ID_SQL,
					B.SQLTEXTO,
					B.TIPO,
					C.CAMPO,
					C.OPERADOR,
					C.PARAMETRO,
					C.CONECTOR,
					C.ORDEN_CAMPO

		FROM  ADM_REPORTES_OPCION_SQL A
		INNER JOIN ADM_REPORTES_SQL B ON A.ID_REPORTES_SQL=B.ID_SQL 
		INNER JOIN ADM_REPORTES_SQL_WHERE C ON C.ID_SQL = B.ID_SQL 
		WHERE A.ID_REPORTES_OPCION='".$parametros[1]."'";
		
//****************************************************************************************************************************************
		$resCuerpo=$objDb->sqlQuery($sqlCuerpo);
		$objDb->sqlEnumRows($resCuerpo).'<br>';
		if($objDb->sqlEnumRows($resCuerpo)!=0){
			  while($row = $objDb->sqlFetchArray($resCuerpo)){
				  
			   
					$variableEnvio = $row['ID_SQL'];
					if(strtoupper($row['TIPO']) === 'Q'){//************************************************************************** tipo Q -> query
					   if($cadena===""){
						   $cadena = buscarCadena($arregloFinal,$row['SQLTEXTO'],'N').' WHERE '.str_replace("'"," ",buscarCadena($arregloFinal,$row['CAMPO'],$row['OPERADOR'])).' '.$row['OPERADOR'].' '.buscarCadena($arregloFinal,$row['PARAMETRO'],$row['OPERADOR']).' '.$row['CONECTOR'].' ';
					   
					   }else{
						   if($row['CONECTOR']!==NULL){
							   $cadena .= str_replace("'"," ",buscarCadena($arregloFinal,$row['CAMPO'],$row['OPERADOR'])).' '.$row['OPERADOR'].' '.buscarCadena($arregloFinal,$row['PARAMETRO'],$row['OPERADOR']).' '.$row['CONECTOR'].' '; 
						   }else{
							   $cadena .= str_replace("'"," ",buscarCadena($arregloFinal,$row['CAMPO'],$row['OPERADOR'])).' '.$row['OPERADOR'].' '.buscarCadena($arregloFinal,$row['PARAMETRO'],$row['OPERADOR']);   
						   }
					   }
					}else{  //******************************************************************************************************* tipo P -> procedimiento alcenado
						 $cadena = $row['SQLTEXTO'].'('.buscarCadena($arregloFinal,$row['PARAMETRO']).');';
					}
			  }
			
			
		}else{
			echo "No se puede procesar el reporte por falta de extraccion SQL.";
		}


	  $sqlCommand=trim($cadena);

		try{
			$x_res =  $this->guardaSqlGenerado($sqlCommand,$variableEnvio,$valores[count($valores)-1]);	
			
			if($x_res == 1){
				// funcion pinta;
			}else{
			   echo "fallo en el guardado de sql generado ".$x_res;	
			}
	    //		$gridDatos=new gridDatos();
		//   	$gridDatos->mostrarDatos($sqlCommand);
		}catch(Exception $e){
			echo $e->getMessage($sqlCommand);
		}
   	}


  public function guardaSqlGenerado($sql,$idSql,$limite){
	//  echo $sql.''.$variableEnvio.''.$limite;
	    $objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlWidgets='UPDATE ADM_REPORTES_SQL SET ULTIMO_SQL_GENERADO="'.$sql.'" WHERE ID_SQL = '.$idSql;
		$resWidgets=$objDb->sqlQuery($sqlWidgets);
		if($resWidgets){
			$respuesta = 1;
		}else{
		    $respuesta = $sqlWidgets;	
		}
	  return $respuesta;
  }

   	public function extraerWidgetsReporte($idReporte){
   		$widgets="";
   		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlWidgets="SELECT NOMBRE_PLANTILLA,CAMPOSVALORES,VALIDACION
		FROM ADM_REPORTES_OPCION_WIDGETS INNER JOIN ADM_REPORTES_WIDGETS ON ADM_REPORTES_OPCION_WIDGETS.ID_WIDGET=ADM_REPORTES_WIDGETS.ID_WIDGET
		WHERE ADM_REPORTES_OPCION_WIDGETS.ID_REPORTES_OPCION='".$idReporte."'";
		$resWidgets=$objDb->sqlQuery($sqlWidgets);
		if($objDb->sqlEnumRows($resWidgets)==0){
			$widgets="S/N";
		}else{
			while($rowWidgets=$objDb->sqlFetchArray($resWidgets)){
				($widgets=="") ? $widgets.=$rowWidgets["NOMBRE_PLANTILLA"]."||".$rowWidgets["CAMPOSVALORES"]."||".$rowWidgets["VALIDACION"] : $widgets.="|||".$rowWidgets["NOMBRE_PLANTILLA"]."||".$rowWidgets["CAMPOSVALORES"]."||".$rowWidgets["VALIDACION"];
			}
		}
		//echo $widgets;

		return $widgets;
   	}

   	public function extraerOpcionesReporte($idReporte){
   		$mensaje="";
      	$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
      	echo "<br>".$sqlObtenerDatosReporte="SELECT NOMBRE_PLANTILLA,QUERY_RESUMEN,CLAUSULA_WHERE_RESUMEN,QUERY_DETALLE,CLAUSULA_WHERE_DETALLE
		FROM (ADM_REPORTES_OPCION INNER JOIN ADM_REPORTES_OPCION_WIDGETS ON ADM_REPORTES_OPCION.ID_REPORTES_OPCION=ADM_REPORTES_OPCION_WIDGETS.ID_REPORTES_OPCION) INNER JOIN ADM_REPORTES_WIDGETS ON ADM_REPORTES_OPCION_WIDGETS.ID_WIDGET=ADM_REPORTES_WIDGETS.ID_WIDGET
		WHERE ADM_REPORTES_OPCION.ID_REPORTES_OPCION='".$idReporte."'";
      	$resObtenerDatosReporte=$objDb->sqlQuery($sqlObtenerDatosReporte);
      	if($objDb->sqlEnumRows($resObtenerDatosReporte)==0){
      		$mensaje="S/N";
      	}else{
      		while($rowObtenerDatosReporte=$objDb->sqlFetchArray($resObtenerDatosReporte)){
      			($mensaje=="") ? $mensaje.=$rowObtenerDatosReporte['NOMBRE_PLANTILLA'] : $mensaje.=",".$rowObtenerDatosReporte['NOMBRE_PLANTILLA'];
      		}
      		echo "<br>".$mensaje;
      	}
      	return $mensaje;
   	}

   	public function extrerMenuUsuario($idUsuario){
		$mensaje="";
      	$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
		$sqlReportesMenu = "SELECT ID_REPORTES_MENU,NOMBRE_REPORTES_MENU,NOMBRE,ADM_REPORTES_OPCION.ID_REPORTES_OPCION AS ID_REPORTES_OPCION
		FROM (ADM_REPORTES_USUARIO INNER JOIN ADM_REPORTES_OPCION ON ADM_REPORTES_USUARIO.ID_REPORTES_OPCION=ADM_REPORTES_OPCION.ID_REPORTES_OPCION) INNER JOIN ADM_REPORTES_MENU ON ADM_REPORTES_MENU.ID_REPORTES_MENU=ADM_REPORTES_OPCION.ID_ADM_REPORTES_MENU
		WHERE ADM_REPORTES_USUARIO.ID_USUARIO='".$idUsuario."'";
		$resReportesMenu = $objDb->sqlQuery($sqlReportesMenu);
		if($objDb->sqlEnumRows($resReportesMenu)==0){
			echo "S/N";
		}else{
			while($rowReportesMenu=$objDb->sqlFetchArray($resReportesMenu)){//se recorre por los valores devueltos
				if($mensaje==""){
					$mensaje=$rowReportesMenu["ID_REPORTES_MENU"]."|".$rowReportesMenu["NOMBRE_REPORTES_MENU"]."|".$rowReportesMenu["NOMBRE"]."|".$rowReportesMenu["ID_REPORTES_OPCION"];
				}else{
					$mensaje.="||".$rowReportesMenu["ID_REPORTES_MENU"]."|".$rowReportesMenu["NOMBRE_REPORTES_MENU"]."|".$rowReportesMenu["NOMBRE"]."|".$rowReportesMenu["ID_REPORTES_OPCION"];
				}
			}
		}
		return $mensaje;
   	}
	

}//fin de la clase
?>