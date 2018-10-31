<?php


// Loading dei contenuti per i toolTips in praticheStatus.php

function getToolTipPratica(pratica_id){
  dojo.xhrGet({
    url: "djPraticaToolTip.php?pratica_id="+pratica_id,
    load: function(response, ioArgs){
      dojo.byId("tool"+pratica_id).innerHTML = response;

      //Dojo recommends that you always return(response); to propagate
      //the response to other callback handlers. Otherwise, the error
      //callbacks may be called in the success case.
      return response;
    },
    error: function(response, ioArgs){
      dojo.byId("tool"+pratica_id).innerHTML =
        "An error occurred, with response: " + response;
      return response;
    },
    handleAs: "text"
  });

}

/*
 * Created on 19/ago/09
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");


$result=dbselect('select pr.PRATICA_ID, ' .
		'pr.condizione as PARERE, ' .
		'am.description as \'MODELLO\', ' .
		'au.description as "UFFICIO", ' .
		'pr.comuneogg as "OGGETTO", ' .
		'pr.pnome as "PROPIETARIO", ' .
		'substring(pr.cognome,1,40) COGNOME, ' .
		'az.code as ZONACOD, ' .
		'substring(az.description,1,20) ZONADES ' .
		'from pratiche pr ' .
		'left join arc_zone az on (az.zona = pr.zona) ' .
		'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
		'left join arc_modelli am on (am.modello = pr.modello) ' .
		'where pratica_id = '.$pratica_id);





if(!$result){
	print('Pratica inesistente!!!');
} else {
		print('Parere -> '.$result['ROWS'][0]['PARERE'].'<br>');
		print('Modello -> '.$result['ROWS'][0]['MODELLO'].'<br>');
		print('Ufficio -> '.$result['ROWS'][0]['UFFICIO'].'<br>');
		print('Oggetto -> '.$result['ROWS'][0]['OGGETTO'].'<br>');
		print('Proprietario -> '.$result['ROWS'][0]['PROPIETARIO'].'<br>');
		print('Mittente -> '.$result['ROWS'][0]['COGNOME'].'<br>');
		print('Zona -> '.$result['ROWS'][0]['ZONACOD'].' - '.$result['ROWS'][0]['ZONADES'].'<br>');
}
?>
