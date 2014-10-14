function rep_abrir_modulo(m){
	//alert(m);
	//$("#accordion_container").html("");
	$.ajax({
		type: "POST",
        url: "index.php?m="+m+"&c=default",
        data: "",
        success: function(datos){
			if(datos!=0){
				$("#rep_content").html(datos);
			    //$("#example").treeview();
			}else{
				$("#rep_content").html("No se han Creado un menú");
			}
			
        }
	});
}