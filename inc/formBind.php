<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">	
<head>
    <script type="text/javascript" src="dojo/dojo.js"></script>
	<script type="text/javascript">
		dojo.require("dojo.widget.*");
		dojo.require("dojo.io.*");
		dojo.require("dojo.io.IframeIO");
	</script>
	<script language="JavaScript">

		function formCall(url,par){
			url='test.php';
			var req = dojo.io.bind({
				url: url+par,
				handler: getDojoResponse,
				error: function(type, error) { alert(error); },
				mimetype: 'text/html'
			});
		}
		function getDojoResponse(type, data, event){
			if (type == 'error'){
	          alert('Error when retrieving data from the server!');
	          }
	        else {
	          dojo.byId('lastUpdates').innerHTML=data;
	          }
		}
	</script>
<?php
/*
 * Created on 29-dic-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


	print('<div id="lastUpdates" class="lastUpdate" >'."\n");
//	print('<script>formCall();</script>');
	include('test.php');	
	print('</div>'."\n");



?>
</body>
</html>