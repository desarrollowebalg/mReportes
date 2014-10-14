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
	
	$id_profile = $userAdmin->user_info['ID_PERFIL'];
	
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
					'IDS'	=> $row['ID_SUBMENU'],
					'SMN'	=> utf8_encode($row['DESCRIPTION']),
					'LNK'	=> $row['UBICACION']
					
			  	));
			}
		}
		
	$tpl->assign_vars(array(	
		'PATH'			=> $dir_mod,
		'PATH_IMG'		=> $dir_pimages,
	));	

	$tpl->pparse('menu');
?>