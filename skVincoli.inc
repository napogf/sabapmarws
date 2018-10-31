<?php
/**
 * Created by PhpStorm.
 * User: giacomo
 * Date: 26/05/17
 * Time: 13.59
 */
$vincoliQuery = 'select distinct ' .
    "concat('<input type=\"radio\" name=\"VINCOLO_ID\" value=\"',av.vincolo_id,'\" ',
											   (case when pr.VINCOLO_ID is null then ''
											   		 when pr.VINCOLO_ID is not null then 'checked'
												end),'>') as '#', " .
    'av.denominazione ,  ' .
    'av.comune ,  ' .
    'av.localita ,  ' .
    'av.provincia ,  ' .
    'av.fogliocatastale ,  ' .
    'av.particelle, ' .

    //						'av.modifichecatastali ,  ' .
    'trim(concat(av.ubicazioneinit,\' \',av.ubicazioneprinc)) as indirizzo,' .
    //
    //						'av.ubicazioneinit ,  ' .
    //						'av.ubicazioneprinc ,  ' .
    //						'av.vincolodiretto ,  ' .
    //						'av.vincoloindiretto ,  ' .
    'av.provvedimentoministeriale ,  ' .
    'av.trascrizioneinconservatoria ,  ' .
    //						'av.posizionegeneralecomune ,  ' .
    'av.cartellaprogettimonumentale ,  ' .
    //						'av.eventualesubposizione ,  ' .
    //						'av.fascicolovincolo ,  ' .
    //						'av.fascicoloprogetti ,  ' .
    'av.posizioneMonumentale ,  ' .
    'av.posizioneVincoli ' .
    'from vincoli av ' .
    'right join pratiche pr on (pr.vincolo_id = av.vincolo_id) ' .
    'where pr.pratica_id = ' . $this->_FormFields['PRATICA_ID']->GetValue();

//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
print ('<fieldset style="border:none">' . "\n");
if ($vincoliResult = dbselect($vincoliQuery)) {
    print ('<LABEL>Vincolo</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["denominazione"]) . '<BR>');
    print ('<LABEL>Indirizzo</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["indirizzo"]) . '<BR>');
    print ('<LABEL>Localit&agrave;</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["localita"]) . '<BR>');
    print ('<LABEL>Comune/Prov.</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["comune"]) . ' ' . $vincoliResult['ROWS'][0]["provincia"] . '<BR>');
    print('<LABEL>Foglio catastale</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["fogliocatastale"]) . '<BR>');
    print('<LABEL>Particelle</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["particelle"]) . '<BR>');
    print('<LABEL>Provv. Ministeriale</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["provvedimentoministeriale"]) . '<BR>');
    print('<LABEL>Trascrizione conservatoria</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["trascrizioneinconservatoria"]) . '<BR>');
    print('<LABEL>Posizione monumentale</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["posizioneMonumentale"]) . '<BR>');
    print('<LABEL>Posizione vincoli</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["posizioneVincoli"]) . '<BR><BR>');
    print('<LABEL>Cartella monumentale</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["cartellaprogettimonumentale"]) . '<BR>');

    $pathCartellaMonumentale = 'archivio' . DIRECTORY_SEPARATOR .
        sanitizePath($vincoliResult['ROWS'][0]['provincia']) . DIRECTORY_SEPARATOR .
        sanitizePath($vincoliResult['ROWS'][0]['comune']) . DIRECTORY_SEPARATOR .
        (!empty($vincoliResult['ROWS'][0]['cartellaprogettimonumentale']) ?
            sanitizePath($vincoliResult['ROWS'][0]['cartellaprogettimonumentale']) . DIRECTORY_SEPARATOR
            : '');

    print('<a href="' . $pathCartellaMonumentale . '" target="_blank"><i class="fa fa-folder-open-o fa-2x fa-align-right" aria-hidden="true"></i> Cartella Monumentale</a>');

    print('</fieldset>' . "\n");
}
