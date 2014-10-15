<?php
/**
*@name 				Controlador para las funciones de REPORTES
*@copyright         Air Logistics & GPS S.A. de C.V.  
*@author 			Gerardo Lara
*@version 			1
*@fecha 			14 Octubre 2014
*/

if($_SERVER["HTTP_REFERER"]==""){
	echo "0";
}else{
	switch($_POST["action"]){
		case "cargarWidget":
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			switch($_POST["widget"]){
				case "fechaHora":
					$tpl->set_filenames(array('controlador' => 'tWidgetFecha'));
				break;
				case "gruposUnidades":
					$idCliente=$_POST["idCliente"];
					$idUsuario=$_POST["idUsuario"];
					$tpl->set_filenames(array('controlador' => 'tWidgetGruposUnidades'));
				break;
			}
			$tpl->pparse('controlador');
		break;
	}
}
?>