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
	private $host;
	private $port;
	private $bname;
	private $user;
	private $pass;

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

   	public function extraerWidgetsReporte($idReporte){
   		$widgets="";
   		$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlWidgets="SELECT NOMBRE_PLANTILLA
		FROM ADM_REPORTES_OPCION_WIDGETS INNER JOIN ADM_REPORTES_WIDGETS ON ADM_REPORTES_OPCION_WIDGETS.ID_WIDGET=ADM_REPORTES_WIDGETS.ID_WIDGET
		WHERE ADM_REPORTES_OPCION_WIDGETS.ID_REPORTES_OPCION='".$idReporte."'";
		$resWidgets=$objDb->sqlQuery($sqlWidgets);
		if($objDb->sqlEnumRows($resWidgets)==0){
			$widgets="S/N";
		}else{
			while($rowWidgets=$objDb->sqlFetchArray($resWidgets)){
				($widgets=="") ? $widgets.=$rowWidgets["NOMBRE_PLANTILLA"] : $widgets.=",".$rowWidgets["NOMBRE_PLANTILLA"];
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