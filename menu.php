<?php
/** * 
 *  @package             
 *  @name                Pagina default del modulo silver 
 *  @version             1
 *  @copyright           Air Logistics & GPS S.A. de C.V.   
 *  @author              Rodwyn Moreno
 *  @modificado          23-04-2012
**/
	$db = new sql($config_bd['host'],$config_bd['port'],$config_bd['bname'],$config_bd['user'],$config_bd['pass']);
	$userAdmin->u_logged();
	/*echo "<pre>";
	print_r($userAdmin);
	echo "</pre>";*/
	$id_profile = $userAdmin->user_info['ID_PERFIL'];
	$id_cliente = $userAdmin->user_info['ID_CLIENTE'];
	$id_usuario = $userAdmin->user_info['ID_USUARIO'];
	
	$tpl->set_filenames(array('menu'=>'menu'));		
	
	$sql ="SELECT ADM_SUBMENU.ID_SUBMENU,DESCRIPTION,UBICACION
			FROM ADM_PERFIL_PERMISOS
			INNER JOIN ADM_SUBMENU ON ADM_SUBMENU.ID_SUBMENU = ADM_PERFIL_PERMISOS.ID_SUBMENU
			WHERE ADM_PERFIL_PERMISOS.ID_PERFIL = ".$id_profile."
			 AND ADM_SUBMENU.TIPO = 'R'
			ORDER BY ADM_SUBMENU.DESCRIPTION  ASC";
	$query = $db->sqlQuery($sql);
	$count = $db->sqlEnumRows($query);
	
	if($count>0){
		while($row = $db->sqlFetchArray($query)){
			$tpl->assign_block_vars('submenu',array(
					'IDS'			=> $row['ID_SUBMENU'],
					'SMN'			=> utf8_encode($row['DESCRIPTION']),
					'LNK'			=> $row['UBICACION']
			));
		}
	}
	
	$tpl->assign_vars(array(	
		'PATH'			=> $dir_mod,
		'PATH_IMG'		=> $dir_pimages,
		'IDCLIENTER'	=> $id_cliente,
		'IDUSUARIOR'	=> $id_usuario
	));	
	/*MODIFICACION PROVISIONAL PARA MOSTRAR EL NUEVO MENU*/
	include "claseReportes.php";
	$objRP=new reportes();
	$menuR=$objRP->extrerMenuUsuario($id_usuario);
	if($menuR=="S/N"){
		echo "No hay Menú especificado";
	}else{
		$menuR=explode("||",$menuR);//se separa los registros
		$temp="";
		$menuUR="";
		$bandera=false;
		//$tpl->set_filenames(array('controlador' => 'tNuevoMenu'));
		$menuUR.="<div id='acordeonReportes'>";
		for($i=0;$i<count($menuR);$i++){
			$registro=explode("|",$menuR[$i]);
			if($registro[1]!=$temp){
				if($bandera==true){
					$menuUR.="</div>";
					$bandera=false;
				}
				$menuUR.="<h3><span style='margin-left:15px;'>".$registro[1]."</span></h3><div><div class='opcReporte' onclick='cargaElementosReporte(\"".$registro[3]."\")'>".$registro[2]."</div>";
				$bandera=true;
			}else{
				$menuUR.="<div class='opcReporte' onclick='cargaElementosReporte(\"".$registro[3]."\")'>".$registro[2]."</div>";
			}					
			$temp=$registro[1];
		}
		$menuUR.="</div></div>";
		//echo htmlentities($menuUR);
		$tpl->assign_vars(array(
			'MENUR'	=> $menuUR
		));
	}
	$tpl->pparse('menu');
?>