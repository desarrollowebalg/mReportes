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
	private $host;
	private $port;
	private $bname;
	private $user;
	private $pass;
	private $elementos 	=	array();
	private $valores	=	array();

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
   		$objBdG=mysql_connect($this->host,$this->user,$this->pass);
   		if($objBdG){
   			@mysql_select_db($this->bname)or die("Error al conectar con la base de datos");
   		}
   		return $objBdG;
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
		
		/*echo "<pre>";
		print_r($configuracionParametros);
		echo "</pre>";*/
		return $valor;
   	}

   	public function construyeSQLReporte($parametros,$elementosAnalizar){
   		
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
			//se procede a extraer el where
			$sqlWhere="SELECT * FROM ADM_REPORTES_SQL_WHERE WHERE ID_SQL='".$rowCuerpo["ID_SQL"]."'";
			$resWhere=$objDb->sqlQuery($sqlWhere);
			//$i=0;

			if($objDb->sqlEnumRows($resWhere) != 0){
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
			}else{
				echo "Error al construir la Consulta SQL";	
			}
		}else{
			echo "No se puede procesar el reporte por falta de extraccion SQL.";
		}
		//echo "<br />".
		$sqlCommand=trim($select.$where);


		$conn=$this->iniciarConexionDbGrid();//conexion hacia la base de datos
		mysql_query("SET NAMES 'utf8'",$conn);// set your db encoding -- for ascent chars (if required)
		include "public/libs/phpgridv1.5.2/lib/inc/jqgrid_dist.php";

		$g = new jqgrid();//se instancia el objeto
		// parametros de configuracion
		//$grid["caption"] = "Alertas";
		$grid["multiselect"] 	= false;
		$grid["autowidth"] 		= true; // expand grid to screen width
		//$grid["resizable"] 		= true;
		//$grid["altRows"] 		= true;
		//$grid["altclass"] 		="alternarRegistros";
		$grid["scroll"] 		= false;
		$grid["sortorder"]		="desc";
		//$grid["rowNum"] 		= 10; // by default 20 
		$g->set_options($grid);
		$g->set_actions(array(  
                        "add"=>false,
                        "edit"=>false,
                        "delete"=>false,
                        "view"=>false,
                        "rowactions"=>false,
                        "export"=>false,
                        "autofilter" => true,
                        "search" => "advance",
                        "inlineadd" => false,
                        "showhidecolumns" => true
                    )
                );
		//echo $sqlCommand;
		$g->select_command = $sqlCommand;// comando SQL
		$g->table = "HIST00000";// set database table for CRUD operations
		//$g->set_columns($cols);
		
		//$g->select_command = "SELECT * FROM HIST00001 WHERE GPS_DATETIME BETWEEN '2014-01-01 00:00' AND '2014-10-22 23:59' AND COD_ENTITY IN (26,27,112,127) ";// comando SQL
		$out = $g->render("reportesX");// render grid
		echo $out;
   	}

   	public function extraerWidgetsReporte($idReporte){
   		$widgets="";
   		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlWidgets="SELECT NOMBRE_PLANTILLA,CAMPOSVALORES
		FROM ADM_REPORTES_OPCION_WIDGETS INNER JOIN ADM_REPORTES_WIDGETS ON ADM_REPORTES_OPCION_WIDGETS.ID_WIDGET=ADM_REPORTES_WIDGETS.ID_WIDGET
		WHERE ADM_REPORTES_OPCION_WIDGETS.ID_REPORTES_OPCION='".$idReporte."'";
		$resWidgets=$objDb->sqlQuery($sqlWidgets);
		if($objDb->sqlEnumRows($resWidgets)==0){
			$widgets="S/N";
		}else{
			while($rowWidgets=$objDb->sqlFetchArray($resWidgets)){
				($widgets=="") ? $widgets.=$rowWidgets["NOMBRE_PLANTILLA"]."||".$rowWidgets["CAMPOSVALORES"] : $widgets.="|||".$rowWidgets["NOMBRE_PLANTILLA"]."||".$rowWidgets["CAMPOSVALORES"];
			}
		}
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