<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
  	<title>Programma Pratiche</title>
	<link rel="stylesheet" type="text/css" href="/css/main.css">
 	<link rel="shortcut icon" href="./graphics/immagini/oss.png" />

</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="">

<?php
if (IsSet($wk_dir_id) and IsSet($Livello)) {
   print('<form action="login/autentica.php?wk_dir_id='.$wk_dir_id.'&Livello='.$Livello.'" method="Post">');
} else {
   print('<form action="login/autentica.php" method="Post">');
}
?>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <table width="100%" align="center">
    <tbody>
    <tr>
      <td colspan="2">
        <div align="center"><br>
          <img src="./immagini/logo_new.jpg" > <br>
        </div>
      </td>
    </tr>
    <tr>
      <td width="50%">
        <div align="Right"><br>
          Utente: <br>
        </div>
      </td>
      <td width="50%">
        <input type="text" name="username" id="999">
      </td>
    </tr>
    <tr>
      <td width="11%">
        <div align="Right">Password: </div>
      </td>
      <td width="18%">
        <p>
          <input type="password" name="password">
        </p>
      </td>
    </tr>
    <tr>
      <td width="11%">&nbsp;</td>
      <td width="18%">
        <div align="Left">
          <input type="submit" value="Entra">
        </div>
      </td>
    </tr>
    <tr>
      <td width="11%">&nbsp;</td>
      <td width="18%">&nbsp;</td>
    </tr>
<?php
if (IsSet($login) and ($login=='failed')) {
    print('<tr align="center">');
    print('  <td colspan="2"><b><i><font color="#FF0000">Login Failed - Incorrect Username');
    print('    or Password</font></i></b></td>');
    print('</tr>');
}
?>
    </tbody>
  </table>
</form>
<script language="JavaScript" type="text/javascript">
  document.getElementById('999').focus();
</script>

</body>
</html>
