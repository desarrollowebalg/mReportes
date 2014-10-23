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
					echo $usuarios=$objW->extraerUsuarios($idCliente,$idUsuario);
					$usuarios=explode("||",$usuarios);
					echo "<pre>";
					print_r($usuarios);
					echo "</pre>";
					
					$tpl->set_filenames(array('controlador' => 'tWidgetUsuarios'));
					for($i=0;$i<count($usuarios);$i++){
						$listadoUsr=explode("|", $usuarios[$i]);
						$option="<option value='".$listadoUsr[0]."'>".$listadoUsr[1]."</option>";
						$tpl->assign_block_vars('listadoUsuarios',array(
			            	'USUARIO' =>	$option
			        	));
					}
					

					$tpl->pparse('controlador');
				break;
			}
			
		break;
	}
}
?>