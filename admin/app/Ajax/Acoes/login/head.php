<?
	//lang
	if(isset($_SESSION['lang'])) $lang = $_SESSION['lang'];
	elseif(isset($_GET['lang'])){ $lang = $_GET['lang']; $_SESSION['lang'] = $_GET['lang']; }
	else $lang = 1;	
	
	if($_SESSION['lang'] == '' or $_SESSION['lang'] == 0){ $_SESSION['lang'] = 1; $lang = 1; }
	//lang
?>
<!-- Meu -->
<?php include '../paginas/app/my_functions/img.php'?>
<?php include '../paginas/app/my_functions/img_link.php'?>
<?php include '../paginas/app/my_functions/youtube.php'?>
<?php include '../paginas/app/my_functions/youtube2.php'?>
<?php include '../paginas/app/select/select_mysql_admin.php'?>

<!-- Meu -->

<link rel="stylesheet" type="text/css" href="../css/style_meu.css" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/ui.core.css" />
<link rel="stylesheet" type="text/css" href="../css/ui.datepicker.css" />
<link rel="stylesheet" type="text/css" href="../css/ui.theme.css" />
<link rel="stylesheet" type="text/css" href="../css/ui.tabs.css" />
<link rel="stylesheet" type="text/css" href="../css/ui.dialog.css" />
<link rel="stylesheet" type="text/css" href="../css/colorbox/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../js/smarkup/smarkup/skins/style.css" />
<link rel="stylesheet" type="text/css" href="../js/smarkup/smarkup/skins/default/style.css" />
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/colorbox/colorbox-ie.css" />
<![endif]-->
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="../js/main.js"></script>
<!--<script type="text/javascript" src="js/sorttable.js"></script>-->
<script type="text/javascript" src="../js/akeditable.js"></script>
<script type="text/javascript" src="../js/jQuery Cycle.js"></script>  
<script type="text/javascript" src="../js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="../js/jquery.example.min.js"></script>
<script type="text/javascript" src="../js/multiple_checkbox.js"></script>
<script type="text/javascript" src="../js/supersleight.js"></script>
<script type="text/javascript" src="../js/smarkup/smarkup/jquery.smarkup.js"></script>
<script type="text/javascript" src="../js/smarkup/smarkup/smarkup.js"></script>
<script type="text/javascript" src="../js/smarkup/smarkup/conf/html/conf.js"></script>

<!-- Meu -->
<!-- datatable -->
		<style type="text/css" title="currentStyle">
			@import "../js/datatables/css/page.css";
			@import "../js/datatables/css/table_jui.css";
			@import "../js/datatables/themes/smoothness/jquery-ui-1.7.2.custom.css";
		</style>
		<script type="text/javascript" language="javascript" src="../js/datatables/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers"
				});
			} );
		</script>
<!-- datatable -->

<!-- Campo -->
<link href="../js/valida/campo_form.css"  rel="stylesheet" type="text/css" />
<script src="../js/valida/jquery.validate.js" type="text/javascript"></script>
<script src="../js/valida/cmxforms.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#form").validate();
    });
    </script>
<!-- Campo -->

<script type="text/javascript">
<!--
function carreg(targ,selObj,restore){ //v3.0
  		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  	if (restore) selObj.selectedIndex=0;
	}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<?php include "../js/fckeditor/fckeditor.php"; ?>
<script src="../js/fckeditor/fckeditor.js"></script> 

<script language=javascript> 
	var max=100;
	var ancho=300;
	function progreso_tecla(obj) {
	 var progreso = document.getElementById("progreso");  
	 if (obj.value.length < max) {
		progreso.style.backgroundColor = "#FFFFFF";    
		progreso.style.backgroundImage = "url(../img/textarea.png)";    
		progreso.style.color = "#FF0000";
		var pos = ancho-parseInt((ancho*parseInt(obj.value.length))/300);
		progreso.style.backgroundPosition = "-"+pos+"px 0px";
	  } else {
		progreso.style.backgroundColor = "#CC0000";    
		progreso.style.backgroundImage = "url()";    
		progreso.style.color = "#FFFFFF";
		alert("use somente 100 caracteres");
	  } 
	  progreso.innerHTML = "("+obj.value.length+" caracteres usados de "+max+")";
	}
		
</script>
<!-- Meu -->


<?
/* if($localhost_config != 'localhost' or $banco_config != 'ncbrasil_16_gafisa'){
	if($localhost_config != 'mysql.carvalhocamara.com.br' or $banco_config != 'carvalhocamara'){
		header("Location: erro.php");
	}
} */
?>