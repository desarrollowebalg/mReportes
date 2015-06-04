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
	   			case "tWidgetUsuariosEvidencias":
	   				$strWidget.=$this->widgetUsuariosEvidencias($idCliente,$idUsuario);
	   			break;
	   			case "tWidgetPdi":
	   				$strWidget.=$this->widgetPDI($idCliente,$idUsuario);
	   			break;
	   			case "tWidgetGeocerca":
					$strWidget.=$this->widgetGeocercas($idCliente,$idUsuario);
	   			break;
	   			case "tWidgetRecursoHumano":
	   				$strWidget.=$this->widgetRecursoHumano($idCliente,$idUsuario);
	   			break;
	   			case "tWidgetCuestionarios":
	   				$strWidget.=$this->widgetCuestionarios($idCliente,$idUsuario);
	   			break;
				case "tWidgetSoloFecha":
	   				$strWidget.=$this->widgetSoloFecha();
	   			break;
				case "tWidgetEquipos":
	   				$strWidget.=$this->widgetEquipos();
	   			break;
				

	   		}
		}
   		//echo htmlentities($strWidget);
   		return $strWidget;
   	}
   	/*
   	*
   	*/
   	public function widgetCuestionarios($idCliente,$idUsuario){
   		$mensaje="";
   		$objDb=$this->iniciarConexionDb();
   		$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlCRM="SELECT ID_CUESTIONARIO,DESCRIPCION FROM ALG_BD_CORPORATE_MOVI.CRM2_CUESTIONARIOS WHERE COD_CLIENT='".$idCliente."' AND ACTIVO='S'";
   		$res=$objDb->sqlQuery($sqlCRM);
   		if($objDb->sqlEnumRows($res)!=0){
   			$widgetCRM="<div class='contenedorWidgetUsuarios ui-corner-all'>
						  <div class='tituloWidgetUsuarios ui-state-default'>Selección de Recurso Humano:</div>
						  <!--<p><input type='checkbox' id='widgetChkHabilitaUsuarios'><label for='widgetChkHabilitaUsuarios'>Seleccionar usuario</label></p>-->
						  <div style='margin-left:5px;margin-top:10px;'>
							<select name='cboWidgetCuestionariosCliente' id='cboWidgetCuestionariosCliente' style='width:220px;'>
								<option value='S/N' selected='selected'>Selecciona...</option>";
			while($rowPdi=$objDb->sqlFetchArray($res)){
				$widgetCRM.="<option value='".$rowPdi["ID_CUESTIONARIO"]."'>".$rowPdi["DESCRIPCION"]."</option>";
			}
			$widgetCRM.="</select>
						</div>
						</div>";				
   		}else{
   			$widgetCRM="<br>No hay Recursos Humanos asociados asociados con el cliente";
   		}
   		return $widgetCRM;
   	}
	/*
   	*
   	*/
   	public function widgetRecursoHumano($idCliente,$idUsuario){
   		$mensaje="";
   		$objDb=$this->iniciarConexionDb();
   		$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlRH="SELECT ID_OBJECT_MAP,DESCRIPCION FROM ALG_BD_CORPORATE_MOVI.ADM_GEOREFERENCIAS WHERE TIPO='RH' AND ID_CLIENTE='".$idCliente."';";
   		$res=$objDb->sqlQuery($sqlRH);
   		if($objDb->sqlEnumRows($res)!=0){
   			$widgetRH="<div class='contenedorWidgetUsuarios ui-corner-all'>
						  <div class='tituloWidgetUsuarios ui-state-default'>Selección de Recurso Humano:</div>
						  <!--<p><input type='checkbox' id='widgetChkHabilitaUsuarios'><label for='widgetChkHabilitaUsuarios'>Seleccionar usuario</label></p>-->
						  <div style='margin-left:5px;margin-top:10px;'>
							<select name='cboWidgetRecursoHumanoCliente' id='cboWidgetRecursoHumanoCliente' style='width:220px;'>
								<option value='S/N' selected='selected'>Selecciona...</option>";
			while($rowPdi=$objDb->sqlFetchArray($res)){
				$widgetRH.="<option value='".$rowPdi["ID_OBJECT_MAP"]."'>".$rowPdi["DESCRIPCION"]."</option>";
			}
			$widgetRH.="</select>
						</div>
						</div>";				
   		}else{
   			$widgetRH="<br>No hay Recursos Humanos asociados asociados con el cliente";
   		}
   		return $widgetRH;
   	}
   	/*
   	*
   	*/
   	public function widgetGeocercas($idCliente,$idUsuario){
		$mensaje="";
   		$objDb=$this->iniciarConexionDb();
   		$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlGEO="SELECT ID_OBJECT_MAP,DESCRIPCION FROM ALG_BD_CORPORATE_MOVI.ADM_GEOREFERENCIAS WHERE TIPO='C' AND ID_CLIENTE='".$idCliente."';";
   		$res=$objDb->sqlQuery($sqlGEO);
   		if($objDb->sqlEnumRows($res)!=0){
   			$widgetGeo="<div class='contenedorWidgetUsuarios ui-corner-all'>
						  <div class='tituloWidgetUsuarios ui-state-default'>Selección de Geocerca:</div>
						  <!--<p><input type='checkbox' id='widgetChkHabilitaUsuarios'><label for='widgetChkHabilitaUsuarios'>Seleccionar usuario</label></p>-->
						  <div style='margin-left:5px;margin-top:10px;'>
							<select name='cboWidgetGeocercaCliente' id='cboWidgetGeocercaCliente' style='width:220px;'>
								<option value='S/N' selected='selected'>Selecciona...</option>";
			while($rowPdi=$objDb->sqlFetchArray($res)){
				$widgetGeo.="<option value='".$rowPdi["ID_OBJECT_MAP"]."'>".$rowPdi["DESCRIPCION"]."</option>";
			}
			$widgetGeo.="</select>
						</div>
						</div>";				
   		}else{
   			$widgetGeo="<br>No hay Geocercas asociadas con el cliente";
   		}
   		return $widgetGeo;
   	}
   	/*
   	*
   	*/
   	public function widgetPDI($idCliente,$idUsuario){
   		$mensaje="";
   		$objDb=$this->iniciarConexionDb();
   		$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlPdi="SELECT ID_OBJECT_MAP,DESCRIPCION FROM ALG_BD_CORPORATE_MOVI.ADM_GEOREFERENCIAS WHERE TIPO='G' AND ID_CLIENTE='".$idCliente."';";
   		$res=$objDb->sqlQuery($sqlPdi);
   		if($objDb->sqlEnumRows($res)!=0){
   			$widgetPdi="<div class='contenedorWidgetUsuarios ui-corner-all'>
						  <div class='tituloWidgetUsuarios ui-state-default'>Selección de Geopunto:</div>
						  <!--<p><input type='checkbox' id='widgetChkHabilitaUsuarios'><label for='widgetChkHabilitaUsuarios'>Seleccionar usuario</label></p>-->
						  <div style='margin-left:5px;margin-top:10px;'>
							<select name='cboWidgetPDICliente' id='cboWidgetPDICliente' style='width:220px;'>
								<option value='S/N' selected='selected'>Selecciona...</option>";
			while($rowPdi=$objDb->sqlFetchArray($res)){
				$widgetPdi.="<option value='".$rowPdi["ID_OBJECT_MAP"]."'>".$rowPdi["DESCRIPCION"]."</option>";
			}
			$widgetPdi.="</select>
						</div>
						</div>";				
   		}else{
   			$widgetPdi="<br>No hay Geopuntos asociados con el cliente";
   		}
   		return $widgetPdi;
   	}
   	/*
   	*
   	*/
   	public function widgetUsuariosEvidencias($idCliente,$idUsuario){
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
					<select name='cboWidgetUsuariosEvidencias' id='cboWidgetUsuariosEvidencias'>
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

    /*
	   SOLO FECHA
	*/

   	private function widgetSoloFecha(){
   		
		$widgetFecha="<div class='contenedorWidgetFecha ui-corner-all'>
			<div class='tituloWidgetFecha ui-state-default'>Selección de fecha:</div>
  			  <div class='fechaHoraCampo'>
			    <label for='fechaInicial'>Fecha</label><input id='widgetFechaInicial' type='text' readonly='readonly'>&nbsp;&nbsp;
			  </div>
			</div>";
		return $widgetFecha;
   	}
	
	/* widget tipo de equipo*/
	
   	public function widgetEquipos(){
   		$mensaje="";
   		$objDb=$this->iniciarConexionDb();
   		$objDb->sqlQuery("SET NAMES 'utf8'");
   		$sqlCRM="SELECT COD_TYPE_EQUIPMENT,DESCRIPTION FROM ADM_EQUIPOS_TIPO ORDER BY DESCRIPTION";
   		$res=$objDb->sqlQuery($sqlCRM);
   		if($objDb->sqlEnumRows($res)!=0){
   			$widgetCRM="<div class='contenedorWidgetUsuarios ui-corner-all'>
						  <div class='tituloTipoEquipo ui-state-default'>Selección de Equipo:</div>
						  <div style='margin-left:5px;margin-top:10px;'>
							<select name='cboWidgetEquipo' id='cboWidgetEquipo' style='width:220px;'>
								<option value='S/N' selected='selected'>Selecciona...</option>";
			while($rowPdi=$objDb->sqlFetchArray($res)){
				$widgetCRM.="<option value='".$rowPdi["COD_TYPE_EQUIPMENT"]."'>".$rowPdi["DESCRIPTION"]."</option>";
			}
			$widgetCRM.="</select>
						</div>
						</div>";				
   		}else{
   			$widgetCRM="<br>No hay equipos";
   		}
   		return $widgetCRM;
   	}

}