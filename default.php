<?php
/** * 
 *  @package             
 *  @name                Pagina default del modulo reportes
 *  @version             1
 *  @copyright           Air Logistics & GPS S.A. de C.V.   
 *  @author              Rodwyn Moreno
 *  @modificado          23-04-2012
 *  @modificado          15-10-2014
 *  @author              Gerardo Lara
**/
	$db = new sql($config_bd['host'],$config_bd['port'],$config_bd['bname'],$config_bd['user'],$config_bd['pass']);
	$userAdmin->u_logged();
	/*if(!$userAdmin->u_logged())
		echo '<script>window.location="index.php?m=login"</script>';*/
	$tpl->set_filenames(array('default'=>'default'));	
	//$idProfile   = $userAdmin->user_info['ID_PROFILE'];
	$tpl->assign_vars(array(
		'PAGE_TITLE'	=> "Reportes",	
		'PATH'			=> $dir_mod,
		'PATH_IMG'		=> $dir_pimages,
		'APIKEY'		=> $config['keyapi'],
		'COD_USER' 		=> $userAdmin->user_info['ID_USUARIO'],
		'COD_CLI'	 	=> $userAdmin->user_info['ID_CLIENTE']
	));	

	$tpl->pparse('default');
?>