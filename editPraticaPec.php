<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";
// prova
$pecId = (isSet($_GET['PEC_ID']) ? $_GET['PEC_ID'] : $_POST['PEC_ID']);

if (isset($_POST) and ! empty($_POST)) {
    if ($_POST ['NUMEROREGISTRAZIONE'] > '' and $_POST ['DATAREGISTRAZIONE'] > '') {

        $numeroRegistrazione = str_pad($_POST ['NUMEROREGISTRAZIONE'], 7, '0', STR_PAD_LEFT);
        if ($praticaId = Db_Pdo::getInstance()->query(' SELECT pratica_id FROM pratiche
                WHERE numeroregistrazione = :numeroregistrazione and dataregistrazione = :dataregistrazione', [
            ':numeroregistrazione' => $numeroRegistrazione,
            ':dataregistrazione' => (new Date($_POST ['DATAREGISTRAZIONE']))->toMysql()
        ])->fetchColumn()) {
            Db_Pdo::getInstance()->query('UPDATE arc_pratiche_pec SET pratica_id = :pratica_id WHERE pec_id = :pec_id', [
                ':pratica_id' => $praticaId,
                ':pec_id' => $_POST ['PEC_ID']
            ]);
        }
    } elseif (empty($_POST['NUMEROREGISTRAZIONE']) and empty($_POST['DATAREGISTRAZIONE']) and
            !empty($_POST['PRATICA_ID'])){
        Db_Pdo::getInstance()->query('UPDATE arc_pratiche_pec SET pratica_id = NULL WHERE pec_id = :pec_id', [
            ':pec_id' => $_POST ['PEC_ID']
        ]);
    }
}

class myDbForm extends formExtended {


    protected $_xmlArrays = array();

    protected $_mailHeader;


	public function setRead($pecId)
	{
		dbupdate('update arc_pratiche_pec SET status = \'R\' where status = "U" AND PEC_ID = '. $pecId);
		$this->_FormFields['STATUS']->SetValue(($this->_FormFields['STATUS']->GetValue() == 'U' ? 'R' : $this->_FormFields['STATUS']->GetValue()));

		return ;
	}
	/* costruisce la form graficamente a partire dagli oggetti caricati */
	function ShowForm() {
			$this->editForm();
	}

	function FormMessageShow() {
		print ('<tr><td class="DbFormMessage">');
		print ($this->GetFormMessage());
		print ('</td></tr>');
		print ("\n");
	}

	function showButtonBar($mode=null) {

		print ('<div style="background-color: #FFFFCC; height:22px; padding: 2px 30px 2px 30px;">');
		print ('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" style="float:left;" />');
		print ('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
		print ('</div>');
	}

	function editForm() {

		/**
		 * Dialog Box per uploads files
		 */
		/* Form container */

		print ('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());
		$this->showButtonBar(FALSE);

		$this->formMessageShow();
		$this->formAttachmentsShow();
		//		$this->showButtonBar(FALSE);

		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		// Pane Container
		print ('<div dojoType="dijit.layout.TabContainer" id="praticheTabs" 
							style="width:98%; height:100%; margin:0px;">');
		$this->editMain();
		$this->editPecfiles();
		print ('</form>' . "\n");
		$praticaId = $this->_FormFields['PRATICA_ID']->GetValue();
		$isProtocollatore = Db_Pdo::getInstance()->query('SELECT * from sys_responsabilities resp
            RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
            WHERE surr.user_id IN (:admin, :user_id) AND resp.description = "Protocollazione"',array(
		                ':admin' => 1,
		                ':user_id' => $_SESSION['user_id']
		            ))->fetch();
		if(empty($praticaId) and $isProtocollatore){
		    $this->protocollaPec();
		}


		print ('</div>');

		print ("\n");
		print ("<br />\n");
		print ('<div id="message"></div>' . "\n");
		//$this->showButtonBar(FALSE);





		print ('</div>' . "\n");
	}

	protected function editPecfiles() {
        $Parser = new displayMail();
		print ('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC">');
			print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" >');

			$pecId = $this->_FormFields['PEC_ID']->GetValue();
			$dispEmlFilesQuery = 'select * from arc_pratiche_pec ' .
											'where pec_id = ' . $pecId;
			if (empty($pecId)){
				print('<div class="DbFormMessage" style="margin-top: 20px; text-align: center;" >Selezionare una mail da visualizzare!</div>');
			} else {
				if(! $emlResult = dbselect($dispEmlFilesQuery)){
					print('<div class="DbFormMessage">Attenzione! File non trovato contattare l\'assistenza</div>');
				} else {

					$pecFile = PEC_PATH . '/' . $emlResult['ROWS'][0]['PEC_ID']. '_' . $emlResult['ROWS'][0]['TYPE'] . '_' .$emlResult['ROWS'][0]['MAIL_HASH'].'.eml';


					$Parser->setText(file_get_contents($pecFile));
					$Parser->viewMail($emlResult['ROWS'][0]['PEC_ID']);
					$this->_mailHeader = $Parser->getHeaders();

				}
			}
			print ('</div>');
		print ('</div>');
		if($attachments = $Parser->getAttachments()){
            $attachIndex = 0;
            foreach($attachments as $attachment) {
                $attachIndex++;
                $ext = pathinfo($attachment->filename, PATHINFO_EXTENSION);
                $filename = pathinfo($attachment->filename, PATHINFO_BASENAME);
                if(strtoupper($ext) == 'XML'){
                    $xmlView = new xmlToHtml($attachment->getContent());
                    if($xmlView->xmlWellFormed()){
                        print ('<div dojoType="dijit.layout.ContentPane" id="xml_' . $attachment->filename . '_' . $attachIndex .'" title="' . $attachment->filename . '">');
                        $this->_xmlArrays[rtrim(strtoupper($filename),'.XML')] = (new xml2array($attachment->getContent()))->getResult();
                        print('<div class="pecXml">' . PHP_EOL);
                        $xmlView->getHtml();
                        print('</div>' . PHP_EOL);
                        print('</div>' . PHP_EOL);
                    }

                }

                // print('<li class="'. strtolower($attachment->extension) .'" onclick="getPecAttachment('.$_GET['PEC_ID'].','.$attachIndex.')" style="cursor: pointer">' .  $fileName . '</li>');
            }

        }
	}

	function editMain() {
		$dataArrivo = $this->_FormFields['DATAARRIVO']->GetValue() > '' ? ' and dataregistrazione >= '.$this->_FormFields['DATAARRIVO']->GetValue() : '';

		$this->_FormFields['PRATICA_ID']->setSqlQuery('select PRATICA_ID, substring(concat(numeroregistrazione,\'-\',dataregistrazione,\'-\',oggetto),1,60) as DESCRIPTION
					from pratiche where 1 ' . $dataArrivo);
		print ('<div dojoType="dijit.layout.ContentPane" title="Pratica">');
//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");

		print ('<fieldset style="border:none">' . "\n");
		$this->_FormFields['PEC_ID']->showDivField();
		$this->_FormFields['PRATICA_ID']->showDivField();

		$this->_FormFields['NUMEROREGISTRAZIONE']->showDivField();



		$this->_FormFields['DATAREGISTRAZIONE']->showDivField();

		$this->_FormFields['DATAARRIVO']->dispDivField();

		$this->_FormFields['MITTENTE']->showDivField();

		$this->_FormFields['SUBJECT']->showDivField();
		if ($this->_FormFields['STATUS']->GetValue() == 'P') {
			print ('<label>');
			print ('Status');
			print ('</label>');
			print ('<span >');
			print ('Protocollato');
			print ('</span>');
			print ('<br/>');
			print ("\n");
		} else {
			$this->_FormFields['STATUS']->showDivField();
		}

		print ('</fieldset>' . "\n");
		print ('</div>');

	}

	protected function protocollaPec()
	{
	    $indirizzoPec = null;
	    $indirizzoEmail = null;
	    $isProtocollatore = Db_Pdo::getInstance()->query('SELECT * from sys_responsabilities resp
            RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
            WHERE surr.user_id IN (:admin, :user_id) AND resp.description = "Protocollazione"',array(
	                    ':admin' => 1,
	                    ':user_id' => $_SESSION['user_id']
	                ))->fetch();

        if($isProtocollatore){
            print('<div dojoType="dijit.layout.ContentPane" id="protocollaPec" title="Protocollazione">');
            print('<div dojoType="dijit.form.Form" jsId="formProtocollazionePec" id="formProtocollazionePec"
                    encType="multipart/form-data" action="" method="">');

            print('<div class="dbFormContainer protocollazione" id="boxProtocollazione">');


            print('<legend>Dati Protocollo</legend>');

            $suapArray = isset($this->_xmlArrays['SUAPENTE']) ? $this->_xmlArrays['SUAPENTE'] : null;
            $daticertArray = isset($this->_xmlArrays['DATICERT']) ? $this->_xmlArrays['DATICERT'] : null;
            $indirizzoPec = $daticertArray['postacert']['intestazione']['mittente'];

            $segnaturaArray = isset($this->_xmlArrays['SEGNATURA']) ? $this->_xmlArrays['SEGNATURA'] : null;
            $oggetto = null;
            $data_documento = null;
            if(!is_null($segnaturaArray)){
                $intestazione = isSet($segnaturaArray['Segnatura']['Intestazione']) ? $segnaturaArray['Segnatura']['Intestazione'] : $segnaturaArray['Segnatura'][1]['Intestazione'];
                $oggetto = $intestazione['Oggetto'];
                $data_documento = (new Date($intestazione['Identificatore']['DataRegistrazione']))->toMysql();
                $numero_registrazione = $intestazione['Identificatore']['NumeroRegistrazione'];
                $mittente = $intestazione['Origine']['Mittente']['Amministrazione']['Denominazione'];
            }

            if(!is_null($suapArray)){
                $intestazione = $suapArray['ns2:cooperazione-suap-ente']['intestazione'];
                $impresa = $suapArray['ns2:cooperazione-suap-ente']['intestazione']['impresa'];
                $oggetto = trim($intestazione['suap-competente'] . ' ' .
                    $intestazione['protocollo-pratica-suap']['numero-registrazione'] . ' ' .
                    $intestazione['oggetto-comunicazione'] . '; Protocollo pratica:' .
                    'CCIAA_' .  $intestazione['protocollo-pratica-suap']['@codice-aoo'] . ' ' .
                    $intestazione['protocollo-pratica-suap']['@numero-registrazione'] . ' ' .
                    (new Date(substr($intestazione['protocollo-pratica-suap']['@data-registrazione'],0,10)))->toReadable() . '; Protocollo della comunicazione: ' .
                    'CCIAA_' .  $intestazione['protocollo']['@codice-aoo'] . ' ' .
                    $intestazione['protocollo']['@numero-registrazione'] . ' ' .
                    (new Date(substr($intestazione['protocollo']['@data-registrazione'],0,10)))->toReadable() . ' ' .
                    $intestazione['oggetto-pratica']) ;


                $data_documento = substr($intestazione['protocollo-pratica-suap']['@data-registrazione'],0,10);


                $numero_registrazione = $intestazione['protocollo-pratica-suap']['@numero-registrazione'];
                $mittente = $intestazione['suap-competente'];

            } elseif(is_null($oggetto)) {
                $oggetto = empty($daticertArray['postacert']['intestazione']['oggetto']) ? $this->_FormFields['SUBJECT']->GetValue() : $daticertArray['postacert']['intestazione']['oggetto'];
            }
            $email = str_replace('>', '', str_replace('<', '', $this->_mailHeader['return-receipt-to']));

            $indirizzoPec = (is_null($indirizzoPec) && $this->_FormFields['TYPE']->GetValue() == 'pec') ? $email : $indirizzoPec;
            $indirizzoEmail = (is_null($indirizzoEmail) && $this->_FormFields['TYPE']->GetValue() == 'mail') ? $email : $indirizzoEmail;


            print('<input type="hidden" maxlength="150" size="10" value="' . $_GET['PEC_ID'] . '" dojoType="dijit.form.TextBox"
                            name="ws_pec_id" >');
            // Testata Documento

            print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
                'url="xml/fascicoli.json" ' .
                'jsId="fascicoloStore" ' .
                '></div>');

            print('<label for="classifica" >Classifica</label>

            
            <div dojoType="dojo.data.ItemFileReadStore"
                    url="xml/jsonSql.php?nullValue=Y&sql=select distinct am.MODELLO,concat(am.classificazione,\'-\',am.description) as DESCRIPTION, am.classificazione  from arc_modelli am order by 2"
                    jsId="classificaStore" >
            </div>
            <input id="classifica" value="'. (isSet($_POST['classifica']) ? $_POST['classifica'] : '') .'" />');

            print('<br />');

            print('<label for="classifica2" >4° Livello</label>

            <div dojoType="dojo.data.ItemFileReadStore"
                    url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_modelli_classifica order by 1"
                    jsId="livelloStore" >
            </div>

            <input id="classifica2" value="'. (isSet($_POST['classifica']) ? $_POST['classifica'] : '') .'" />
            <br />');


            print('<label>Fascicolo</label>
           <input id="fascicolo" value="'. (isSet($_POST['fascicolo']) ? $_POST['fascicolo'] : '') .'" />');
            print('<br />');

            print('<label>Oggetto</label>');
            print('<textarea rows="4" 
                                    wrap="PHYSICAL" id="clsTestataDocumento_Oggetto" 
                                    required="true" name="clsTestataDocumento[Oggetto]" 
                                    class="djTextArea"
                                    dojoType="dijit.form.Textarea">' .
                $oggetto .
                '</textarea>');
            print('<br />');
            print('<br />');

            print('<label>Numero documento</label><input class=" djCodice" type="TEXT" maxlength="150" size="40" value="' .
                $numero_registrazione .
                '" id="clsTestataDocumento_numeroregistrazione" required="false" name="clsTestataDocumento[Numero]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Data Documento</label><input class=" djCodice" type="TEXT" maxlength="150" size="40" value="' .
                $data_documento .
                '" id="clsTestataDocumento_Data" name="clsTestataDocumento[Data]" required="true"  dojoType="dijit.form.DateTextBox">');
            print('<br />');
            print('<label>Data Arrivo</label><input class=" djCodice" type="TEXT" maxlength="150" size="40" value="' .
                (new Date($this->_FormFields['DATAARRIVO']->GetValue()))->toMysql() .
                '" id="clsTestataDocumento_Arrivo" required="true" name="clsTestataDocumento[Arrivo]" dojoType="dijit.form.DateTextBox">');
            print('<br />');

            print('<label>Ufficio competente</label>');

            print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=Select code as CODE, description as DESCRIPTION from arc_ufficicompetenti" ' . 'jsId="protAssegnazione" ' . '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' .
                'store="protAssegnazione"
				searchAttr="DESCRIPTION" ' .
//                ' pageSize="50" ' .
                ' autoComplete="true" ' .
                'name="CodUfficioCompetente" ' .
                ' required="true" ' .
                'id="protAssegnazione" ' .
                ' value="" ' .
                ' style="width:300px;" '.
                ' searchDelay="500" ' .
//                ' pageSize="50" ' .
                '></div>');
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
								name="RESPONSABILE_ID" value="" 
								></div>');

            print('<br>');

            print('</fieldset>');


            print('<br />');


            print('<legend>Mittente</legend>');

            print('<label>Seleziona un Mittente dall\'anagrafica</label>');



            print('<div dojoType="dojox.data.QueryReadStore" ' .
                'url="xml/jsonRicercaMittente.php" ' .
                'jsId="pecRicercaMittenteStore" ' .
                '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' .
                'store="pecRicercaMittenteStore"
							searchAttr="DESCRIPTION" ' .
                ' autoComplete="true" ' .
                'name="pecRicercaMittente" ' .
                ' queryExpr="*${0}*" ' .
                ' required="false" ' .
                'id="pecRicercaMittente" ' .
                ' value="" ' .
                ' style="width:300px;" '.
				' searchDelay="1000" ' .
//                ' pageSize="50" ' .
                '></div>');
            print('<button dojoType="dijit.form.Button" id="pecRicercaMittenteButton">Seleziona</button>');
            print('<br />');


            print('<label>Titolo</label>');

            print('<div dojoType="dojo.data.ItemFileReadStore" ' .
                'url="xml/jsonSql.php?nullValue=N&sql=SELECT value as CODE, value as DESCRIPTION FROM sys_fields_validations WHERE field_name = \'tipo_anagrafica\' ORDER BY 1" ' .
                'jsId="sel_tipo_anagrafica" ' .
                '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' .
                'store="sel_tipo_anagrafica"
							searchAttr="DESCRIPTION" ' .
                'name="clsTMittenteDestinatario[DesTipoAnagrafica]" ' .
                'id="pec_titolo" ' .
                ' value="" ' .
                ' style="width:300px;" '.
                '></div>');
            print('<br />');

            print('<label>Nome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value=""
                            id="pec_nome"    name="clsTMittenteDestinatario[Nome]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Cognome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . (empty($indirizzoPec) ? $indirizzoEmail : $indirizzoPec) . '"
                             id="pec_cognome" name="clsTMittenteDestinatario[Cognome]" required="true" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Codice Fiscale</label>
                            <input class="djDescrizione" type="TEXT" maxlength="150" size="30" value=""
                             id="pec_codicefiscale"   name="clsTMittenteDestinatario[CF]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Indirizzo</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="100" value=""
                                id="pec_toponimo" name="clsTMittenteDestinatario[Indirizzo]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Località</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value=""
                                id="pec_localita" name="clsTMittenteDestinatario[Localita]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>C.A.P.</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value=""
                                id="pec_cap" name="clsTMittenteDestinatario[CAP]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Comune</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value=""
                                id="pec_comune" name="clsTMittenteDestinatario[Comune]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Provincia</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value=""
                                id="pec_provincia" name="clsTMittenteDestinatario[Provincia]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Telefono</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value=""
                                id="pec_telefono" name="clsTMittenteDestinatario[Telefono]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Fax</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value=""
                                id="pec_fax" name="clsTMittenteDestinatario[Fax]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Email</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $indirizzoEmail . '"
                                id="pec_email" name="clsTMittenteDestinatario[Email]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Pec</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $indirizzoPec . '"
                                id="pec_pec" name="pec" dojoType="dijit.form.ValidationTextBox">');

            print('<br />');



            print('<button dojoType="dijit.form.Button" type="submit" name="protocollaButton" id="protocollaPecButton" value="Submit">Protocolla</button>');
            print('</div>');
            print('</div>');
            print('<div class="dbFormContainer assegnazionePec" id="assegnazionePec">');
            $uoSt = Db_Pdo::getInstance()->query('select * from arc_organizzazione WHERE
                                code <> "ADMIN" AND valid = "Y" order by tipo DESC , DESCRIPTION ');
            print('<legend>Assegnazione</legend>');

            while($uo = $uoSt->fetch()) {
                print('<label>' . $uo['DESCRIPTION'] . '</label><input type="checkbox" value="' . $uo['UOID'] . '" name="uoid[]" /><br />');
            }



            print('</div>');
            print('<div class="wsAlert" id="wsErrors"></div>');
            print('</div>');

        }

	    return $this;
	}




}


$dbKey = isset($_GET['dbKey']) ? $_GET['dbKey'] : ' where PEC_ID=' . $pecId;


$ManagedTable = new MyDbForm('ARC_PRATICHE_PEC', $_SESSION['sess_lang']);
$ManagedTable->setRead($pecId);

$ManagedTable->setAfterUpdateLocation('pecWrkProtocollazione.php');


$ManagedTable->SetFormMode("modify", stripslashes($dbKey));


include ('pageheader.inc');
print('<script type="text/javascript" src="javascript/editPraticaPec.js?' . filemtime(ROOT_PATH . DIRECTORY_SEPARATOR . 'javascript/editPraticaPec.js') .  '"></script>');





if (isSet ($display) and ($display == 'Y')) {
	$ManagedTable->displayForm();
} else {
	$ManagedTable->ShowForm();
}
print('<iframe id="printElement" style="height: 0px; width: 0px; position: absolute"></iframe>');
include ('pagefooter.inc');
?>
