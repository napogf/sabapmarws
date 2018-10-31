<?php
/*
 * Created on 20/ago/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "login/autentication.php";

 $respQuery = "select res.RESP_ID, drr.RESP_ID from resopnsabilities res " .
 						"left join dir_resp_reference drr on ((drr.resp_id = res.resp_id ) and (drr.dir_id = $dirId)";
 $respResult = dbselect($respQuery);

 var_dump($respQuery);


print('<div id="dirRespAssign"><FORM   METHOD="POST" name="Manage_Directory">');
print('<input type="submit" value="Assegna" onclick="assignResp()" name="buttapp" style="margin: 10px; float: left;"/>');
print('<input type="submit" value="Assegna" onclick="assignResp()" name="buttapp" style="margin: 10px; float: right;"/>');
for ($index = 0; $index < ; $index++) {

}


print('<input type="submit" value="Assegna" onclick="assignResp()" name="buttapp" style="margin: 10px; float: left;"/>');
print('<input type="submit" value="Assegna" onclick="assignResp()" name="buttapp" style="margin: 10px; float: right;"/>');
print('</FORM></div>');










?>
