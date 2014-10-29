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
	include "claseWidgets.php";
	$objW=new widgets();
	switch($_POST["action"]){
		case "cargarWidget":
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			switch($_POST["widget"]){
				case "fechaHora":
					$tpl->set_filenames(array('controlador' => 'tWidgetFecha'));
					for($i=0;$i<24;$i++){
						($i<10)? $horas=	"0".$i : $horas= $i;
						$tpl->assign_block_vars('listadoHoras',array(
				            'HORAS' =>	$horas
				        ));
					}
					for($i=0;$i<60;$i++){
						($i<10)? $minutos="0".$i: $minutos= $i;
						$tpl->assign_block_vars('listadoMinutos',array(
			            	'MINUTOS' =>	$minutos
			        	));
					}
					$tpl->pparse('controlador');
				break;
				case "gruposUnidades":
					$idCliente=$_POST["idCliente"];
					$idUsuario=$_POST["idUsuario"];
					$gruposUsuario=$objW->regresaGruposCliente($idCliente,$idUsuario);

					$tpl->set_filenames(array('controlador' => 'tWidgetGruposUnidades'));
					$tpl->assign_vars(array(
						'GRUPOSUSUARIO'  => $gruposUsuario
					));					
					$tpl->pparse('controlador');
				break;
				case "usuarios":
					$idCliente=$_POST["idCliente"];
					$idUsuario=$_POST["idUsuario"];

					$usuarios=$objW->widgetUsuarios($idCliente,$idUsuario);
					$tpl->set_filenames(array('controladorWidgets' => 'tWidgetUsuarios'));
					$tpl->assign_vars(array(
						'USUARIOS'  => $usuarios
					));
					$tpl->pparse('controladorWidgets');
				break;
			}
			
		break;
	}
}
?>