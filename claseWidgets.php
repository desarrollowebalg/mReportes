<?php
/**
*@description       Clase para el manejo de los diferentes reportes en la plataforma
*@copyright         Air Logistics & GPS S.A. de C.V.  
*@author 			Gerardo Lara
*@version 			1.0.0
*@fecha 			14 Octubre 2014
*/

class widgets{
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

   	public function regresaGruposCliente($idCliente,$idUsuario){
   		$mensaje="";
      	$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlGrupos="SELECT ADM_GRUPOS.ID_GRUPO, ADM_GRUPOS.NOMBRE, ADM_USUARIOS_GRUPOS.COD_ENTITY,ADM_UNIDADES.DESCRIPTION
				FROM (ADM_USUARIOS_GRUPOS INNER JOIN ADM_GRUPOS ON ADM_GRUPOS.ID_GRUPO = ADM_USUARIOS_GRUPOS.ID_GRUPO)INNER JOIN ADM_UNIDADES ON ADM_USUARIOS_GRUPOS.COD_ENTITY=ADM_UNIDADES.COD_ENTITY
				WHERE ADM_USUARIOS_GRUPOS.ID_USUARIO = '".$idUsuario."' ORDER BY NOMBRE,COD_ENTITY";
		$resGrupos=$objDb->sqlQuery($sqlGrupos);
		if($objDb->sqlEnumRows($resGrupos)==0){
			$mensaje="S/N";
		}else{
			$nombreGrupo=""; $banderaGrupo=false;
			while($rowGrupos=$objDb->sqlFetchArray($resGrupos)){
				if($rowGrupos["NOMBRE"] != $nombreGrupo){//se crea el grupo
					if($banderaGrupo==true){
						$mensaje.="</optgroup>";
						$banderaGrupo=false;
					}
					$mensaje.="<optgroup label='".$rowGrupos["NOMBRE"]."'><option value='".$rowGrupos["COD_ENTITY"]."'>".$rowGrupos["DESCRIPTION"]."</option>";
					$banderaGrupo=true;
					$nombreGrupo=$rowGrupos["NOMBRE"];
				}else{
					$mensaje.="<option value='".$rowGrupos["COD_ENTITY"]."'>".$rowGrupos["DESCRIPTION"]."</option>";
				}
			}
			$mensaje.="</optgroup>";
			//echo htmlentities($mensaje);
			return $mensaje;
		}
   	}
}