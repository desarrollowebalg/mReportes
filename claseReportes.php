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

   	private function ejecutaOperacion($sqlReporte,$pag,$tipoSQL){
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


   	}

   	private function evaluaParametro($strParametro){
		include "configuracionVariables.php";
		$valor="";
		//echo "<br>".$strParametro;
		//$strParametro=explode(" ",$strParametro);
		$parametrosCampo=explode(" ",$strParametro);
		/*echo "<pre>";
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
   		
   		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
		//se separan los parametros de la opcion del reporte
		$parametros=explode("|||",$parametros);

		/*echo "<pre>";
		print_r($parametros);
		echo "</pre>";*/
		//se trabaja para el orden de los paramatros en el array
		$elementos=explode(",",$elementosAnalizar);
		$valores=explode("||",$parametros[0]);
		$this->elementos=$elementos;
		/*echo "<pre>";
		print_r($elementos);
		echo "</pre>";*/
		$this->valores=$valores;
		/*echo "<pre>";
		print_r($valores);
		echo "</pre>";*/
		
		$this->idCliente=$idCliente;
		$this->idUsuario=$idUsuario;

		$select="";
		$where=" WHERE ";
		$operadorTemporal="";
		//se extrae el cuerpo SQL
		$sqlCuerpo="SELECT ID_SQL,SQLTEXTO,TIPO
		FROM ADM_REPORTES_OPCION_SQL INNER JOIN ADM_REPORTES_SQL ON ADM_REPORTES_OPCION_SQL.ID_REPORTES_SQL=ADM_REPORTES_SQL.ID_SQL
		WHERE ADM_REPORTES_OPCION_SQL.ID_REPORTES_OPCION='".$parametros[1]."'";
		
		$resCuerpo=$objDb->sqlQuery($sqlCuerpo);
		if($objDb->sqlEnumRows($resCuerpo)!=0){
			$rowCuerpo=$objDb->sqlFetchArray($resCuerpo);
			
				$select=$rowCuerpo["SQLTEXTO"];
				$tipoQuery=$rowCuerpo["TIPO"];
				//se procede a extraer el where
				$sqlWhere="SELECT * FROM ADM_REPORTES_SQL_WHERE WHERE ID_SQL='".$rowCuerpo["ID_SQL"]."'";
				$resWhere=$objDb->sqlQuery($sqlWhere);
				//$i=0;
				if($objDb->sqlEnumRows($resWhere) != 0){
					
					if($tipoQuery=="Q"){

						while($rowWhere=$objDb->sqlFetchArray($resWhere)){
							if($operadorTemporal==""){	
								if($rowWhere["OPERADOR"]=="IN"){
									//$where.=" (".$rowWhere["PARAMETRO"].")";
									$where.=" ".$rowWhere["CAMPO"]." ".$rowWhere["OPERADOR"]." (".$this->evaluaParametro($rowWhere["PARAMETRO"]).")";
								}else{
									$valor="";
									//$where .= $rowWhere["CAMPO"]." ".$rowWhere["OPERADOR"]." '".$rowWhere["PARAMETRO"]."'";
									$where .= $rowWhere["CAMPO"]." ".$rowWhere["OPERADOR"]." '".$this->evaluaParametro($rowWhere["PARAMETRO"])."'";	
								}
								$operadorTemporal=$rowWhere["OPERADOR"];
							}else{
								if($operadorTemporal=="BETWEEN"){
									//$where.=" '".$rowWhere["PARAMETRO"]."'";
									$where.=" '".$this->evaluaParametro($rowWhere["PARAMETRO"])."'";
								}
								$operadorTemporal="";
							}

							if($rowWhere["CONECTOR"]!=""){
								$where.=" ".$rowWhere["CONECTOR"]." ";
							}
							//echo "<br />contador ".$i." ".$where;
							//$i+=1;
						}
						$sqlGR=$select.$where;
					}elseif($tipoQuery=="P"){
						$where = "";
						//echo "Armar el procedimiento<br><br>";
						while($rowWhereP=$objDb->sqlFetchArray($resWhere)){
							($where=="") ? $where.="'".$this->evaluaParametro($rowWhereP["PARAMETRO"])."'" : $where.=","."'".$this->evaluaParametro($rowWhereP["PARAMETRO"])."'";
						}
						//$rowWhereP=$objDb->sqlFetchArray($resWhere);
						//echo "<br>".$where."<br><br>";
						$sqlGR="CALL ".$select."(".$where.")";
						//exit();
					}


				}else{
					echo "Error al construir la Consulta SQL";	
				}
			
		}else{
			echo "No se puede procesar el reporte por falta de extraccion SQL.";
		}
		//echo "<br />".
		$sqlCommand=trim($sqlGR);
		
		//echo "1.- ".$sqlCommand;
		try{
			$gridDatos=new gridDatos();
			$gridDatos->mostrarDatos($sqlCommand);
		}catch(Exception $e){
			echo $e->getMessage($sqlCommand);
		}
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