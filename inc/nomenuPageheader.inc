<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN"> -->
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8">
	<script language="JavaScript">
		document.write('<script language="JavaScript" src="javascript/datax.js"></sc' + 'ript>');
	</script>	
	<meta name="MSSmartTagsPreventParsing" content="true">
	<link rel="stylesheet" type="text/css" href="<?php print($sess_style); ?>">
	<link rel="stylesheet" type="text/css" href="css/tests.css">		
	<link rel="stylesheet" type="text/css" href="css/menuh.css">		

<!--[if IE]>

<style type="text/css" media="screen">
body {
behavior: url(css/csshover.htc); /* call hover behaviour file */
font-size: 100%; /* enable IE to resize em fonts */
} 
#menu ul li {
float: left; /* cure IE5.x "whitespace in lists" problem */
width: 100%;
}
#menu ul li a {
height: 1%; /* make links honour display: block; properly */
} 

#menu a, #menu h2 {
font: bold 1em/1.4em arial, helvetica, sans-serif; 
/* if required use em's for IE as it won't resize pixels */
} 
</style>

<![endif]-->

	
		
    <title><?php print($PHP_SELF); ?></title>
    <base target="_self">
</head>
<script language="JavaScript">
	var source;
	var tipo;
	var campi = new Array();
<?php if ($sess_lang==0) {
?>
	var mesi = new Array('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
	               'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');
	
	var days = new Array('Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa', 'Do');	
	var close_desc = 'chiudi';
<?           
       } else {
?>
	var mesi = new Array('January', 'February', 'March', 'April', 'May', 'June',
	               'July', 'August', 'September', 'October', 'November', 'December');
	
	var days = new Array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');	
	var close_desc = 'close';
<?	   
	   }
?>



	
	function cal(val, e, t) {
		var dt = new Array();
		xPos = (document.layers) ? e.pageX : ((document.all) ? event.x : e.clientX);
		yPos = (document.layers) ? e.pageY : ((document.all) ? event.y : e.clientY);

		tipo = t;
		source = val;
		
		if (t == 0) {
			dt[0] = val[0].value;
			dt[1] = val[1].value;
			dt[2] = val[2].value;
		}
		else
			dt = val.value.split('-');		

		if (isDate(dt[2], dt[1] - 1, dt[0]))
			showCalendar(new Date(dt[2], dt[1] - 1, dt[0]), xPos, yPos);
		else 
			showCalendar(new Date(), xPos, yPos);
		
	}
</script>
<SCRIPT>

var dhtml = '', no = 0;

if (navigator.appVersion.indexOf("MSIE") != -1)
   dhtml = 'IE';
else
   dhtml = 'NN';


function submit_del(ref_action,del_message)
{
	if (confirm(del_message)){
	   if(dhtml == 'NN') {
	       document.forms[0].action = ref_action;
	       document.forms[0].submit();
	   }else if (dhtml == 'IE'){
	       document.forms[0].action = ref_action;
	       document.forms[0].submit();
	   }
	}
}
</SCRIPT>
<?php
if ($menuType=='Htabs') {
	//require_once('menu_y.inc');	
} else {
	//require_once('menu_c.inc');
}
$menuBar = new menu_c($sess_uid);

print('<body>');
print('<div id="topHeader"><img src="immagini/fascia_logo.jpg" ></div>'."\n");
print('<div id="topTitle">'.$top_label.'</div>'."\n");
print('<div id="topOwner">'."\n");
if (isset($systemData)){
	$sites=dbselect("select * from $systemData.sys_sites");
	print('<select id="selectSite" onChange="setSite(this.value)">'."\n");
	for ($index = 0; $index < $sites['NROWS']; $index++) {
		$selectedOption=$_SESSION['sess_site']['siteData']==$sites['ROWS'][$index]['SITE_DATA']?' selected ':'';
		print('<option value="'.$sites['ROWS'][$index]['SITE_ID'].'" '.$selectedOption.'>'.$sites['ROWS'][$index]['SITE_NAME'].'</OPTION>'."\n");
	}
	print('</select>'."\n");
} else {
	print($menuBar->GetPersonName().' '.date("d-m-Y",time()));
}
print('</div>');
print('<div id="clear"></div>'."\n");

print('<table width="100%" cellpadding="0" cellspacing="0" >'."\n");
print('<tr><td id="pageBody" >');

?>


