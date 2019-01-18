<?php

class formUscita extends formPratica
{
    /* costruisce la form graficamente a partire dagli oggetti caricati */

    public function editMenu(){



        print ('<div>' . "\n");
        print ('<div style="float: left;">' . "\n");

        print ('<input id="selDocuments">' .
                '<button id="creaButton" type="button"  disabled="disabled" ' .
                '	onclick="return creaDaModello(\'' . $this->_FormFields['PRATICA_ID']->GetValue() . '\')" >Crea da Modello</button>');


        print ('</div>' . "\n");
        print ('<div class="editMenu">' . "\n");





        print ('</div>' . "\n");
        print ('<div style="clear: both;"></div>' . "\n");


        return $this;
    }



    function showButtonBar($mode=null)
    {
        print('<div id="praticaButtonBar" style="background-color: #FFFFCC; height:22px; padding: 2px 30px 2px 30px;">');
        print('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" style="float:left;" />');
        if ($this->_FormFields['MODELLO']->GetValue() > ''
            and $this->_FormFields['ESITO_ID']->GetValue() > ''
            and $this->_FormFields['NUMEROREGISTRAZIONE']->GetValue() > '') {
            print('<input type="submit" value="Chiudi Procedimento" name="chiusura" class="buttons"
                    style="float:left;background-color:red; color: white;" />');
        }

        //print('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
        print('</div>');
    }

    protected function editMain()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Pratica" selected="' . $this->isPaneSelected('main') .  '" >');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");

        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['PRATICA_ID']->showDivField();
        $this->_FormFields['DATAREGISTRAZIONE']->showDivField();



        $this->_FormFields['NUMEROREGISTRAZIONE']->showDivField();
        $this->_FormFields['MODELLO']->showDivField();
        print('<label for="DATAARRIVO">Data ricevutra comunicazione</label>');
        print('<input dojoType="dijit.form.DateTextBox"
							type="text" name="DATAARRIVO"
							id="_DATAARRIVO"
							value="' . $this->_FormFields['DATAARRIVO']->GetValue() . '" ><BR />');
        print('<label for="USCITA">Chiuso il</label>');
        print('<input dojoType="dijit.form.DateTextBox"
							type="text" name="USCITA"
							id="_USCITA"
                            disabled="true"
							value="' . $this->_FormFields['USCITA']->GetValue() . '" ><BR />');


        $this->_FormFields['FUNZIONARIO']->showDivField();
        $this->_FormFields['FIRMA']->showDivField();
        $this->_FormFields['SCADENZA']->dispDivField();
        $this->_FormFields['RESPONSABILE']->showDivField();
//        $this->_FormFields['RESPONSABILE_ID']->showDivField();
//        $this->_FormFields['FALDONE']->showDivField();
        // $this->_FormFields['ESITO_ID']->showDivField();

        print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?sql=select * from arc_esiti" ' . 'jsId="testJsonPhp" ' . '/>');
        print('<label for="ESITO_ID">Esito</label>');
        print('<div dojoType="dijit.form.FilteringSelect" style="width: 400px;" ID="SEL_ESITO"
								store="testJsonPhp"
								labelAttr="DESCRIPTION"
								searchAttr="DESCRIPTION"
								name="ESITO_ID" ' . 'value="' . $this->_FormFields['ESITO_ID']->GetValue() . '" ' . '></div>');

        print('<br>');


        if ($this->_FormFields['SCADENZA']->GetValue() > ' ') {

            $alertDays = dbselect('select allarme from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue());

            $dateStart = strtotime($this->_FormFields['DATAREGISTRAZIONE']->GetValue());
            $dateEnd = strtotime($this->_FormFields['SCADENZA']->GetValue());

            $dateDiff = $dateEnd - $dateStart;
            $fullDays = floor($dateDiff / (60 * 60 * 24));

            if ($this->_FormFields['USCITA']->GetValue()) {
                $actualDays = floor((strtotime($this->_FormFields['USCITA']->GetValue()) - $dateStart) / (60 * 60 * 24));
            } else {
                $actualDays = floor((time() - $dateStart) / (60 * 60 * 24));
            }

            if ($actualDays > $fullDays) {
                $maxVal = $actualDays;
                $advVal = $fullDays;
                $bgColor = 'red';
            } else {
                if (($actualDays + $alertDays['ROWS'][0]['allarme']) >= $fullDays) {
                    $bgColor = 'yellow';
                } else {
                    $bgColor = 'lime';
                }
                $maxVal = $fullDays;
                $advVal = $actualDays;
            }

            print('<LABEL>Avanzamento</LABEL><div style="width:340px;  background: ' . $bgColor . ' none repeat;" ' . 'annotate="true"
								  		maximum="' . $maxVal . '" id="setTestBar" ' . 'progress="' . $advVal . '" ' . 'label="Prova giorni" ' . 'dojoType="dijit.ProgressBar">' . '<script type="dojo/method" event="report">' . '	var test = dojo.query(".dijitProgressBarFull","setTestBar"); ' . '   return dojo.string.substitute("Gg. Trascorsi ' . $actualDays . ' di ' . $fullDays . '", [this.progress, this.maximum]);
										  </script>
								  		 </div><br>');
        }
        print('</fieldset>' . "\n");
        print('</div>');
    }


    protected function protocollaUscita()
    {



        $isProtocollatore = Db_Pdo::getInstance()->query('SELECT * from sys_responsabilities resp
            RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
            WHERE surr.user_id IN (:admin, :user_id) AND resp.description = "Protocollazione"',array(
        	':admin' => 1,
            ':user_id' => $_SESSION['user_id']
        ))->fetch();

        if($isProtocollatore){


            print('<div dojoType="dijit.layout.ContentPane" id="protocollaUscita" title="Protocollazione" selected="' . $this->isPaneSelected('protocollazione') .  '" >');
            print('<div class="dbFormContainer protocollazione">');
            print('<div dojoType="dijit.form.Form" jsId="formProtocollazione" id="formProtocollazione" encType="multipart/form-data" action="" method="POST">');
            print('<input type="hidden" maxlength="150" size="10" value="' . $this->_FormFields['PRATICA_ID']->getValue() . '"
                            name="ws_pratica_id" >');
            print('<input id="ws_tipologia" type="hidden" maxlength="150" size="10" value="U" name="ws_protocollazione" >');
            print('<fieldset><legend>Pratica</legend>');

            // Titolario
//            $modello = Db_Pdo::getInstance()->query('SELECT * FROM arc_modelli WHERE modello = :modello', array(
//                ':modello' => $this->_FormFields['MODELLO']->getValue()
//            ))
//            ->fetch();
//            $titolario = array(
//                'ClasseTitolario' => $modello['CLASSIFICAZIONE'],
//                'DesTitolario' => $modello['DESCRIPTION']
//            );
//            print('<label>Titolario</label>
//                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="' . $modello['CLASSIFICAZIONE'] . '" id="ClasseTitolario" name="ClasseTitolario" dojoType="dijit.form.ValidationTextBox"> -
//                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="40" value="' . $modello['DESCRIPTION'] . '" id="DesTitolario" name="DesTitolario" dojoType="dijit.form.ValidationTextBox">');
            print('<label for="classifica" >Classifica</label>

            
            <div dojoType="dojo.data.ItemFileReadStore"
                    url="xml/jsonSql.php?nullValue=Y&sql=select distinct am.MODELLO,concat(am.classificazione,\'-\',am.description) as DESCRIPTION, am.classificazione  from arc_modelli am order by 2"
                    jsId="classificaStore" >
            </div>');

            print('<div dojoType="dijit.form.FilteringSelect"
                    store="classificaStore"
					searchAttr="DESCRIPTION"
                    name="classifica"
                    required="true"
                    readonly="true"
                    id="classifica"
                    value="'. $this->_FormFields['MODELLO']->getValue()  .'"
                    style="width:400px;"
                    queryExpr="*${0}*"
                    searchDelay="1000"
                     ></div>');

            print('<br />');

            print('<label for="classifica2" >4° Livello</label>

            <div dojoType="dojo.data.ItemFileReadStore"
                    url="xml/livelloStore.php?modello=' . $this->_FormFields['MODELLO']->getValue() .  '"
                    jsId="livelloStore" >
            </div>

            <input id="classifica2" value="" />
            <br />');

            print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
                'url="xml/fascicoli.json" ' .
                'jsId="fascicoloStore" ' .
                '></div>');

            print('<label>Fascicolo</label>
           <input id="fascicolo" value="" />');
            print('<br />');




            // Testata Documento
            print('<label>Oggetto</label>');
            print('<textarea  id="clsTestataDocumento_Oggetto" class="w600"  
                                    name="clsTestataDocumento[Oggetto]" 
                                    dojoType="dijit.form.Textarea">' . $this->_FormFields['COMUNEOGG']->getValue() . '</textarea>');
            print('<br />');
            print('<label>Data Protocollazione</label><input class=" djCodice" type="TEXT" maxlength="150" size="20" value="' . date('d/m/Y') . '" id="clsTestataDocumento_Data" name="clsTestataDocumento[Data]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Note</label>');
            print('<textarea  name="clsTestataDocumento[Note]" class="w600" dojoType="dijit.form.Textarea">' . $this->_FormFields['NOTE']->getValue() . '</textarea>');
            print('<br />');
            print('<label>Ufficio competente</label>');
            print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=Select code as CODE, description as DESCRIPTION from arc_ufficicompetenti" ' . 'jsId="protAssegnazione" ' . '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="protAssegnazione"
			searchAttr="DESCRIPTION" ' .
//        ' pageSize="50" ' .
                ' autoComplete="true" ' .
                ' name="CodUfficioCompetente" ' .
                ' required="true" ' . 'id="protAssegnazione" ' .
                ' value="" ' .
                ' style="width:300px;"  queryExpr="*${0}*" ' .
                ' searchDelay="500" ' .
                ' ></div>');



            print('</fieldset>');
            // Mittente
            $i = 0;
            print('<fieldset><legend>Mittente/Destinatario</legend>');

            print('<label>Escludi indirizzo dal protocollo</label><input class="escludiDestinatario" name="clsTMittenteDestinatario[' . $i . '][escludi]" dojoType="dijit.form.CheckBox" value="1" >');
            print('<br />');
            print('<label>Titolo</label>');

            print('<div dojoType="dojo.data.ItemFileReadStore" ' .
                'url="xml/jsonSql.php?nullValue=N&sql=SELECT value as CODE, value as DESCRIPTION FROM sys_fields_validations WHERE field_name = \'tipo_anagrafica\' ORDER BY 1" ' .
                'jsId="sel_tipo_anagrafica" ' . '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' .
                'store="sel_tipo_anagrafica" searchAttr="DESCRIPTION" ' .
                'name="clsTMittenteDestinatario[' . $i . '][DesTipoAnagrafica]" ' .
                ' queryExpr="*${0}*" ' .
                'id="pec_titolo" ' .
                'required="false" ' .
                'value="'. $this->_FormFields['TITOLO']->getValue() .'" ' .
                ' style="width:300px;" ' . '></div>');
            print('<br />');


            print('<br />');
            print('<label>Nome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['NOME']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Nome]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Cognome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['COGNOME']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Cognome]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Codice Fiscale</label>
                            <input class="djDescrizione" type="TEXT" maxlength="150" size="30" value="' . $this->_FormFields['CODICEFISCALE']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][CF]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Indirizzo</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="100" value="' . $this->_FormFields['TOPONIMO']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Indirizzo]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Località</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['LOCALITA']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Localita]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>C.A.P.</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="' . $this->_FormFields['CAP']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][CAP]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Comune</label>
                            <input class="djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['COMUNE']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Comune]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Provincia</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['PROVINCIA']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Provincia]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Telefono</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['TELEFONO']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Telefono]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Fax</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['FAX']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Fax]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Email</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['EMAIL']->getValue() . '"
                                name="clsTMittenteDestinatario[' . $i . '][Email]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Per Conoscenza</label><input name="clsTMittenteDestinatario[' . $i . '][PerConoscenza]" dojoType="dijit.form.CheckBox" value="1" >');
            print('</fieldset>');
            $praticheRel = Db_Pdo::getInstance()->query(
                    'select * from pratiche_fascicoli pf1
                        right join pratiche_fascicoli pf2 ON (pf2.fascicolo_id = pf1.fascicolo_id)
                         where pf1.pratica_id = :pratica_id ',
                    array(
                        ':pratica_id' => $this->_FormFields['PRATICA_ID']->getValue()
                    ))
                ->fetchAll();

            $arrayPratiche = array();
            if ($praticheRel) {
                foreach ($praticheRel as $pratiche) {
                    foreach ($praticheRel as $pratiche) {
                        $arrayPratiche[] = $pratiche['pratica_id'];
                    }
                }
            } else {
                $arrayPratiche[] = $this->_FormFields['PRATICA_ID']->getValue();
            }

            $altreDestinazioni = Db_Pdo::getInstance()->query('SELECT * FROM arc_destinazioni WHERE pratica_id in (' . implode(',', $arrayPratiche) . ')');

            while ($destinazione = $altreDestinazioni->fetch()) {
                $i ++;
                print('<fieldset><legend>Altra Destinazione</legend>');
                print('<label>Escludi indirizzo dal protocollo</label><input class="escludiDestinatario" name="clsTMittenteDestinatario[' . $i . '][escludi]" dojoType="dijit.form.CheckBox" value="1" >');
                print('<br />');
                print('<label>Titolo</label>');

                print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="sel_tipo_anagrafica"
							searchAttr="DESCRIPTION" ' . 'name="clsTMittenteDestinatario[' . $i . '][DesTipoAnagrafica]" ' . 'id="clsTMittenteDestinatario[' . $i . '][DesTipoAnagrafica]" ' . '
                value="' .  $destinazione['TITOLO'] . '" ' . ' style="width:300px;" ' . '></div>');
                print('<br />');

                print('<label>Nome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $destinazione['NOME'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Nome]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Cognome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $destinazione['COGNOME'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Cognome]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Codice Fiscale</label>
                            <input class="djDescrizione" type="TEXT" maxlength="150" size="30" value="' . $destinazione['CODICEFISCALE'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][CF]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Indirizzo</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="100" value="' . $destinazione['TOPONIMO'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Indirizzo]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Località</label>
                            <input class="readonly djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $destinazione['LOCALITA'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Localita]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>C.A.P.</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="' . $destinazione['CAP'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][CAP]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Comune</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $destinazione['COMUNE'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Comune]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Provincia</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $destinazione['PROVINCIA'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Provincia]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Telefono</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $destinazione['TELEFONO'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Telefono]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Fax</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $destinazione['FAX'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Fax]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Email</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $destinazione['EMAIL'] . '"
                                name="clsTMittenteDestinatario[' . $i . '][Email]" dojoType="dijit.form.ValidationTextBox">');
                print('<br />');
                print('<label>Per Conoscenza</label><input name="clsTMittenteDestinatario[' . $i . '][PerConoscenza]" dojoType="dijit.form.CheckBox" value="1" >');
                print('</fieldset>');
            }

            print('<button dojoType="dijit.form.Button" type="submit" name="protocollaButton" id="protocollaButton" value="Submit">Protocolla</button>');
            print('</div>');
            print('</div>');
            print('<div class="wsAlert" id="wsErrors"></div>');
            print('</div>');

        }
        return $this;
    }

    function editOrganizzazione()
    {
        if (dbselect('select * from user_uo_ref where user_id = ' . $_SESSION['sess_uid'] . ' and uoid = 1')) {
            print('<div dojoType="dijit.layout.ContentPane" title="Unit&agrave; Organizzative" id="unitaOrganizzative" selected="' . $this->isPaneSelected('uo') .  '"  >
						<div dojoType="dijit.form.Form" jsId="formUnitaOrganizzative" id="formUnitaOrganizzative" encType="multipart/form-data" action="" method="">
						    <script type="dojo/method" event="onReset">
						        return true;
						    </script>
						    <script type="dojo/method" event="onSubmit">
						        if (this.validate()) {
						        	data = this.attr(\'value\');
									inserisciUnitaOrganizzativa(data,' . $this->_FormFields['PRATICA_ID']->GetValue() . ');
						            return false;
						        } else {
						            alert(\'Mancano dati - correggi e aggiorna!\');
						            return false;
						        }
						        return false;
						    </script>
						    <div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=select UOID, DESCRIPTION from arc_organizzazione  where uoid <> 1" ' . 'jsId="selUOID" ' . '>
							</div>
							<div style="width:200px;" dojoType="dijit.form.FilteringSelect" ID="addUOID"
										store="selUOID"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="UOID" >
							</div>
							<button dojoType="dijit.form.Button" type="submit" name="submitButton"
					    		value="Submit">
					        Aggiungi Unit&agrave; Organizzativa
					    	</button>
					</div>
					<div dojoType="dijit.layout.ContentPane" id="dispUnitaOrganizzative" style="height:80%;"
						href="djGetUnitaOrganizzative.php?praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >
					</div>
				</div>');
        }
    }

    function editForm()
    {


        /* Form container */
        print('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

        print('<!-- Form open -->');
        print("\n");
        $this->_FormFields['ZONA']->hideField();

        $this->formMessageShow();
        $this->formAttachmentsShow();
        // $this->showButtonBar(FALSE);

        print('<div style="background-color: azure; font-size: 1.5em; display: block">');
        print('<div style="float:left">' . $this->GetFormTitle() . '</div>'); // Protocollo data e oggetto
        print('<div style="float:right">' . $this->GetLastUpdate() . '</div>'); // Utente e data ultimo update
        print('</div><div style="clear: both"></div> ');


        print($this->GetFormHeader());
        $this->showButtonBar();
        // Pane Container
        print('<div id="praticheTabs"  dojoType="dijit.layout.TabContainer"
							style="width:100%; height:100%; margin:0px;" >');

        $this->editMain();
        $this->praticheFascicolo();
        $this->editOggetto();
        $this->editProprietario();
        $this->editMittente();
        $this->editAltriDestinatari();
        $this->editTitolazione();

        $this->editVincoli();
        $this->editIstruttoria();
        $this->editIntegrazioni();
        // Skede dipendenti dal modello
        if ($this->_FormFields['MODELLO']->GetValue() > '') {
            if ($tipoPraticaResult = dbselect('select * from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue())) {
                if ($tipoPraticaResult['ROWS'][0]['SK_CSERVIZI'] == 'Y') {
                    $this->editConferenzaServizi();
                }
                if ($tipoPraticaResult['ROWS'][0]['SK_PAESAGGIO'] == 'Y') {
                    $this->editPaesaggio();
                }
                if ($tipoPraticaResult['ROWS'][0]['SK_CONTRIBUTI'] == 'Y') {
                    $this->editContributi();
                }
            } else {
                print('<div dojoType="dijit.layout.ContentPane" title="Errori">');
                print('<h1>Errore nel Tipo di pratica</h1>' . "\n");
                print('</div>');
            }
        }

        $this->editOrganizzazione();
        $this->editUploads();
        $this->editPecfiles();
        print('</span>');

        if ($this->_FormFields['MODELLO']->GetValue() > ''
            and $this->_FormFields['ESITO_ID']->GetValue() > ''
            and $this->_FormFields['NUMEROREGISTRAZIONE']->GetValue() == '') {
            $this->protocollaUscita();
        }

         if($this->_FormFields['NUMEROREGISTRAZIONE']->GetValue() > ''
                AND $this->_FormFields['USCITA']->GetValue()  >  ''
            ){
            if(!($this->_FormFields['MAIL_SENT_ID']->getValue() > '')){
                $this->sendPec();
            }
        }



        print('</div>');

        print("\n");
        print("<br />\n");
        print('<div id="message"></div>' . "\n");
        // $this->showButtonBar(FALSE);
        print('</form>' . "\n");
        print('</div>' . "\n");
    }
    protected function editPecfiles()
    {
        print('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC" selected="' . $this->isPaneSelected('pec') .  '" >');
        print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djGetEmlpecFile.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '&type=PEC" >');
        print('</div>');
        print('</div>');
    }
    function displayForm()
    {

        print('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");
        print($this->GetFormHeader());
        //print('<form id="99" name="PRATICHE" method="POST" enctype="multipart/form-data" action="?PRATICA_ID=' . $this->GetFormFieldValue('PRATICA_ID') . '">');
        // Titolo
        print('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        $this->showButtonBar();
        print('<div id="praticheTabs" dojoType="dijit.layout.TabContainer"
					style="width:100%; height:100%; margin:0px;">');
        print('<div dojoType="dijit.layout.ContentPane" title="Pratica" doLayout="false">');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['PRATICA_ID']->showDivField();
        $this->_FormFields['DATAREGISTRAZIONE']->SetShowed('R');
        $this->_FormFields['DATAREGISTRAZIONE']->dispDivField();
        $this->_FormFields['NUMEROREGISTRAZIONE']->SetShowed('R');
        $this->_FormFields['NUMEROREGISTRAZIONE']->dispDivField();

        print('<label for="PRATICA_USCITA_ID">Pratica</label>');

        $modello = Db_Pdo::getInstance()->query('select * from arc_modelli where modello = :modello', array(
            ':modello' => $this->_FormFields['MODELLO']->getValue()
        ))
            ->fetch();
        print($modello['CLASSIFICAZIONE'] . ' - ' . $modello['DESCRIPTION']);
        print('<br />');

        $this->_FormFields['DATAARRIVO']->dispDivField();
        $this->_FormFields['FUNZIONARIO']->dispDivField();
        $this->_FormFields['FIRMA']->dispDivField();
        $this->_FormFields['USCITA']->dispDivField();
        $this->_FormFields['PROTUSCITA']->dispDivField();
        // Protocollazione WS

        print('<label for="PRATICA_USCITA_ID">Protocollo uscita</label>');
        if ($protoUscita = Db_Pdo::getInstance()->query('SELECT pratiche_entrata.PRATICA_USCITA_ID, pratiche_uscita.NUMEROREGISTRAZIONE, pratiche_uscita.DATAREGISTRAZIONE
		      FROM pratiche pratiche_entrata
		      RIGHT JOIN pratiche pratiche_uscita ON (
		          pratiche_uscita.pratica_id = pratiche_entrata.pratica_uscita_id
		      )
		      WHERE pratiche_entrata.pratica_id = :pratica_id', array(
            ':pratica_id' => $this->_FormFields['PRATICA_ID']->GetValue()
        ))
            ->fetch()) {
            print('<span class="protoUscita">
		                  <a href="editPratica.php?PRATICA_ID=' . $protoUscita['PRATICA_USCITA_ID'] . '" title="Apri Protocollo in Uscita">
		              ' . $protoUscita['NUMEROREGISTRAZIONE'] . ' del ' . (new Date($protoUscita['DATAREGISTRAZIONE']))->toReadable() . '</a>
	              </span>');
        } else {
            print('<span class="protoUscita">Protocollata manualmente</span>');
        }
        print('<br />');

        $this->_FormFields['SCADENZA']->dispDivField();
        // $this->_FormFields['RESPONSABILE']->dispDivField();
        $this->_FormFields['RESPONSABILE_ID']->dispDivField();
        $this->_FormFields['ESITO_ID']->dispDivField();
        $this->_FormFields['FALDONE']->showDivField();

        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Oggetto">');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");

        $this->_FormFields['DATADOCUMENTO']->dispDivField();
        $this->_FormFields['NUMERORIFERIMENTO']->dispDivField();

        $this->_FormFields['OGGETTO']->dispDivField();
        $this->_FormFields['COMUNEOGG']->dispDivField();

        $this->_FormFields['INDIRIZZO_OG']->dispDivField();
        $this->_FormFields['OGG_PROV']->dispDivField();
        $this->_FormFields['OGG_COMUNE']->dispDivField();

        $this->_FormFields['ANAGRAFICO']->dispDivField();
        $this->_FormFields['FOGLIO']->dispDivField();
        $this->_FormFields['MAPPALE']->dispDivField();
        $this->_FormFields['CONDIZIONE']->dispDivField();
        $this->_FormFields['ALLEGATINUMERO']->dispDivField();
        $this->_FormFields['NOTE']->dispDivField();
        print('</fieldset>' . "\n");
        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Proprietario">');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");

        $this->_FormFields['PNOME']->dispDivField();
        $this->_FormFields['PTOPONIMO']->dispDivField();
        $this->_FormFields['PCIVICO']->dispDivField();
        $this->_FormFields['PCAP']->dispDivField();
        $this->_FormFields['PCOMUNE']->dispDivField();
        $this->_FormFields['PPROVINCIA']->dispDivField();
        print('</fieldset>' . "\n");
        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Mittente">');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");

        $this->_FormFields['NOME']->dispDivField();
        $this->_FormFields['COGNOME']->dispDivField();
        $this->_FormFields['TITOLO']->dispDivField();
        $this->_FormFields['TOPONIMO']->dispDivField();
        $this->_FormFields['CIVICO']->dispDivField();
        $this->_FormFields['CAP']->dispDivField();
        $this->_FormFields['COMUNE']->dispDivField();
        $this->_FormFields['PROVINCIA']->dispDivField();
        $this->_FormFields['LOCALITA']->dispDivField();
        $this->_FormFields['TELEFONO']->dispDivField();
        $this->_FormFields['FAX']->dispDivField();
        $this->_FormFields['CODICEFISCALE']->dispDivField();
        $this->_FormFields['EMAIL']->dispDivField();
        print('</fieldset>' . "\n");
        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Altri Destinatari" id="altriDestinatari">');
        print('<div dojoType="dijit.layout.ContentPane" id="dispDestinazioni" href="djGetDestinazioni.php?praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
        print('</div>');
        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Vincoli">');
        include ('skVincoli.inc');
        print('</div>');

        print('<div dojoType="dijit.layout.ContentPane" title="Istruttoria">');
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['ISTR01']->dispDivField();
        $this->_FormFields['ISTR02']->dispDivField();
        $this->_FormFields['ISTR03']->dispDivField();
        print('</fieldset>' . "\n");
        print('</div>');
        print('<div dojoType="dijit.layout.ContentPane" title="Integrazioni">');
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['NOTE01']->dispDivField();
        $this->_FormFields['NOTE02']->dispDivField();
        print('</fieldset>' . "\n");
        print('</div>');

        if ($this->_FormFields['MODELLO']->GetValue() > '') {

            if ($tipoPraticaResult = dbselect('select * from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue())) {

                if ($tipoPraticaResult['ROWS'][0]['SK_PAESAGGIO'] == 'Y') {
                    print('<div dojoType="dijit.layout.ContentPane" title="Paesaggistica">');
                    print('<fieldset style="border:none">' . "\n");
                    $this->_FormFields['PAE_DATA_PARERE']->dispDivField();
                    $this->_FormFields['PAE_LOC_INTERVENTO']->dispDivField();
                    $this->_FormFields['PAE_VAL_NCONDIVISE']->dispDivField();
                    $this->_FormFields['PAE_DESC_INCOMPATIBILITA']->dispDivField();
                    $this->_FormFields['PAE_MOTIVAZIONI']->dispDivField();
                    $this->_FormFields['PAE_NOTE_PRESCRIZIONI']->dispDivField();
                    $this->_FormFields['PAE_INTEGRAZIONI']->dispDivField();
                    print('</fieldset>' . "\n");
                    print('</div>');
                }
                if ($tipoPraticaResult['ROWS'][0]['SK_CONTRIBUTI'] == 'Y') {
                    print('<div dojoType="dijit.layout.ContentPane" title="Contributi">');
                    print('<fieldset style="border:none">' . "\n");
                    $this->_FormFields['CONTRIBUTI']->dispDivField();
                    $this->_FormFields['CONT_RIFAUTLAV']->dispDivField();
                    $this->_FormFields['CONT_TIPINT']->dispDivField();
                    $this->_FormFields['CONT_TIPNONAMM']->dispDivField();
                    $this->_FormFields['CONT_DATAIST']->dispDivField();
                    print('<div dojoType="dijit.layout.ContentPane" id="dispContributi" href="djGetContributi.php?display=Y&praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
                    print('</div>');

                    print('</fieldset>' . "\n");
                    print('</div>');
                }
            } else {
                print('<div dojoType="dijit.layout.ContentPane" title="Errori">');
                print('<h1>Errore nel Tipo di pratica</h1>' . "\n");
                print('</div>');
            }
        }
        $this->dispUploads();
        $this->dispPecfiles();
        print('</div>');

        print("\n");
        print("<br />\n");

        // $this->showButtonBar(FALSE);
        print('</form>' . "\n");
        print('</div>' . "\n");
    }

    protected function dispPecfiles()
    {

        return true;
    }




    protected function sendPec(){

        $body = 'Ns protocollo: ' . $this->_FormFields['NUMEROREGISTRAZIONE']->GetValue() . ' del ' .
                (new Date($this->_FormFields['DATAREGISTRAZIONE']->GetValue()))->toReadable() . "\n" .
                            ($this->_FormFields['COMUNEOGG']->GetValue() > '' ?
                                $this->_FormFields['COMUNEOGG']->GetValue() :
                                $this->_FormFields['OGGETTO']->GetValue());

        $toAddress = array();



        if($this->_praticaObj->pec){
            $pecMail = end($this->_praticaObj->pec);
            $pecFile = PEC_PATH . DIRECTORY_SEPARATOR . $pecMail['PEC_ID'] . '_pec_' . $pecMail['MAIL_HASH'] . '.eml' ;

            $Parser = new displayMail();
            $Parser->setText(file_get_contents($pecFile));


            $datiCertDom = new DOMDocument();
            $datiCertDom->loadXML($Parser->getAttachedFile('daticert.xml'));
            $datiCertXpath = new DOMXPath($datiCertDom);
            $mittente = $datiCertXpath->query('//postacert/intestazione/mittente');
            $subject = 'Re: ' . $datiCertXpath->query('//postacert/intestazione/oggetto')->item(0)->nodeValue;
            $toAddress[] = $mittente->item(0)->nodeValue;


            // Salvo SuapEnte.xml in tmp per allegarlo alla PEC
            if($suapente=$Parser->getAttachedFile('suapente.xml')){
                $suapEnteDom = new DOMDocument();
                $suapEnteDom->loadXML($suapente);
                $suapEnteXpath = new DOMXPath($suapEnteDom);
                if($oggetto = $suapEnteXpath->query('//ns2:cooperazione-suap-ente/intestazione/oggetto-comunicazione')){
                    $oggetto->item(0)->nodeValue = 'Re: ' . $oggetto->item(0)->nodeValue;
                    $subject = 'Re: ' . $oggetto->item(0)->nodeValue;
                }
                if($testoComunicazione = $suapEnteXpath->query('//ns2:cooperazione-suap-ente/intestazione/testo-comunicazione')){
                    $testoComunicazione->item(0)->nodeValue = $body;
                }
                if($elements = $suapEnteXpath->query('//ns2:cooperazione-suap-ente/allegato')){
                    for ($i = $elements->length; --$i >= 0; ) {
                        $href = $elements->item($i);
                        $href->parentNode->removeChild($href);
                    }
                }

                $suapEnteDom->save(TMP_PATH . DIRECTORY_SEPARATOR . $this->_FormFields['PRATICA_ID']->GetValue() . '_entesuap.xml');
            }
        } else {
            $subject = $this->_FormFields['COMUNEOGG']->GetValue();
        }




        $toAddress = [];
        $ccAddress = [];

        $toAddress[] = $this->_FormFields['EMAIL']->GetValue();
        if($this->_praticaObj->destinatari){
            foreach ($this->_praticaObj->destinatari as $destinatario) {
                if(!empty($destinatario['EMAIL']) or !empty($destinatario['PEC'])){
                    $toAddress[] = !empty($destinatario['PEC']) ? $destinatario['PEC'] : $destinatario['EMAIL'];
                }
            }
        }


        print('<div dojoType="dijit.layout.ContentPane" title="Risposta PEC" id="rispostaPec" selected="' . $this->isPaneSelected('sendpec') .  '" >');

            print('<div dojoType="dijit.form.Form" jsId="sendPec" id="sendPec" encType="multipart/form-data" action="" method="">');
                print('<script type="dojo/method" event="onSubmit">');
                print('if (this.validate()) {
        						sendPecMail(this,' . $this->_FormFields['PRATICA_ID']->GetValue() . ');
        			            return false;
        			        } else {
        			            alert(\'Mancano dati - correggi e aggiorna!\');
        			            return false;
        			        }
        			        return false;');
                print('</script>');
                print('<div class="sendPec">');
                    print('<table class="sendPec">');
                    // Indirizzo
                    print('<tr><td class="label">A</td><td class="address" ><input type="text" class="pecInput" name="ToAddress"
                        value="' . implode(',', $toAddress) . '"
                        required="true" dojoType="dijit.form.ValidationTextBox"/></td></tr>');
                    // Per Conoscenza
                    print('<tr><td class="label">CC</td><td><input type="text" class="pecInput" name="CCAddress"
                        value="' . implode(',', $ccAddress) . '"
                        required="false" dojoType="dijit.form.ValidationTextBox"/></td></tr>');
                    // Da From
                    print('<tr><td class="label">Da</td><td><input type="text" class="pecInput" name="FromAddress" value="');
                    print(strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'giacomo.fonderico@gmail.com' : PEC_USERNAME);
                    print('" required="true" dojoType="dijit.form.ValidationTextBox"/></td></tr>');
                    // Oggetto
                    print('<tr><td class="label">Oggetto</td><td><input type="text" class="pecInput" name="Subject" value="' . trim($subject) . '"
                         required="true" dojoType="dijit.form.ValidationTextBox"/></td></tr>');

                    print('<tr><td colspan="2" class="address" >
                            <textarea id="bodyMessage" dojoType="dijit.form.Textarea"
                            name="bodyMessage" class="pecInput" >');


                    print( $body . "\n\n----------\n\n" . (isSet($_SESSION['config']['KEY_FIRMA']) ? $_SESSION['config']['KEY_FIRMA'] : '') );
                    // print($datiCertXpath->query('//postacert/intestazione/')->item(0)->nodeValue);

                    print('</textarea></td></tr>');
                    print('<tr><td colspan="2" class="address" >');

                    if($this->_praticaObj->fascicolo){
                        foreach ($this->_praticaObj->fascicolo as $fascicoloArray){
                            $fascicolo_id = $fascicoloArray['fascicolo_id'];
                            break;
                        }
                    }

                    // Possibili attachments
                    $uploadsFilesQuery = 'SELECT upload_id, description, filename FROM uploads WHERE pratica_id IN
                                            (SELECT pratica_id FROM pratiche_fascicoli WHERE fascicolo_id = :fascicolo_id ) 
                                            or pratica_id = :pratica_id';
                    $uploadFiles = Db_Pdo::getInstance()->query($uploadsFilesQuery,array(
                        ':pratica_id' => $this->_FormFields['PRATICA_ID']->GetValue(),
                        ':fascicolo_id' => $fascicolo_id
                    ))->fetchAll();

                    print('<div class="mailCert"><ul class="fa-ul" >Seleziona i Files da Allegare:<br />');
                    $filesClass = array(
                        'bin' => 'file-archive-o',
                        'pdf' => 'file-pdf-o',
                        'txt' => 'file-text-o',
                        'xls' => 'file-excel-o',
                        'xml' => 'code',
                        'jpg' => 'file-image-o',
                        'jpeg' => 'file-image-o',
                        'png' => 'file-image-o',
                        'gif' => 'file-image-o',
                        'doc' => 'file-word-o',
                        'odt' => 'file-word-o',
                        'docx' => 'file-word-o',
                        'zip' => 'file-zip-o'
                    );


//

        $attachIndex = 0;
                    foreach($uploadFiles as $attachment) {
                        $fileName = $attachment['filename'];
                        if($fileName > ''){
                            if(preg_match('|iso-8859|i',$fileName)){
                                mb_internal_encoding('ISO-8859-1');
                                $fileName = str_replace("_"," ", mb_decode_mimeheader($fileName));
                            }
                            print('<li >
            			         <i onclick="window.open(\'get_file.php?wk_inline=Y&fid='. $attachment['upload_id'] .'\');" class="mouseOn fa-li fa fa-'. $filesClass[pathinfo($fileName)['extension']] .'" > </i><span id="attachment_' .
                                $attachment['upload_id'] . '" >' . $fileName . '</span>' .
                                '<span dojoType="dijit.Tooltip" connectId="attachment_' . $attachment['upload_id'] . '" style="display:none;" > ' .
                                '<img src="thumbnailNew.php?src='. $attachment['upload_id'] . '_' . $attachment['filename'] . '&maxw=300"></span>' .
                                '<input type="checkbox" value="' . $attachment['upload_id'] .
                                    '" name="attachment[' . $attachIndex . ']" checked />'.
                                '</li>');
                        }
                        $attachIndex++;
                    }

                    if($this->_praticaObj->getSegnatura()){

                        print('<li ><i onclick="window.open(\'/tmp/'. $this->_FormFields['PRATICA_ID']->GetValue() . '_segnatura.xml' .'\');" 
                            class="mouseOn fa-li fa fa-'. $filesClass['xml'] .'" > </i><span id="segnaturaXml" >Segnatura.xml</span>' .
                            '</li>');
                    }

                    print('</ul></div>');


                    print('</td></tr>');
                    print('</table>');


                print('</div>');
                print('<button dojoType="dijit.form.Button" type="submit" name="submitButton" value="sendmail">Invia Pec</button>');
            print('</div>');

            print('</div>');
        print('</div>');
        return $this;


    }



    static function chiudiPratica($praticaId)
    {
        Db_Pdo::getInstance()->query('UPDATE pratiche SET uscita = now() WHERE pratica_id = :pratica_id', array(
            ':pratica_id' => $praticaId
        ));

        return;
    }
}
