<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
	<meta charset="utf-8">


<link rel="stylesheet" type="text/css"
	href="release/dojo/dojo/resources/dojo.css" />
<link rel="stylesheet" type="text/css"
	href="release/dojo/dijit/themes/tundra/tundra.css" />

<script type="text/javascript" src="release/dojo/dojo/dojo.js" djConfig="parseOnLoad: true"></script>

	<script type="text/javascript" src="javascript/common.js"></script>
	<script type="text/javascript" src="javascript/dojoUtils.js?<?php print(filemtime(ROOT_PATH . DIRECTORY_SEPARATOR . 'javascript/dojoUtils.js')); ?>"></script>
	<script type="text/javascript" src="javascript/custom.js"></script>
	<script type="text/javascript" src="javascript/sysUtils.js"></script>

	<meta name="MSSmartTagsPreventParsing" content="true">
	<link rel="stylesheet" type="text/css" href="<?php print($_SESSION['sess_style']); ?>">
	<link rel="stylesheet" href="css/font-awesome-4.4.0/css/font-awesome.css">
<!--	<link rel="stylesheet" type="text/css" href="css/tests.css"> -->
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/ie.css">
<![endif]-->
    <title><?php print($_SESSSION['sess_title']); ?></title>
    <base target="_self">
</head>
<body class="tundra">
<?php

class myMenu extends menu {
	/**
     * Constructor
     * @access protected
     */
}
$_SESSION['menu_id'] = isSet($_GET['menu_id']) ? $_GET['menu_id'] : $_SESSION['menu_id'];
$menuBar = new myMenu($_SESSION['sess_uid']);
print('<div id="appLayout" dojoType="dijit.layout.BorderContainer" design="headline" ' .
		'style="height:100%;width:100%;" >'."\n");

	print('<div dojoType="dijit.layout.ContentPane" region="top" class="topBar"  >');
			print('<div id="topLogo" ><img src="immagini/logo_new.jpg" ></div>'."\n");
			print('<div id="clear"></div>'."\n");

				if ($menuType=='Htabs') {
					if (IsSet($wk_dir_id) and (!isSet($_SESSION['menu_id']))) {
						$menuBar->showMenu($wk_dir_id,TRUE);
					} else {
						$menuBar->showMenu($_SESSION['menu_id'],FALSE);
					}
				} else {
					$menuBar->showMenu();
				}
	print('</div>'."\n");
	print('<!-- fine Page Header -->'."\n");
	print('
	<div dojoType="dijit.layout.ContentPane" region="bottom"  >
	<span class="footer">Open Source Solutions - Via Levà,24 - 36030 (VI) - <a href="mailto:giacomo.fonderico@opensourcesolutions.it">info@opensourcesolutions.it</a></span>
	</div>
	');
print('<div dojoType="dijit.layout.ContentPane" class="mainBox" region="center" id="mainBox"
		 style="heigth: 100%; overflow:auto;margin:2px 2px 2px 2px;">');
?>
