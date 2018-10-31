<?php

/**
 * 
 *
 * @version $Id: select_body.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003 
 **/
//var_dump($result);
	$riga=true;
    print('<table width="100%" border="0"');
    print($bg_color);
    print('>');
    print('<tr><td>');
	print("\n");
    for ($x=$start; $x<$limit; $x++) {
		if ($riga) {
	        $row_class=($row_class=="lista1")?"lista2":"lista1";
	        $bg_color=($row_class=="lista1")?"":' bgcolor="#EEEEf8"';
			print('<!--   Nuova Riga -->'."\n");
	        print('<tr>');
			$riga=false;
		} 
	    print('<td width="50%" class="');
        print($row_class);
        print('">');
        print('<table width="100%" border="0"');
        print($bg_color);
        print('>');
        print('<tr>');
		// Numero riga
        print('<td width="3%" class="');
        print($row_class);
        print('">');
        print('</td>');
		// Immagine oppure icona
        print('<td align="left" class="');
        print($row_class);
        print('">');

//        print('"><a href="get_file.php?f=');
//        $wk_param=str_replace("+","�",$result['ROWS'][$x]['FILENAME']);
//        print($wk_param);
//		print('&wk_link_id=');
//		print($result['ROWS'][$x]['LINK_ID']);
//		print('&wk_inline=');
//		print($result['ROWS'][$x]['INLINE']);
//        print('">');
		if ($result['ROWS'][$x]['UPLOADS_TYPE']=='S') {
			print('<img src="thumbnail.php?gd=2&src='.$dir_upload.$result['ROWS'][$x]['FILENAME'].'&maxw=40" alt="'.$alt_message.'" >');
		} elseif ($result['ROWS'][$x]['UPLOADS_TYPE']=='F'){
			print('<img src="');
			print(get_mime($result['ROWS'][$x]['FILENAME']));
			print('" width="16" height="16" border="0" alt="'.$result['ROWS'][$x]['DIDASCALIA'].$alt_message.'" >');
		}
        print('</td>');
//        print('</a></td>');

		// Titolo e didascalia 
		print('<td width="100%" align="left">');
			print('<table cellspacing="5" width="100%" ><tr>');
	        print ('<td valign="top" align="left" class="'.$row_class.'">');
			print('&nbsp;&nbsp;');

		print('<a href="get_file.php?f=');
        $wk_param=str_replace("+","�",$result['ROWS'][$x]['FILENAME']);
        print($wk_param);
		print('&wk_link_id=');
		print($result['ROWS'][$x]['LINK_ID']);
		print('&wk_inline=');
		print($result['ROWS'][$x]['INLINE']);
        print('" target="other" >');

//	        print('<a href="');
//	        print('link_template.php?wk_link_id=');
//	        print($result['ROWS'][$x]['LINK_ID']);
//			print('&wk_page='.$wk_page);
//	        print('">');		
			print('<b><i><font color="#990000" size="2" >');
			print($sequence);
	        print($result['ROWS'][$x]['LABEL']);
	        print('</a></td></tr>');
	        print("\n");     
			// Seconda riga
	        print('<tr>');				
	        print('<td valign="top" class="'.$row_class.'">');
	        print($result['ROWS'][$x]['LINK_NOTES']);		
			print('</td>');
			print('</tr>');
	        print('</table>');
	        print ("\n");
		print('</td></tr>');
        print('</table>');
        print('</td>');
		if (!$riga) {
	        print('</tr><tr><td colspan="2"><hr></td></tr>');
	        print("\n");
			$riga=true;
		} else {
			$riga=false;
		}
    }
	print('</td></tr></table>');
?>