<?php
include "login/autentication.php";
$db = Db_Pdo::getInstance();
$session = new Session_Namespace(__FILE__);
if (isSet($_POST) and ! empty($_POST)) {
    // genero protocollo
    try {
        if($_POST['tipologia'] == 'Interno'){
            try {
                $db->beginTransaction();
                $progressivo = (integer)$db->query('SELECT count(*) FROM pratiche WHERE dataregistrazione between :data1 and :data2 and numeroregistrazione like "NP%"', [
                        ':data1' => date('Y') . '-01-01',
                        ':data2' => date('Y') . '-12-31',
                    ])->fetchColumn() + 1;

                $db->query('INSERT INTO pratiche (
                    tipologia,
                    numeroregistrazione, 
                    dataregistrazione,
                    oggetto,
                    comuneogg,
                    modello,
                    note
                    ) values (
                    :tipologia,
                    :numeroregistrazione, 
                    :dataregistrazione,
                    :oggetto,
                    :oggetto,
                    :modello,
                    :note
                    )', [
                    ':tipologia' => 'E',
                    ':numeroregistrazione' => 'NP-' . str_pad($progressivo, 4, '0', STR_PAD_LEFT),
                    ':dataregistrazione' => date('Y-m-d'),
                    ':oggetto' => $_POST['clsTestataDocumento']['Oggetto'],
                    ':modello' => $_POST['classifica'],
                    ':note' => $_POST['clsTestataDocumento']['Note'],
                ]);
                $praticaId = $db->lastInsertId();
                $fascicoloId = (integer)$db->query('SELECT max(fascicolo_id) FROM pratiche_fascicoli')->fetchColumn() + 1;
                $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id,  funzione, tipologia) values
                                                                        (:pratica_id, :fascicolo_id, null, "E")', array(
                    ':pratica_id' => $praticaId,
                    ':fascicolo_id' => $fascicoloId,
                ));



                foreach ($_POST['uoid'] as $uoid) {
                    $db->query('insert into arc_pratiche_uo (pratica_id, uoid) values (:pratica_id, :uoid)', [
                        ':pratica_id' => $praticaId,
                        ':uoid' => $uoid,
                    ]);
                }
                $db->commit();
                header('Location: /editPratica.php?PRATICA_ID=' . $praticaId);
                exit;
            } catch (Exception $e) {
                $db->rollback();
                $session->msg[] = $e->getMessage();
            }
        } else {
            // Converto le date nel formato che vuole ESPI

            $session->msg = [];
            $post = $_POST;
            $post['clsTestataDocumento']['Data'] = Helpers::revertDate($_POST['clsTestataDocumento']['Data']);
            $post['clsTestataDocumento']['Arrivo'] = Helpers::revertDate($_POST['clsTestataDocumento']['Arrivo']);
            $post['CodUfficioCompetente'] = $_POST['CodUfficioCompetente'];


            // function wsHtmlChars(&$item, $key){
            // $item = htmlspecialchars($item);
            // }
            // array_walk_recursive($_POST, 'wsHtmlChars');
            $espiWs = new EspiWS();

            $espiWs->setTipoProtocollo($post['tipologia']);
            $titolario = $db->query('SELECT
                    classificazione as ClasseTitolario, description as DesTitolario
                FROM arc_modelli WHERE modello = :modello', [
                ':modello' => $post['classifica']
            ])->fetch();
            $espiWs->setTitolario($titolario,$post['classifica2']);
            $espiWs->setFascicolo($post['fascicolo']);
            if ($post['tipologia'] == 'Uscita') {
                unset($post['clsTestataDocumento']['Arrivo']);
                $post['clsTestataDocumento']['Data'] = date('d/m/Y');
            }

            $espiWs->setTestataDocumento($post['clsTestataDocumento']);
            if ($_POST['tipologia'] != 'Interno') {
                $espiWs->setMittenteDestinatario(array(
                    0 => $post['clsTMittenteDestinatario']
                ));
            }

            $espiWs->setCodiceUfficioCompetente($post['CodUfficioCompetente']);

            $espiWs->protocollaDocumento();
            $wsResult = $espiWs->getWsresult();



            if ($wsResult->CodError != 'OK') {
                $espiWs->logConnection(0);
                throw new Exception('Errore nella protocollazione in ' . $post['tipologia'] . ' : ' . $wsResult->DesError);
            } else {
                $espiWs->protocollaPratica((isset($post['ws_pratica_id']) ? $post['ws_pratica_id'] : null), $post);
                /*
                 * Aggiornamento o inserimento dell'indirizzo
                 */
                $indirizzoArray = [
                    'titolo' => $post['clsTMittenteDestinatario']['DesTipoAnagrafica'] ,
                    'nome' => $post['clsTMittenteDestinatario']['Nome'] ,
                    'cognome' => $post['clsTMittenteDestinatario']['Cognome'] ,
                    'toponimo' => $post['clsTMittenteDestinatario']['Indirizzo'] ,
                    'cap' => $post['clsTMittenteDestinatario']['CAP'] ,
                    'localita' => $post['clsTMittenteDestinatario']['Localita'] ,
                    'comune' => $post['clsTMittenteDestinatario']['Comune'] ,
                    'provincia' => $post['clsTMittenteDestinatario']['Provincia'] ,
                    'telefono' => $post['clsTMittenteDestinatario']['Telefono'] ,
                    'fax' => $post['clsTMittenteDestinatario']['Fax'] ,
                    'codicefiscale' => $post['clsTMittenteDestinatario']['CF'] ,
                    'email' => $post['clsTMittenteDestinatario']['Email'] ,
                    'pec' => $post['pec'],
                ];

                $indirizzo = new Indirizzo($indirizzoArray);
                if(!$errori = $indirizzo->getError()){
                    $indirizzo->save();
                } else {
                    throw new Exception(implode('|',$errori));
                }

                $session->msg[] = 'Creato il Protocollo ' . $post['tipologia'] . ' -> ' . $wsResult->NumProtocollo . ' del -> ' . $wsResult->DataProtocollo;
            }

            if ($errore = $espiWs->getError()) {
                $session->msg[] = $errore;
                throw new Exception('Errore nella Protocollazione!');
            }

        }
        header('Location: /creaProtocollo.php');
        exit;
    } catch (Exception $e) {
        $session->msg[] = $e->getMessage();
    }
}
// Form per la protocollazione
include 'pageheader.inc';

print('<script type="text/javascript" src="javascript/formProtocollazione.js?' . filemtime(ROOT_PATH . DIRECTORY_SEPARATOR . 'javascript/formProtocollazione.js') . '"></script>');
$isProtocollatore = $db->query('SELECT * from sys_responsabilities resp
        RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
        WHERE surr.user_id = :user_id AND resp.description = "Protocollazione"', array(
    ':user_id' => $_SESSION['sess_uid']
))->fetchAll();


if ($isProtocollatore) {
    if (isSet($session->msg)) {
        foreach ($session->msg as $message) {
            print('<div class="DbFormMessage">' . $message . '</div>');
        }
        unset($session->msg);
    }
    print('<div class="dbFormContainer" >');
    print('<div dojoType="dijit.form.Form" jsId="formProtocollazionePec" id="formProtocollazionePec" encType="multipart/form-data" action="" method="POST">');

    print('<div class="dbFormContainer protocollazione">');

    print('<fieldset>');
    print('<legend>Dati Protocollo</legend>');
    print('<label>Tipo Protocollo</label>');
    print('<input type="radio" name="tipologia" id="tipologiaE" '.
        ((isSet($_POST['tipologia']) and $_POST['tipologia'] == 'Entrata') ? 'checked' : (IsSet($_POST['tipologia']) ? '' : 'checked')) .
        ' value="Entrata"/><span for="tipologiaE">Entrata</span>
           <input type="radio" name="tipologia" id="tipologiaU" '. ((isSet($_POST['tipologia']) and $_POST['tipologia'] == 'Uscita') ? 'checked' : '') .' value="Uscita"/> <span for="tipologiaU">Uscita</span>
           <input type="radio" name="tipologia" id="tipologiaI" value="Interno"/> <span for="tipologiaI">Nuovo procedimento</span>
        ');

    print('<br />');

    print('<label for="classifica" >Classifica</label>

            <div dojoType="dojo.data.ItemFileReadStore"
                    url="xml/jsonSql.php?nullValue=Y&sql=select distinct am.MODELLO,concat(am.classificazione,\'-\',am.description) as DESCRIPTION  from arc_modelli am order by 2"
                    jsId="classificaStore" >
            </div>

            <div dojoType="dijit.form.FilteringSelect"
                    store="classificaStore"
					searchAttr="DESCRIPTION"
                    name="classifica"
                    required="true"
                    disabled="true"
                    id="classifica"
                    value="'. (isSet($_POST['classifica']) ? $_POST['classifica'] : '') .'"
                    style="width:200px;"
                    queryExpr="*${0}*"
                    searchDelay="1000"
                     >
        </div>
        <span style="font-weight: bold; margin: 0px 5px">4° livello</span>
        <input type="TEXT" id="classifica2" maxlength="3" style="width: 30px;" title="Se applicabile 4° livello di classifica senza punto es. 01" 
        value="" name="classifica2" dojoType="dijit.form.ValidationTextBox"><br/>');

    print('<label>Fascicolo</label>
                    <select dojoType="dijit.form.FilteringSelect" id="fascicolo" disabled="true" name="fascicolo" style="width:250px;">
                        <option value="Monumentale" ' .
                                ( (isSet($_POST['fascicolo']) && $_POST['fascicolo'] == 'Monumentale') ? 'selected' : '') .
                            ' >Monumentale</option>                    
                        <option value="Paesaggio" ' .
                            ( (isSet($_POST['fascicolo']) && $_POST['fascicolo'] == 'Paesaggio') ? 'selected' : '') .
                            ' >Paesaggio</option>
                        <option value="Archeologia" ' .
                            ( (isSet($_POST['fascicolo']) && $_POST['fascicolo'] == 'Archeologia') ? 'selected' : '') .
                            ' >Archeologia</option>
                    </select>');
    print('<br />');

    // Testata Documento
    print('<label>Oggetto</label>');
    print('<textarea cols="60" rows="4" wrap="PHYSICAL" id="clsTestataDocumento_Oggetto" name="clsTestataDocumento[Oggetto]"
        dojoType="dijit.form.Textarea">'. (isSet($_POST['clsTestataDocumento']['Oggetto']) ? $_POST['clsTestataDocumento']['Oggetto'] : '') .'</textarea>');
    print('<br />');
    print('<label>Note</label>');
    print('<textarea  cols="60" rows="4" id="clsTestataDocumento_Note" name="clsTestataDocumento[Note]"
        dojoType="dijit.form.Textarea">'. (isSet($_POST['clsTestataDocumento']['Note']) ? $_POST['clsTestataDocumento']['Note'] : '') .'</textarea>');

    print('<br />');

    print('<label>Numero documento</label><input class=" djCodice" type="TEXT" maxlength="150" size="20" required="false" value="'.
        (isSet($_POST['clsTestataDocumento']['Numero']) ? $_POST['clsTestataDocumento']['Numero'] : '') .
        '" id="clsTestataDocumento_numeroregistrazione" name="clsTestataDocumento[Numero]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Data Documento</label><input class=" djCodice"  type="TEXT" maxlength="150" size="20" required="true" value="'.
        (isSet($_POST['clsTestataDocumento']['Data']) ? $_POST['clsTestataDocumento']['Data'] : '') .'"
        id="clsTestataDocumento_Data" name="clsTestataDocumento[Data]" dojoType="dijit.form.DateTextBox">');
    print('<br />');
    print('<label>Data Arrivo</label><input class=" djCodice" type="TEXT" maxlength="150" size="20" required="true" value="'.
        (isSet($_POST['clsTestataDocumento']['Arrivo']) ?$_POST['clsTestataDocumento']['Arrivo'] : '') .'"
        id="clsTestataDocumento_Arrivo" name="clsTestataDocumento[Arrivo]" dojoType="dijit.form.DateTextBox">');
    print('<br />');
    print('<label>Ufficio competente</label>');
    print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=Select code CODE, description as DESCRIPTION from arc_ufficicompetenti" ' . 'jsId="protAssegnazione" ' . '></div>');
    print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="protAssegnazione"
			searchAttr="DESCRIPTION" ' .
//        ' pageSize="50" ' .
        ' autoComplete="true" ' .
        ' name="CodUfficioCompetente" ' .
        ' required="true" ' . 'id="protAssegnazione" ' .
        ' value="'. (isSet($_POST['CodUfficioCompetente']) ? ['CodUfficioCompetente'] : '') .'" ' .
        ' style="width:300px;"  queryExpr="*${0}*" ' .
        ' searchDelay="500" ' .
        ' ></div>');
    print('<br />');
    print('<div dojoType="dojo.data.ItemFileReadStore" ' .
        'url="xml/jsonSql.php?sql=select user_id as ID, trim(concat(first_name,\' \',last_name)) as DESCRIPTION from sys_users order by 2" ' . 'jsId="responsabileId" ' . '/>');

    print('<label for="RESPONSABILE_ID">Responsabile</label>');
    print('<div dojoType="dijit.form.FilteringSelect" style="width: 400px;" ID="RESPONSABILE_ID"
								store="responsabileId"
								labelAttr="DESCRIPTION"
    							queryExpr="*${0}*"
    							searchDelay="500" 
    							autocomplete="false" 
								searchAttr="DESCRIPTION"
								name="RESPONSABILE_ID" 
								value="' . (isSet($_POST['RESPONSABILE_ID']) ? $_POST['RESPONSABILE_ID'] : '') . '" ></div>');

    print('<br>');

    print('</fieldset>');

    print('<fieldset id="mittente">');

    print('<legend>Mittente/Destinatario</legend>');

    print('<label>Seleziona un Mittente/Destinatario dall\'anagrafica</label>');

    print('<div dojoType="dojox.data.QueryReadStore" ' .
                'url="xml/jsonRicercaMittente.php" ' .
                'jsId="pecRicercaMittenteStore" ' . '></div>');

    print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="pecRicercaMittenteStore"
						searchAttr="DESCRIPTION" ' .  ' autoComplete="true" ' .
                    ' required="false" ' .
                    'name="pecRicercaMittente" ' .
                    'id="pecRicercaMittente" ' .
                    ' value="" ' .
                    ' style="width:300px;"  ' .
                    ' queryExpr="*${0}*" ' .
                    ' searchDelay="500" ' .
    // ' searchDelay="1000" ' .
    '></div>');
    print('<button dojoType="dijit.form.Button" id="pecRicercaMittenteButton">Seleziona</button>');
    print('<br />');

    print('<label>Titolo</label>');

    print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=SELECT value as CODE, value as DESCRIPTION FROM sys_fields_validations WHERE field_name = \'tipo_anagrafica\' ORDER BY 1" ' . 'jsId="sel_tipo_anagrafica" ' . '></div>');
    print('<div dojoType="dijit.form.FilteringSelect"  ' .
                'store="sel_tipo_anagrafica" searchAttr="DESCRIPTION" ' .
                'name="clsTMittenteDestinatario[DesTipoAnagrafica]" ' .
                ' queryExpr="*${0}*" ' .
                'id="pec_titolo" ' .
                'required="false" ' .
                'value="'. (isSet($_POST['clsTMittenteDestinatario']['DesTipoAnagrafica']) ? $_POST['clsTMittenteDestinatario']['DesTipoAnagrafica'] : '') .'" ' .
                ' style="width:300px;" ' . '></div>');
    print('<br />');

    print('<label>Nome</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Nome']) ? $_POST['clsTMittenteDestinatario']['Nome'] : '') .'"
                        id="pec_nome"    name="clsTMittenteDestinatario[Nome]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Cognome</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="80"  required="true"  value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Cognome']) ? $_POST['clsTMittenteDestinatario']['Cognome'] : '') .'"
        id="pec_cognome" name="clsTMittenteDestinatario[Cognome]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Codice Fiscale</label>
                        <input class="djDescrizione" type="TEXT" maxlength="150" size="30" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['CF']) ? $_POST['clsTMittenteDestinatario']['CF'] : '') .'"
        id="pec_codicefiscale"   name="clsTMittenteDestinatario[CF]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Indirizzo</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="100" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Indirizzo']) ? $_POST['clsTMittenteDestinatario']['Indirizzo'] : '') .'"
                            id="pec_toponimo" name="clsTMittenteDestinatario[Indirizzo]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Località</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Localita']) ? $_POST['clsTMittenteDestinatario']['Localita'] : '') .'"
                            id="pec_localita" name="clsTMittenteDestinatario[Localita]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>C.A.P.</label>
                        <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['CAP']) ? $_POST['clsTMittenteDestinatario']['CAP'] : '') .'"
                            id="pec_cap" name="clsTMittenteDestinatario[CAP]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Comune</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Comune']) ? $_POST['clsTMittenteDestinatario']['Comune'] : '') .'"
                            id="pec_comune" name="clsTMittenteDestinatario[Comune]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Provincia</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Provincia']) ? $_POST['clsTMittenteDestinatario']['Provincia'] : '') .'"
                            id="pec_provincia" name="clsTMittenteDestinatario[Provincia]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Telefono</label>
                        <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Telefono']) ? $_POST['clsTMittenteDestinatario']['Telefono'] : '') .'"
                            id="pec_telefono" name="clsTMittenteDestinatario[Telefono]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Fax</label>
                        <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Fax']) ? $_POST['clsTMittenteDestinatario']['Fax'] : '') .'"
                            id="pec_fax" name="clsTMittenteDestinatario[Fax]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Email</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="'.
        (isSet($_POST['clsTMittenteDestinatario']['Email']) ? $_POST['clsTMittenteDestinatario']['Email'] : '') .'"
                            id="pec_email" name="clsTMittenteDestinatario[Email]" dojoType="dijit.form.ValidationTextBox">');
    print('<br />');
    print('<label>Pec</label>
                        <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="'.
        (isSet($_POST['pec']) ? $_POST['pec'] : '') .'"
                            id="pec_pec" name="pec" dojoType="dijit.form.ValidationTextBox">');

    print('</fieldset>');

    print('<button dojoType="dijit.form.Button" type="submit" name="protocollaButton" id="protocollaPecButton" value="Submit">Protocolla</button>');
    print('</div>');
    print('<div class="dbFormContainer assegnazionePec" id="assegnazioneUo" >');
    $uoSt = $db->query('Select * from arc_organizzazione WHERE code <> "ADMIN" AND valid = "Y" order by tipo DESC, code ');
    print('<legend>Assegnazione</legend>');


    $userUo = $db->query('SELECT arc_organizzazione.uoid from arc_organizzazione 
                            RIGHT JOIN user_uo_ref ON (user_uo_ref.UOID = arc_organizzazione.UOID)
                            WHERE user_uo_ref.USER_ID = :user_id order by DESCRIPTION',[
                                ':user_id' => $_SESSION['sess_uid'],
    ])->fetchAll(PDO::FETCH_COLUMN);

    while ($uo = $uoSt->fetch()) {
        print('<label>' . $uo['DESCRIPTION'] . '</label><input type="checkbox" value="' . $uo['UOID'] . '" ' .
            (array_search($uo['UOID'], $userUo) === false ? '' : 'checked') .
            ' name="uoid[' . $uo['UOID'] . ']" class="uoidchk" /><br />');
    }

    print('<br />');

    print('</div>');
    print('</div>');
    print('<div class="wsAlert" id="wsErrors"></div>');
    print('</div>');
} else {
    print('<div cass="message"><h4>Non sei abilitato alla protocollazione!</h4></div>');
}
include 'pagefooter.inc';
