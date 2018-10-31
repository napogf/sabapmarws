<?php
print('<div id="topHeader">');

	print('<div id="topMessage">');
	print('Messaggio '.$topbar['W_MESSAGE'][0]);
	print(" ");
	print($_SESSION['sess_nome']);
	print('</div>');

	print('<div id="topMenu">');
	$menuBar = new menu_c($_SESSION['sess_uid']);
	$menuBar->showMenu();
	print('</div>');

	print('<div id="topData">');
	print(date("d-m-Y H:i",time()));
	print('</div>');

print('</div>');
?>
