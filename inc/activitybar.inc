<?php

/**
 * 
 *
 * @version $Id: activitybar.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003 
 **/

print('<hr>');
print('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
print('<tr>');
print("\n");  
print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>'."\n");    

$originFilter=$result_action['ROWS'][0]['ORIGIN_ID']>''?'?ORIGIN_ID='.$result_action['ROWS'][0]['ORIGIN_ID']:'';

print('<td valign="middle" width="100%" class="lista2" >&nbsp;&nbsp;<a href="workflow.php'.$originFilter.'" >');
print(get_label('go_back'));
print('</a></td>'."\n");

print('<td valign="middle" align="right" nowrap class="lista2" >&nbsp;&nbsp;<a href="#" onClick="javascript:if (confirm(\''.GetMessage('stopWork').'\')) location.href=\'stopWork.php?ACTIVITY_ID='.$ACTIVITY_ID.'\'" >');
print(get_label('stopWork'));
print('</a></td>'."\n");
print('<td class="lista2" ><img src="graphics/toolbar_icon_stop_active.gif" ></td>'."\n");    

print('</tr>'."\n");
print('</table>');
print('<hr>');

?>