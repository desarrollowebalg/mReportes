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
	private $widgetFecha="";
	private $widgetGruposUsuarios="";
	public $strWidget="";

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

   	public function obtenerWidget($widgets,$idCliente,$idUsuario){
   		$strWidget="";
   		$widgets=explode(",",$widgets);
		/*
		echo "<pre>";
		print_r($widgets);
		echo "</pre>";
		*/
		for($i=0;$i<count($widgets);$i++){
			switch($widgets[$i]){
	   			case "tWidgetFecha":
	   				$strWidget.=$this->widgetFechaHora();
	   			break;
	   			case "tWidgetGruposUnidades":
	   				$strWidget.=$this->widgetGruposUsuarios($idCliente,$idUsuario);
	   			break;
	   			case "tWidgetUsuarios":
	   				$strWidget.=$this->widgetUsuarios($idCliente,$idUsuario);
	   			break;
	   		}
		}
   		//echo htmlentities($strWidget);
   		return $strWidget;
   	}
   	/*
   	*
   	*/
   	public function widgetUsuarios($idCliente,$idUsuario){
   		$mensaje="";
      	$objDb=$this->iniciarConexionDb();
      	$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlUsuarios="SELECT ID_USUARIO,NOMBRE_COMPLETO FROM ADM_USUARIOS WHERE ID_CLIENTE='".$idCliente."'";
   		$resUsuarios=$objDb->sqlQuery($sqlUsuarios);
   		if($objDb->sqlEnumRows($resUsuarios)!=0){
   			while($rowUsuarios=$objDb->sqlFetchArray($resUsuarios)){
	   			if($mensaje==""){
	   				$mensaje=$rowUsuarios["ID_USUARIO"]."|".$rowUsuarios["NOMBRE_COMPLETO"];
	   			}else{
	   				$mensaje.="||".$rowUsuarios["ID_USUARIO"]."|".$rowUsuarios["NOMBRE_COMPLETO"];
	   			}	
   			}
   		}else{
   			$mensaje=0;
   		}
   		if($mensaje!=0){
			$usuarios=explode("||",$mensaje);
	   		$option="";
	   		for($i=0;$i<count($usuarios);$i++){
				$listadoUsr=explode("|", $usuarios[$i]);
				$option.="<option value='".$listadoUsr[0]."'>".$listadoUsr[1]."</option>";
			}
			$widgetUsuarios="<div class='contenedorWidgetUsuarios ui-corner-all'>
				<div class='tituloWidgetUsuarios ui-state-default'>Selección de Usuarios:</div>
				<p><input type='checkbox' id='widgetChkHabilitaUsuarios'><label for='widgetChkHabilitaUsuarios'>Seleccionar usuario</label></p>
				<div style='margin-left:5px;margin-top:10px;'>
					<select name='cboWidgetUsuarios' id='cboWidgetUsuarios'>
						<option value='S/N' selected='selected'>Selecciona...</option>
					".$option."
					</select>
				</div>
			</div>";
   		}else{
   			$widgetUsuarios="Widget no definido";
   		}
   		
		return $widgetUsuarios;
   	}	
   	/*
   	*Widget Grupos/Usuarios
   	*/
   	private function widgetGruposUsuarios($idCliente,$idUsuario){
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
		}
		$widgetGruposUsuarios="<div class='contenedorWidgetGruposUnidades ui-corner-all'>
			<div class='tituloWidgetGruposUnidades ui-state-default'>Selección de Unidades:</div>
			<p><input type='checkbox' id='widgetChkTodasGruposUnidades'><label for='widgetChkTodasGruposUnidades'>Seleccionar todas</label></p>
			<div style='margin-left:5px;'>
				<select name='cboWidgetGruposUnidades' id='cboWidgetGruposUnidades' multiple='multiple'>
				".$mensaje."
				</select>
			</div>
		</div>";
		return $widgetGruposUsuarios;
   	}
   	/*
   	*Widget Fecha/Hora
   	*/
   	private function widgetFechaHora(){
   		$horas="";
   		$minutos="";
   		for($i=0;$i<24;$i++){
			($i<10)? $horas.="<option value='0".$i."'>0".$i."</value>" : $horas.="<option value='".$i."'>".$i."</value>";
		}
		for($i=0;$i<60;$i++){
			($i<10)? $minutos.="<option value='0".$i."'>0".$i."</option>" : $minutos.="<option value='".$i."'>".$i."</option>";
		}
		$widgetFecha="<div class='contenedorWidgetFecha ui-corner-all'>
				<div class='tituloWidgetFecha ui-state-default'>Selección de fechas:</div>
				<div class='fechaHoraCampo'>
			    <label for='fechaInicial'>Fecha Inicial:</label><input id='widgetFechaInicial' type='text' readonly='readonly'>&nbsp;&nbsp;
			    <select name='widgetCboHoraInicial_1' id='widgetCboHoraInicial_1'>
			      <option value='00' selected='selected'>00</option>
			      ".$horas."
			    </select>
			    <select name='widgetCboHoraFinalFin_1' id='widgetCboHoraFinal_1'>
			      <option value='00' selected='selected'>00</option>
			      ".$minutos."
			    </select>
			  </div>
				<div class='fechaHoraCampo'>
			    <label for='fechaFinal'>Fecha Final:&nbsp;&nbsp;</label><input id='widgetFechaFinal' type='text' readonly='readonly'>&nbsp;&nbsp;
			    <select name='widgetCboHoraInicial_2' id='widgetCboHoraInicial_2'>
			      <option value='23' selected='selected'>23</option>
			      ".$horas."
			    </select>
			    <select name='widgetCboHoraFinal_2' id='widgetCboHoraFinal_2'>
			      <option value='59' selected='selected'>59</option>
			      ".$minutos."
			    </select>
			  </div>
			</div>";
		return $widgetFecha;
   	}

}