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

   	public function regresaGruposCliente($idCliente){
   		$mensaje="";
      	$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlGrupos="SELECT ADM_GRUPOS.ID_GRUPO AS ID_GRUPO,NOMBRE
		FROM ADM_GRUPOS INNER JOIN ADM_GRUPOS_CLIENTES ON ADM_GRUPOS.ID_GRUPO=ADM_GRUPOS_CLIENTES.ID_GRUPO
		WHERE ADM_GRUPOS_CLIENTES.ID_CLIENTE='".$idCliente."'";
		$resGrupos=$objDb->sqlQuery($sqlGrupos);
		if($objDb->sqlEnumRows($resGrupos)==0){
			$mensaje="S/N";
		}else{
			while($rowGrupos=$objDb->sqlFetchArray($resGrupos)){
				$mensaje=$rowGrupos["ID_GRUPO"]."|".$rowGrupos["NOMBRE"];
			}
		}
   	}

}