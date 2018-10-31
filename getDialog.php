<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
$db = Db_Pdo::getInstance();
switch ($_GET['dialog']) {
    case 'tipoPratica':
        $ogResult = $db->query('SELECT numeroregistrazione, date_format(dataregistrazione,"%Y") as anno , oggetto, esito_id, protuscita, modello, note FROM pratiche WHERE pratica_id = :pratica_id', array(
            ':pratica_id' => $_GET['praticaId'],
        ))->fetch();

        $zonaResult = $db->query('SELECT arco.uoid, arco.description from arc_organizzazione arco
                RIGHT JOIN arc_pratiche_uo apuo on (arco.uoid = apuo.uoid)
                WHERE apuo.pratica_id = :pratica_id AND arco.tipo = "Z"', array(
            ':pratica_id' => $_GET['praticaId'],
        ))->fetch();

        $ufficioResult = $db->query('SELECT arco.uoid, arco.description from arc_organizzazione arco
        RIGHT JOIN arc_pratiche_uo apuo on (arco.uoid = apuo.uoid)
        WHERE apuo.pratica_id = :pratica_id AND arco.tipo = "U"', array(
            ':pratica_id' => $_GET['praticaId'],
        ))->fetch();

        print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
            'url="xml/jsonSql.php?sql=select * from arc_organizzazione where TIPO = \'Z\' " ' .
            'jsId="getZone" ' .
            '/>');
        print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
            'url="xml/jsonSql.php?sql=select * from arc_organizzazione where TIPO = \'U\'" ' .
            'jsId="getUffici" ' .
            '/>');


        print('<div class="djFormContainer" style="width: 450px; display: block;">');
        print('<div>' . $ogResult['numeroregistrazione'] . '-' . $ogResult['anno'] . ' - ' .
            $ogResult['oggetto'] . '</div>');
        print('<fieldset style="border:none">' . "\n");
        print('<label for="SEL_ZONA_QUICK">Zona</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_ZONA_QUICK"
							store="getZone"
							labelAttr="DESCRIPTION"
							required="false"
							searchAttr="DESCRIPTION"
							name="ZONA" ' .
            'value="' . $zonaResult['uoid'] . '"' .
            '>' .
            '<br/>');
        print('<label for="SEL_UFFICIO_QUICK">Ufficio</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_UFFICIO_QUICK"
							store="getUffici"
							labelAttr="DESCRIPTION"
							required="false"
							searchAttr="DESCRIPTION"
							name="UFFICIO" ' .
            'value="' . $ufficioResult['uoid'] . '"' .
            '>' .
            '<br/>');
        print('<label for="SEL_MODELLO">Tipo Pratica</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_MODELLO_CHIUSURA"
							store="getModelli"
							required="false"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="MODELLO" ' .
            'value="' . $ogResult['modello'] . '"' .
            '>' .
            '<br/>');
        print('<label for="SEL_ESITO">Esito</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_ESITO_CHIUSURA"
							store="getEsiti"
							required="false"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="ESITO_ID" ' .
            'value="' . $ogResult['esito_id'] . '"' .
            '>' .
            '<br/>');
        print('<label for="DATAUSCITA" >Data Uscita</label>' .
            '<input dojoType="dijit.form.DateTextBox" required="false" type="text" name="USCITA"
				        id="DATAUSCITA" value="" >' .
            '<br/>');
        print('<label for="NOTE">Note</label>
                        <textarea id="NOTE" name="NOTE" dojoType="dijit.form.Textarea">' . $ogResult['note'] . '</textarea>' .
            '<br>');

        print('<input dojoType="dijit.form.TextBox" type="hidden" name="prId" id="prId" value="' . $_GET['praticaId'] . '" >' .
            '<br/>');
        print('</fieldset>' . "\n");
        print(' <button dojoType="dijit.form.Button" ' .
            'onClick="aggiornaTipo()">Aggiorna Pratica</button>');
        print('</div>');
        break;
    default:
        $ogResult = dbselect('select OGGETTO, ESITO_ID, MODELLO from pratiche where pratica_id = ' . $_GET['praticaId']);
        print('<div class="djFormContainer" style="width: 450px; display: block;">');
        print('<div>' . $ogResult['ROWS'][0]['OGGETTO'] . '</div>');
        print('<fieldset style="border:none">' . "\n");
        print('<label for="SEL_MODELLO">Tipo Pratica</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_MODELLO_CHIUSURA"
							store="getModelli"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="MODELLO" ' .
            'value="' . $ogResult['ROWS'][0]['MODELLO'] . '"' .
            '>' .
            '<br/>');
        print('<label for="SEL_ESITO">Esito</label>');
        print('<input dojoType="dijit.form.FilteringSelect" ID="SEL_ESITO_CHIUSURA"
							store="getEsiti"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="ESITO_ID" ' .
            'value="' . $ogResult['ROWS'][0]['ESITO_ID'] . '"' .
            '>' .
            '<br/>');
        print('<label for="DATAUSCITA" >Data Uscita</label>' .
            '<input dojoType="dijit.form.DateTextBox" type="text" displayFormat="dd-MM-yyyy" name="DATAUSCITA" id="DATAUSCITA" value="' . date('Y-m-d') . '" >' .
            '<br/>');
        print('<input dojoType="dijit.form.TextBox" type="hidden" name="prId" id="prId" value="' . $_GET['praticaId'] . '" >' .
            '<br/>');
        print('</fieldset>' . "\n");
        //	print(' <button dojoType="dijit.form.Button" ' .
        //					'onClick="return dijit.byId(\'dialogOne\').isValid();">Chiudi Pratica</button>');
        print(' <button dojoType="dijit.form.Button" ' .
            'onClick="chiudiPratica()">Chiudi Pratica</button>');
        print('</div>');
        break;
}
?>
