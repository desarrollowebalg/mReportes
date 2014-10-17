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
	include "claseReportes.php";
	include "claseWidgets.php";
	$objR=new reportes();
	$objW=new widgets();
	switch($_POST["action"]){
		case "mostrarNuevoMenu":
			$menuR=$objR->extarerMenuUsuario($_POST["idUsuario"]);
			if($menuR=="S/N"){
				echo "No hay Menú especificado";
			}else{
				$menuR=explode("||",$menuR);//se separa los registros
				$temp="";
				$menuUR="";
				$bandera=false;
				$tpl->set_filenames(array('controlador' => 'tNuevoMenu'));
				for($i=0;$i<count($menuR);$i++){
					$registro=explode("|",$menuR[$i]);
					if($registro[1]!=$temp){
						if($bandera==true){
							$menuUR.="</div>";
							$bandera=false;
						}
						$menuUR.="<h3><span style='margin-left:15px;'>".$registro[1]."</span></h3><div><p><a href='#' onclick='cargaElementosReporte(\"".$registro[3]."\")'>".$registro[2]."</a></p>";
						$bandera=true;
					}else{
						$menuUR.="<p><a href='#' onclick='cargaElementosReporte(\"".$registro[3]."\")'>".$registro[2]."</a></p>";
					}					
					$temp=$registro[1];
				}
				$menuUR.="</div>";
				
				$tpl->assign_vars(array(
					'MENUR'	=> $menuUR
				));
				$tpl->pparse('controlador');
			}
		break;
		case "mostrarPlantillaReporte":
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			$tpl->set_filenames(array('controlador' => 'tPlantillaReporte'));
			
			$widgets=$objR->extraerWidgetsReporte($_POST["idReporte"]);

			$strWidgets=$objW->obtenerWidget($widgets,$_POST["idCliente"],$_POST["idUsuario"]);
			
			$tpl->assign_vars(array(
				'WIDGETS'	=> $strWidgets
			));
			

			$tpl->pparse('controlador');
		break;
	}
}
?>