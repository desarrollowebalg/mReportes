<?php
	$userAdmin->u_logged();
	/*
	*CONFIGURACION DE LAS VARIABLES PARA LAS CLAUSULA WHERE DE LAS CONSULTAS
	*/
	$configuracionParametros = array(
		"@USUARIOSISTEMA"	=>	$userAdmin->user_info['ID_USUARIO'],
		"@FECHAINICIAL"		=>	"widgetFechaInicial",
		"@HORAINICIAL"		=>	"widgetCboHoraInicial_1",
		"@MININICIAL"		=>	"widgetCboHoraFinal_1",
		"@FECHAFINAL"		=>	"widgetFechaFinal",
		"@HORAFINAL"		=>	"widgetCboHoraInicial_2",
		"@MINFINAL"			=>	"widgetCboHoraFinal_2",
		"@UNIDADES"			=>	"cboWidgetGruposUnidades",
		"@USUARIOS"			=>	"cboWidgetUsuarios"
	);
?>