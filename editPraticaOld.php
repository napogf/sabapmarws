<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";

class myHtmlETable extends htmlETable {

}

class MyDbForm extends formExtended {
	/* costruisce la form graficamente a partire dagli oggetti caricati */
	function FormMessageShow() {
		print ('<div class="DbFormMessage">');
		print ($this->GetFormMessage());
		print ('</div>');
		print ("\n");
	}
	function ShowForm() {
		if ($this->_FormFields['USCITA']->GetValue() > '' and $this->_FormFields['USCITA']->GetValue() <> '0000-00-00') {
			$this->displayForm();
		} else {
			$this->editForm();
		}
	}

	function FormPreValidation() {
		if ((is_null($_POST['ESITO_ID']) or strlen(trim($_POST['ESITO_ID'])) == 0)
			or (is_null($_POST['MODELLO']) or strlen(trim($_POST['MODELLO'])) == 0)) {
			if (strlen(trim($_POST['USCITA'])) > 0) {
				$this->SetFormMessage('Attenzione! non hai inserito l\'esito o il tipo chiudendo la Pratica' );
				return FALSE;
			}
		}
		return TRUE;
	}

	function showButtonBar($mode=null) {

		print ('<div style="background-color: #FFFFCC; height:22px; padding: 2px 30px 2px 30px;">');
		print ('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" style="float:left;" />');
		print ('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
		print ('</div>');
	}

	function editForm() {

		include('skUploads.inc');
		/* Form container */

		print ('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());
		$this->showButtonBar();
		$this->_FormFields['ZONA']->hideField();

		$this->formMessageShow();
		$this->formAttachmentsShow();
		//		$this->showButtonBar(FALSE);

		print('<div style="background-color: azure; font-size: 1.5em;">');
		print('<div style="float:left">' . $this->GetFormTitle() . '</div>'); // Protocollo data e oggetto
		print('<div style="float:right">' . $this->GetLastUpdate() . '</div>'); // Utente e data ultimo update
		print('</diV>');

		// Pane Container
		print ('<div dojoType="dijit.layout.TabContainer"
									style="width:98%; height:550px; margin:0px;">');


		$this->EditMain();
        $this->editOggetto();
        $this->editProprietario();
        $this->editMittente();
        $this->editAltriDestinatari();
        $this->editTitolazione();
        $this->editVincoli();
        $this->editIstruttoria();

		if ($this->_FormFields['MODELLO']->GetValue() > '') {

			if ($tipoPraticaResult = dbselect('select * from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue())) {
				if ($tipoPraticaResult['ROWS'][0]['SK_CSERVIZI'] == 'Y') {
				    $this->editConferenzaServizi();
				}
				if ($tipoPraticaResult['ROWS'][0]['SK_PAESAGGIO'] == 'Y') {
				    $this->editPaesaggistica();
				}
				if ($tipoPraticaResult['ROWS'][0]['SK_CONTRIBUTI'] == 'Y') {
				    $this->editContributi();
				}

			} else {
				print ('<div dojoType="dijit.layout.ContentPane" title="Errori">');
				print ('<h1>Errore nel Tipo di pratica</h1>' . "\n");
				print ('</div>');

			}

		}

		print ('</form>' . "\n");

		$this->editOrganizzazione();
		$this->editUploads();
		$this->editPecfiles();

		print ('</div>');

		print ("\n");
		print ("<br />\n");
		print ('<div id="message"></div>' . "\n");
		//$this->showButtonBar(FALSE);

		print ('</div>' . "\n");
	}

	protected function editMain(){
	    print ('<div dojoType="dijit.layout.ContentPane" title="Pratica">');
	    //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");

	    print ('<fieldset style="border:none">' . "\n");
	    $this->_FormFields['PRATICA_ID']->showDivField();
	    $this->_FormFields['DATAREGISTRAZIONE']->showDivField();
	    $this->_FormFields['NUMEROREGISTRAZIONE']->showDivField();
	    $this->_FormFields['MODELLO']->SetPostValidation('setDocuments(this.value);');
	    $this->_FormFields['MODELLO']->showDivField();
// 	    $this->_FormFields['ZONA']->showDivField();
// 	    $this->_FormFields['UFFICIO']->showDivField();
	    $this->_FormFields['DATAARRIVO']->showDivField();
	    $this->_FormFields['FUNZIONARIO']->showDivField();
	    $this->_FormFields['FIRMA']->showDivField();
	    $this->_FormFields['USCITA']->showDivField();
	    $this->_FormFields['PROTUSCITA']->showDivField();

	    if ($this->_FormFields['MODELLO']->GetValue() > '') {
	        $getModelloQuery = 'select scadenza from arc_modelli where modello =' . $this->_FormFields['MODELLO']->GetValue();
	        if ($getModelloResult = dbselect($getModelloQuery)) {
	            if ($getModelloResult['ROWS'][0]['scadenza'] == '0') {
	                $this->_FormFields['SCADENZA']->showDivField();
	            } else {
	                $this->_FormFields['SCADENZA']->dispDivField();
	            }
	        }
	    }
	    $this->_FormFields['RESPONSABILE']->showDivField();
	    //		$this->_FormFields['ESITO_ID']->showDivField();

	    print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
	        'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' .
	        'jsId="testJsonPhp" ' .
	        '/>');
	    print ('<label for="ESITO_ID">Esito</label>');
	    print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_ESITO"
										store="testJsonPhp"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="ESITO_ID" ' .
	        'value="' . $this->_FormFields['ESITO_ID']->GetValue() . '" ' .
	        '></div>');

	    print ('<br>');

	    // Project_id
	    if (!$project=dbselect('select * from arc_pratiche_prj where pratica_id = '.$this->_FormFields['PRATICA_ID']->GetValue())
	    or strlen($this->_FormFields['PROJECT_ID']->GetValue())==0) {
	        $this->_FormFields['PROJECT_ID']->showDivField();
	    } else {
	        print('<label>Progetto</label>
						<span>'. $project['ROWS'][0]['DESCRIPTION'] .'
						<div class="delete" onclick="deleteProject('.$project['ROWS'][0]['PROJECT_ID'].','.$this->_FormFields['PRATICA_ID']->GetValue().')">Elimina il progetto</div>
						</span><br>');
	        //				$this->_FormFields['PROJECT_ID']->dispDivField();
	        print('<input type="hidden" name="PROJECT_ID" value="'.$this->_FormFields['PROJECT_ID']->GetValue().'" >');
	    }

	    if ($this->_FormFields['SCADENZA']->GetValue() > ' ') {

	        $alertDays = dbselect('select allarme from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue());

	        $dateStart = strtotime($this->_FormFields['DATAARRIVO']->GetValue());
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

	        print ('<LABEL>Avanzamento</LABEL><div style="width:340px;  background: ' . $bgColor . ' none repeat;" ' .
	            'annotate="true"
											  		maximum="' . $maxVal . '" id="setTestBar" ' .
	            'progress="' . $advVal . '" ' .
	            'label="Prova giorni" ' .
	            'dojoType="dijit.ProgressBar">' .
	            '<script type="dojo/method" event="report">' .
	            '	var test = dojo.query(".dijitProgressBarFull","setTestBar"); ' .
	            '   return dojo.string.substitute("Gg. Trascorsi ' . $actualDays . ' di ' . $fullDays . '", [this.progress, this.maximum]);
													  </script>
											  		 </div><br>');

	    }
	    print ('</fieldset>' . "\n");
	    print ('</div>');


	}

	protected function editOggetto(){
	    print ('<div dojoType="dijit.layout.ContentPane" title="Oggetto">');
	    //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
	    print ('<fieldset style="border:none">' . "\n");
	    $this->_FormFields['DATADOCUMENTO']->showDivField();
	    $this->_FormFields['NUMERORIFERIMENTO']->showDivField();
	    $this->_FormFields['OGGETTO']->showDivField();
	    $this->_FormFields['COMUNEOGG']->showDivField();
	    $this->_FormFields['INDIRIZZO_OG']->showDivField();
	    $this->_FormFields['COMUNE_OG']->showDivField();
	    $this->_FormFields['FOGLIO']->showDivField();
	    $this->_FormFields['MAPPALE']->showDivField();
	    // $this->_FormFields['ANAGRAFICO']->showDivField();
	    $this->_FormFields['CONDIZIONE']->showDivField();
	    $this->_FormFields['ALLEGATINUMERO']->showDivField();
	    $this->_FormFields['NOTE']->showDivField();
	    print ('</fieldset>' . "\n");
	    print ('</div>');

	}
	protected function editProprietario(){
	    print ('<div dojoType="dijit.layout.ContentPane" title="Proprietario">');
	    //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
	    print ('<fieldset style="border:none">' . "\n");

	    $this->_FormFields['PNOME']->showDivField();
	    $this->_FormFields['PTOPONIMO']->showDivField();
	    $this->_FormFields['PCIVICO']->showDivField();
	    $this->_FormFields['PCAP']->showDivField();
	    $this->_FormFields['PCOMUNE']->showDivField();
	    $this->_FormFields['PPROVINCIA']->showDivField();
	    print ('</fieldset>' . "\n");
	    print ('</div>');

	}

	protected function editMittente(){
	    print ('<div dojoType="dijit.layout.ContentPane" title="Mittente">');
	    //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
    	    print ('<fieldset style="border:none">' . "\n");
        	    $this->_FormFields['NOME']->showDivField();
        	    $this->_FormFields['COGNOME']->showDivField();
        	    $this->_FormFields['TITOLO']->showDivField();
        	    $this->_FormFields['TOPONIMO']->showDivField();
        	    $this->_FormFields['CIVICO']->showDivField();
        	    $this->_FormFields['CAP']->showDivField();
        	    $this->_FormFields['COMUNE']->showDivField();
        	    $this->_FormFields['PROVINCIA']->showDivField();
        	    $this->_FormFields['LOCALITA']->showDivField();
        	    $this->_FormFields['TELEFONO']->showDivField();
        	    $this->_FormFields['FAX']->showDivField();
        	    $this->_FormFields['CODICEFISCALE']->showDivField();
        	    $this->_FormFields['EMAIL']->showDivField();
    	    print ('</fieldset>' . "\n");

	    print ('</div>');

	}

	protected function editAltriDestinatari(){
	    print ('<div dojoType="dijit.layout.ContentPane" title="Altri Destinatari" id="altriDestinatari">');
            print('<div dojoType="dijit.form.Form" jsId="altraDestinazione" id="altraDestinazione" encType="multipart/form-data" action="" method="">');
                print('<script type="dojo/method" event="onReset">
            	            return true;
            	        </script>');
                print('<script type="dojo/method" event="onSubmit">
        	            if (this.validate()) {
        	            	data = this.attr(\'value\');
        	    			inserisciDestinazione(data,' . $this->_FormFields['PRATICA_ID']->GetValue() . ');
        	                return false;
        	            } else {
        	                alert(\'Mancano dati - correggi e aggiorna!\');
        	                return false;
        	            }
        	            return false;
        	        </script>');
                print('<table style="border: 1px solid #9f9f9f; margin: 5px;" cellspacing="10">');
                print('<tr>
        	                <td><label for="NOME_COGNOME"> Nome e Cognome </td>
        	                <td><input type="text" id="NOME_COGNOME" name="NOME_COGNOME" required="true" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('<tr>
        	                <td><label for="PER_CONOSCENZA"> Per Conoscenza </td>
        	                <td><input type="text" id="PER_CONOSCENZA" name="PER_CONOSCENZA" required="false" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('<tr>
        	                <td><label for="VIA"> Via </td>
        	                <td><input type="text" id="VIA" name="VIA" required="true" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('<tr>
        	                <td><label for="CAP"> C.A.P. </td>
        	                <td><input type="text" id="CAP" name="CAP" required="true" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('<tr>
        	                <td><label for="COMUNE"> Comune </td>
        	                <td><input type="text" id="DCOMUNE" name="DCOMUNE" required="true" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('<tr>
        	                <td><label for="PROVINCIA"> Provincia </td>
        	                <td><input type="text" id="PROVINCIA" name="PROVINCIA" required="true" dojoType="dijit.form.ValidationTextBox" /></td>
        	            </tr>');
                print('</table>');
                print('<button dojoType="dijit.form.Button" type="submit" name="submitButton" value="Submit">Aggiungi Indirizzo</button>');
                print('<button dojoType="dijit.form.Button" type="reset">Annulla</button>');
            print('</div>');

    		print ('<div dojoType="dijit.layout.ContentPane" id="dispDestinazioni" href="djGetDestinazioni.php?praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
    		print ('</div>');

		print ('</div>');

	}

	protected function editVincoli(){

	    print ('<div dojoType="dijit.layout.ContentPane" title="Vincoli">');


        print('</div>');

    }

    protected function editTitolazione(){
        // Titolazione
        print ('<div dojoType="dijit.layout.ContentPane" title="Titolazione" id="titolazione">');
        //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print ('<fieldset style="border:none">' . "\n");

        print ('<label for="TITO01">Livello I</label>');
        print ('<input id="TITO01">');
        print ('<br>');

        print ('<label for="TITO02">Livello II</label>');
        print ('<input id="TITO02">');
        print ('<br>');

        print ('<label for="TITO03">Livello III</label>');
        print ('<input id="TITO03">');
        print ('<br>');

        print ('<label for="SIGLA">Provincia</label>');
        print ('<input id="SIGLA">');
        print ('<br>');

        print ('<label for="TITOCOMUNE">Comune/Fascicolo</label>');
        print ('<input id="TITOCOMUNE">');
        print ('<br>');

        print ('<label for="Fascicolo">Sottofascicolo</label>');
        print ('<input id="FASCICOLO">');
        print ('<br>');

        print ('<label for="FASCICOLO_NEW">Aggiungi sottofascicolo</label>');
        print ('<input id="FASCICOLO_NEW">');
        print ('<br>');

        print ('<label for="TITOLAZIONE">Titolazione</label>');

        $findFascicoloSql = 'select al1.description as LIV01,' .
            'al2.description as LIV02, ' .
            'al3.description as LIV03, ' .
            'concat(ac.comune,\' ( \',ac.provincia,\')\') as COMUNE, ' .
            'at.fascicolo as FASCICOLO ' .
            'from arc_titolazioni at ' .
            'right join pratiche pr on (pr.titolazione = at.id) ' .
            'right join arc_comuni ac on (ac.id = at.comune) ' .
            'right join arc_titolario al3 on (al3.titolo = at.titolo) ' .
            'right join arc_tito02 al2 on ((al2.liv01 = al3.liv01) and (al2.liv02 = al3.liv02)) ' .
            'right join arc_tito01 al1 on (al1.liv01 = al2.liv01) ' .
            'where pr.pratica_id=' . $this->_FormFields['PRATICA_ID']->GetValue();

        // var_dump($findFascicoloSql);

        print ('<div style="display: block; font-weight: bold;" id="dispFascicolo">');
        if ($fascicolazioneResult = dbselect($findFascicoloSql)) {
            print ($fascicolazioneResult['ROWS'][0]['LIV01'] . '->' . $fascicolazioneResult['ROWS'][0]['LIV02'] . '->' . $fascicolazioneResult['ROWS'][0]['LIV03'] . '->');
            print ($fascicolazioneResult['ROWS'][0]['COMUNE'] . '->');
            print ($fascicolazioneResult['ROWS'][0]['FASCICOLO'] . '<br/>');
            $buttonTitola = 'Riesegui Titolazione';
        } else {
            print ('- Titolazione non eseguita -');
            $buttonTitola = 'Esegui Titolazione';
        }
        print ('</div>');
        print ('<br>');

        print (' <button dojoType="dijit.form.Button" ' .
            'onClick="return titola(\'' . $this->_FormFields['PRATICA_ID']->GetValue() . '\');">' .
            $buttonTitola .
            '</button>');

        print ('</div>');

    }
    protected function editIstruttoria(){
        print ('<div dojoType="dijit.layout.ContentPane" title="Istruttoria">');
        print ('<fieldset style="border:none">' . "\n");
        $this->_FormFields['ISTR01']->showDivField();
        $this->_FormFields['ISTR02']->showDivField();
        $this->_FormFields['ISTR03']->showDivField();
        print ('</fieldset>' . "\n");
        print ('</div>');

    }

    protected function editIntegrazioni(){
        print ('<div dojoType="dijit.layout.ContentPane" title="Integrazioni">');
        print ('<fieldset style="border:none">' . "\n");

        $this->_FormFields['NOTE01']->showDivField();
        $this->_FormFields['NOTE02']->showDivField();

        print ('</fieldset>' . "\n");
        print ('</div>');

    }

    protected function editConferenzaServizi(){
        print ('<div dojoType="dijit.layout.ContentPane" title="Conferenza Servizi">');
        print ('<fieldset style="border:none">' . "\n");
        $this->_FormFields['SER_TIPOLOGIA']->showDivField();
        $this->_FormFields['SER_ORA']->showDivField();
        $this->_FormFields['SER_LUOGO']->showDivField();
        $this->_FormFields['SER_DESCRIZIONE']->showDivField();
        $this->_FormFields['SER_AMBITO']->showDivField();
        $this->_FormFields['SER_PARERI']->showDivField();
        $this->_FormFields['SER_DOCUMENTAZIONE']->showDivField();
        $this->_FormFields['SER_OSSERVAZIONI']->showDivField();
        $this->_FormFields['SER_VALUTAZIONI']->showDivField();
        print ('</fieldset>' . "\n");
        print ('</div>');

    }

    protected function editPaesaggistica(){
        print ('<div dojoType="dijit.layout.ContentPane" title="Paesaggistica">');
        print ('<fieldset style="border:none">' . "\n");
        $this->_FormFields['PAE_DATA_PARERE']->showDivField();
        $this->_FormFields['PAE_LOC_INTERVENTO']->showDivField();
        $this->_FormFields['PAE_VAL_NCONDIVISE']->showDivField();
        $this->_FormFields['PAE_DESC_INCOMPATIBILITA']->showDivField();
        $this->_FormFields['PAE_MOTIVAZIONI']->showDivField();
        $this->_FormFields['PAE_NOTE_PRESCRIZIONI']->showDivField();
        $this->_FormFields['PAE_INTEGRAZIONI']->showDivField();
        print ('</fieldset>' . "\n");
        print ('</div>');

    }

    protected function editContributi(){
        print ('<div dojoType="dijit.layout.ContentPane" title="Contributi-Sgravi Fiscali">');
        print ('<fieldset style="border:none">' . "\n");
            $this->_FormFields['CONTRIBUTI']->showDivField();
            $this->_FormFields['CONT_RIFAUTLAV']->showDivField();
            $this->_FormFields['CONT_TIPINT']->showDivField();
            $this->_FormFields['CONT_TIPNONAMM']->showDivField();
            $this->_FormFields['CONT_DATAIST']->showDivField();
            include ('skContributi.inc');
        print ('</fieldset>' . "\n");
        print ('</div>');

    }

    function editOrganizzazione() {
        // if (dbselect('select * from user_uo_ref where user_id = '.$_SESSION['sess_uid'].' and uoid = 1')) {
            print ('<div dojoType="dijit.layout.ContentPane" title="Unit&agrave; Organizzative" id="unitaOrganizzative" >
						<div dojoType="dijit.form.Form" jsId="formUnitaOrganizzative" id="formUnitaOrganizzative" encType="multipart/form-data" action="" method="">
						    <script type="dojo/method" event="onReset">
						        return true;
						    </script>
						    <script type="dojo/method" event="onSubmit">
						        if (this.validate()) {
						        	data = this.attr(\'value\');
									inserisciUnitaOrganizzativa(data,'.$this->_FormFields['PRATICA_ID']->GetValue().');
						            return false;
						        } else {
						            alert(\'Mancano dati - correggi e aggiorna!\');
						            return false;
						        }
						        return false;
						    </script>
						    <div dojoType="dojo.data.ItemFileReadStore" ' .
                'url="xml/jsonSql.php?nullValue=N&sql=select UOID, DESCRIPTION from arc_organizzazione  where uoid <> 1" ' .
                'jsId="selUOID" ' .
                '>
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
        // }
    }


    function displayForm(){
        /* Form container */
        // var_dump($this->_FormFields['USCITA']);
        print('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

        // Titolo
		print('<div style="background-color: azure; font-size: 1.5em;">');
		print('<div style="float:left">' . $this->GetFormTitle() . '</div>'); // Protocollo data e oggetto
		print('<div style="float:right">' . $this->GetLastUpdate() . '</div>'); // Utente e data ultimo update
		print('</diV>');
        print('<div dojoType="dijit.layout.TabContainer"
							style="width:95%; height:450px; margin:0px;">');



        print('<div dojoType="dijit.layout.ContentPane" title="Pratica">');
		//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		print ('<fieldset style="border:none">' . "\n");
		//		$this->_FormFields['PRATICA_ID']->dispDivField();
		$this->_FormFields['DATAREGISTRAZIONE']->dispDivField();
		$this->_FormFields['NUMEROREGISTRAZIONE']->dispDivField();
		$this->_FormFields['MODELLO']->dispDivField();
		//		$this->_FormFields['ZONA']->dispDivField();
		//		$this->_FormFields['UFFICIO']->dispDivField();
		$this->_FormFields['DATAARRIVO']->dispDivField();
		$this->_FormFields['FUNZIONARIO']->dispDivField();
		$this->_FormFields['FIRMA']->dispDivField();
		$this->_FormFields['USCITA']->dispDivField();
		$this->_FormFields['PROTUSCITA']->dispDivField();
		$this->_FormFields['SCADENZA']->dispDivField();
		$this->_FormFields['RESPONSABILE']->dispDivField();
		$this->_FormFields['ESITO_ID']->dispDivField();

		print ('<label for="TITOLAZIONE">Titolazione</label>');

		$findFascicoloSql = 'select al1.description as LIV01,' .
		'al2.description as LIV02, ' .
		'al3.description as LIV03, ' .
		'concat(ac.comune,\' ( \',ac.provincia,\')\') as COMUNE, ' .
		'at.fascicolo as FASCICOLO ' .
		'from arc_titolazioni at ' .
		'right join pratiche pr on (pr.titolazione = at.id) ' .
		'right join arc_comuni ac on (ac.id = at.comune) ' .
		'right join arc_titolario al3 on (al3.titolo = at.titolo) ' .
		'right join arc_tito02 al2 on ((al2.liv01 = al3.liv01) and (al2.liv02 = al3.liv02)) ' .
		'right join arc_tito01 al1 on (al1.liv01 = al2.liv01) ' .
		'where pr.pratica_id=' . $this->_FormFields['PRATICA_ID']->GetValue();

		print ('<div style="display: block; font-weight: bold;" id="dispFascicolo">');
		if ($fascicolazioneResult = dbselect($findFascicoloSql)) {
			print ($fascicolazioneResult['ROWS'][0]['LIV01'] . '->' . $fascicolazioneResult['ROWS'][0]['LIV02'] . '->' . $fascicolazioneResult['ROWS'][0]['LIV03'] . '->');
			print ($fascicolazioneResult['ROWS'][0]['COMUNE'] . '->');
			print ($fascicolazioneResult['ROWS'][0]['FASCICOLO'] . '<br/>');
			$buttonTitola = 'Riesegui Titolazione';
		} else {
			print ('- Titolazione non eseguita -');
		}
		print ('</div>');

		print ('</div>');

		print ('<div dojoType="dijit.layout.ContentPane" title="Oggetto">');
		//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		print ('<fieldset style="border:none">' . "\n");

		$this->_FormFields['DATADOCUMENTO']->dispDivField();
		$this->_FormFields['NUMERORIFERIMENTO']->dispDivField();

		$this->_FormFields['OGGETTO']->dispDivField();
		$this->_FormFields['COMUNEOGG']->dispDivField();
		$this->_FormFields['INDIRIZZO_OG']->dispDivField();
		$this->_FormFields['FOGLIO']->dispDivField();
		$this->_FormFields['MAPPALE']->dispDivField();
		//		$this->_FormFields['ANAGRAFICO']->dispDivField();
		$this->_FormFields['CONDIZIONE']->dispDivField();
		$this->_FormFields['ALLEGATINUMERO']->dispDivField();
		$this->_FormFields['NOTE']->dispDivField();
		print ('</fieldset>' . "\n");
		print ('</div>');

		print ('<div dojoType="dijit.layout.ContentPane" title="Proprietario">');
		//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		print ('<fieldset style="border:none">' . "\n");

		$this->_FormFields['PNOME']->dispDivField();
		$this->_FormFields['PTOPONIMO']->dispDivField();
		$this->_FormFields['PCIVICO']->dispDivField();
		$this->_FormFields['PCAP']->dispDivField();
		$this->_FormFields['PCOMUNE']->dispDivField();
		$this->_FormFields['PPROVINCIA']->dispDivField();
		print ('</fieldset>' . "\n");
		print ('</div>');

		print ('<div dojoType="dijit.layout.ContentPane" title="Mittente">');
		//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		print ('<fieldset style="border:none">' . "\n");

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
		print ('</fieldset>' . "\n");
		print ('</div>');

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

		print ('<div dojoType="dijit.layout.ContentPane" title="Vincoli">');
		//		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
		print ('<fieldset style="border:none">' . "\n");
		if ($vincoliResult = dbselect($vincoliQuery)) {
			print ('<LABEL>Vincolo</LABEL>' . $vincoliResult['ROWS'][0]["denominazione"] . '<BR>');
			print ('<LABEL>Indirizzo</LABEL>' . $vincoliResult['ROWS'][0]["indirizzo"] . '<BR>');
			print ('<LABEL>Localit&agrave;</LABEL>' . $vincoliResult['ROWS'][0]["localita"] . '<BR>');
			print ('<LABEL>Comune/Prov.</LABEL>' . $vincoliResult['ROWS'][0]["comune"] . ' ' . $vincoliResult['ROWS'][0]["provincia"] . '<BR>');
			print ('<LABEL>Foglio catastale</LABEL>' . $vincoliResult['ROWS'][0]["fogliocatastale"] . '<BR>');
			print ('<LABEL>Particelle</LABEL>' . $vincoliResult['ROWS'][0]["particelle"] . '<BR>');
			print ('<LABEL>Provv. Ministeriale</LABEL>' . $vincoliResult['ROWS'][0]["provvedimentoministeriale"] . '<BR>');
			print ('<LABEL>Trascrizione conservatoria</LABEL>' . $vincoliResult['ROWS'][0]["trascrizioneinconservatoria"] . '<BR>');
			print ('<LABEL>Posizione monumentale</LABEL>' . $vincoliResult['ROWS'][0]["posizioneMonumentale"] . '<BR>');
			print ('<LABEL>Posizione vincoli</LABEL>' . $vincoliResult['ROWS'][0]["posizioneVincoli"] . '<BR><BR>');
            print('<LABEL>Cartella monumentale</LABEL>' . utf8_encode($vincoliResult['ROWS'][0]["cartellaprogettimonumentale"]) . '<BR>');
            $pathCartellaMonumentale = 'archivio' . DIRECTORY_SEPARATOR .
                sanitizePath($vincoliResult['ROWS'][0]['provincia']) . DIRECTORY_SEPARATOR .
                sanitizePath($vincoliResult['ROWS'][0]['comune']) . DIRECTORY_SEPARATOR .
                (!empty($vincoliResult['ROWS'][0]['cartellaprogettimonumentale']) ?
                    sanitizePath($vincoliResult['ROWS'][0]['cartellaprogettimonumentale']) . DIRECTORY_SEPARATOR
                : '');

            print('<a href="' . $pathCartellaMonumentale . '" target="_blank"><i class="fa fa-folder-open-o fa-2x fa-align-right" aria-hidden="true"></i> Cartella Monumentale</a>');

			print ('</fieldset>' . "\n");

		}
		print ('</div>');

		print ('<div dojoType="dijit.layout.ContentPane" title="Altri Destinatari" id="altriDestinatari">');
		print ('<div dojoType="dijit.layout.ContentPane" id="dispDestinazioni" href="djGetDestinazioni.php?praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
		print ('</div>');

		print ('</div>');

		print ('<div dojoType="dijit.layout.ContentPane" title="Istruttoria">');
		print ('<fieldset style="border:none">' . "\n");
		$this->_FormFields['ISTR01']->dispDivField();
		$this->_FormFields['ISTR02']->dispDivField();
		$this->_FormFields['ISTR03']->dispDivField();
		print ('</fieldset>' . "\n");
		print ('</div>');
		print ('<div dojoType="dijit.layout.ContentPane" title="Integrazioni">');
		print ('<fieldset style="border:none">' . "\n");
		$this->_FormFields['NOTE01']->dispDivField();
		$this->_FormFields['NOTE02']->dispDivField();
		print ('</fieldset>' . "\n");
		print ('</div>');

		if ($this->_FormFields['MODELLO']->GetValue() > '') {

			if ($tipoPraticaResult = dbselect('select * from arc_modelli where modello = ' . $this->_FormFields['MODELLO']->GetValue())) {
				if ($tipoPraticaResult['ROWS'][0]['SK_PAESAGGIO'] == 'Y') {
					print ('<div dojoType="dijit.layout.ContentPane" title="Paesaggistica">');
					print ('<fieldset style="border:none">' . "\n");
					$this->_FormFields['PAE_DATA_PARERE']->dispDivField();
					$this->_FormFields['PAE_LOC_INTERVENTO']->dispDivField();
					$this->_FormFields['PAE_VAL_NCONDIVISE']->dispDivField();
					$this->_FormFields['PAE_DESC_INCOMPATIBILITA']->dispDivField();
					$this->_FormFields['PAE_MOTIVAZIONI']->dispDivField();
					$this->_FormFields['PAE_NOTE_PRESCRIZIONI']->dispDivField();
					$this->_FormFields['PAE_INTEGRAZIONI']->dispDivField();
					print ('</fieldset>' . "\n");
					print ('</div>');
				}
				if ($tipoPraticaResult['ROWS'][0]['SK_CSERVIZI'] == 'Y') {
					print ('<div dojoType="dijit.layout.ContentPane" title="Conferenza Servizi">');
					print ('<fieldset style="border:none">' . "\n");
					$this->_FormFields['SER_TIPOLOGIA']->showDivField();
					$this->_FormFields['SER_ORA']->dispDivField();
					$this->_FormFields['SER_LUOGO']->dispDivField();
					$this->_FormFields['SER_DESCRIZIONE']->dispDivField();
					$this->_FormFields['SER_AMBITO']->dispDivField();
					$this->_FormFields['SER_PARERI']->dispDivField();
					$this->_FormFields['SER_DOCUMENTAZIONE']->dispDivField();
					$this->_FormFields['SER_OSSERVAZIONI']->dispDivField();
					$this->_FormFields['SER_VALUTAZIONI']->dispDivField();
					print ('</fieldset>' . "\n");
					print ('</div>');
				}

				if ($tipoPraticaResult['ROWS'][0]['SK_PAESAGGIO'] == 'Y') {
					print ('<div dojoType="dijit.layout.ContentPane" title="Paesaggistica">');
					print ('<fieldset style="border:none">' . "\n");
					$this->_FormFields['PAE_DATA_PARERE']->dispDivField();
					$this->_FormFields['PAE_LOC_INTERVENTO']->dispDivField();
					$this->_FormFields['PAE_VAL_NCONDIVISE']->dispDivField();
					$this->_FormFields['PAE_DESC_INCOMPATIBILITA']->dispDivField();
					$this->_FormFields['PAE_MOTIVAZIONI']->dispDivField();
					$this->_FormFields['PAE_NOTE_PRESCRIZIONI']->dispDivField();
					$this->_FormFields['PAE_INTEGRAZIONI']->dispDivField();
					print ('</fieldset>' . "\n");
					print ('</div>');
				}

				if ($tipoPraticaResult['ROWS'][0]['SK_CONTRIBUTI'] == 'Y') {
					print ('<div dojoType="dijit.layout.ContentPane" title="Contributi-Sgravi Fiscali">');
					print ('<fieldset style="border:none">' . "\n");
					$this->_FormFields['CONTRIBUTI']->dispDivField();
					$this->_FormFields['CONT_RIFAUTLAV']->dispDivField();
					$this->_FormFields['CONT_TIPINT']->dispDivField();
					$this->_FormFields['CONT_TIPNONAMM']->dispDivField();
					$this->_FormFields['CONT_DATAIST']->dispDivField();
					print ('<div dojoType="dijit.layout.ContentPane" id="dispContributi" href="djGetContributi.php?display=Y&praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
					print ('</div>');

					print ('</fieldset>' . "\n");
					print ('</div>');
				}

			} else {
				print ('<div dojoType="dijit.layout.ContentPane" title="Errori">');
				print ('<h1>Errore nel Tipo di pratica</h1>' . "\n");
				print ('</div>');

			}
		}
		$this->dispUploads();
		$this->dispPecfiles();

		print ('</div>');

		print ("\n");
		print ("<br />\n");
		print ('</div>' . "\n");
	}

	public function getFormTitle() {
		if ($this->getFormName() == 'PRATICHE' and $this->GetFormMode() == 'modify') {
			$formTitle = '<span id="oggettoEspi" style="cursor: pointer" >Nr Reg.: ' . $this->GetFormFieldValue('numeroregistrazione') .
			' - Data Reg.: ' . date('d-m-Y', strtotime($this->GetFormFieldValue('dataregistrazione'))) .
			'</span>';
			$formTitle .= '<span dojoType="dijit.Tooltip" id ="ttOggettoEspi" connectId="oggettoEspi" style="display:none;"><div class="djToolTipContainer" >' .
			$this->GetFormFieldValue('OGGETTO') . '</div></span>';
			return ($formTitle);
		} else {
			return $this->_FormName;
		}
	}

	protected function getLastUpdate(){
	    $latUpdate = Db_Pdo::getInstance()->query('SELECT DATE_FORMAT(pratiche.updated, "%d/%m/%Y") as updated,
	            pratiche.updated as data,
	            sys_users.last_name, sys_users.first_name FROM pratiche
	            LEFT JOIN sys_users ON (sys_users.user_id = pratiche.updated_by)
	            WHERE pratiche.pratica_id = :pratica_id',array(
	            ':pratica_id' => $this->GetFormFieldValue('PRATICA_ID')))->fetch();


        return ($latUpdate ? $latUpdate['first_name'] . ' ' . $latUpdate['last_name'] . ' - ' . $latUpdate['updated'] : '');
	}

	function FormPostValidation() {

		if ($_POST['MODELLO'] > '' and $_POST['DATAARRIVO'] > '') {
			$getModelloQuery = 'select scadenza from arc_modelli where modello =' . $_POST['MODELLO'];
			if ($getModelloResult = dbselect($getModelloQuery)) {
				if ($getModelloResult['ROWS'][0]['scadenza'] == '0') {
					return true;
				}
				$scadenzaQuery = 'update pratiche set scadenza = getScadenza(' . $this->GetFormFieldValue('PRATICA_ID') . ') where pratica_id = ' . $this->GetFormFieldValue('PRATICA_ID');
				dbupdate($scadenzaQuery);

			}

		}
		return TRUE;
	}
	protected function editUploads() {
		print ('<div dojoType="dijit.layout.ContentPane" title="Uploads">');
		print('<div style="margin:10px 5px;">
						<a  href="#" onclick="openUploadsDialog()">
                            <i class="fa fa-upload" > </i>
							<span style="margin:5px;">Carica Files</span>

						</a>
				</div>');
			print('<div dojoType="dijit.layout.ContentPane" id="dispUploads" href="djGetUploads.php?PRATICA_ID='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
			print ('</div>');
		print ('</div>');
	}
	protected function editPecfiles() {
		print ('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC">');
			print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djGetEmlpecFile.php?PRATICA_ID='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
			print ('</div>');
		print ('</div>');
	}

	protected function dispUploads() {
		print ('<div dojoType="dijit.layout.ContentPane" title="Uploads">');
			print('<div dojoType="dijit.layout.ContentPane" id="dispUploads" href="djGetUploads.php?mode=ro&PRATICA_ID='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
			print ('</div>');
		print ('</div>');
	}
	protected function dispPecfiles() {
		print ('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC">');
			print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djGetEmlpecFile.php?PRATICA_ID='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
			print ('</div>');
		print ('</div>');
	}



}

$PRATICA_ID = isSet($_GET['PRATICA_ID']) ? $_GET['PRATICA_ID'] : $_POST['PRATICA_ID'];

$dbKey = isSet($_GET['dbKey']) ? $_GET['dbKey'] : ' where PRATICA_ID=' . $PRATICA_ID;

$ManagedTable = new MyDbForm('PRATICHE', $_SESSION['sess_lang']);

$del_message = get_label('del_message');

//$ManagedTable->setAfterUpdateLocation('praticheStatus.php');

$ManagedTable->setAfterUpdateLocation('editPratica.php?PRATICA_ID=' . $PRATICA_ID);

$ManagedTable->SetFormMode("modify", stripslashes($dbKey));

include ('pageheader.inc');

$modelloQuery = $ManagedTable->_FormFields['MODELLO']->GetValue() > '' ? ' or modello = ' . $ManagedTable->_FormFields['MODELLO']->GetValue() : '';

print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select DOC_ID, DESCRIPTION, MODELLO from arc_documenti where modello is null ' . $modelloQuery . '" ' .
'jsId="sDocumenti" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select TIT01, LIV01, concat(liv01,\' - \',description) as DESCRIPTION from arc_tito01" ' .
'jsId="sTito01" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select TIT02, LIV01, LIV02, concat(liv01,\'.\',liv02,\' - \',description) as DESCRIPTION from arc_tito02 " ' .
'jsId="sTito02" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select TITOLO, LIV01, LIV02, LIV03, concat(liv01,\'.\',liv02,\'.\',liv03,\' - \',description) as DESCRIPTION from arc_titolario " ' .
'jsId="sTito03" ' .
'></div>');

print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select * from arc_province " ' .
'jsId="sProvince" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select * from arc_comuni " ' .
'jsId="sComuni" ' .
'></div>');

print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_titolazioni " ' .
'jsId="sFascicoli" ' .
'></div>');
?>
<script language="JavaScript" type="text/javascript">

	dojo.addOnLoad(function(){

	    new dijit.form.FilteringSelect({
	                store: sDocumenti,
	                labelAttr: 'DESCRIPTION',
	                searchAttr: 'DESCRIPTION',
	                name: "DOCUMENTO",
	                autoComplete: true,
	                style: "width: 150px;",
	                id: "selDocuments",
	                onChange: function(selDocuments) {
						dojo.byId('creaButton').disabled = false;
	                }

	            },
	            "selDocuments");

	    new dijit.form.FilteringSelect({
	                store: sTito01,
	                labelAttr: 'DESCRIPTION',
	                searchAttr: 'DESCRIPTION',
	                name: "TITO01",
	                autoComplete: true,
	                style: "width: 150px;",
	                id: "TITO01",
	                onChange: function(TITO01) {

						dijit.byId('TITO02').query.LIV01 = dijit.byId('TITO01').item.LIV01[0] ;
						dijit.byId('TITO03').query.LIV01 = dijit.byId('TITO01').item.LIV01[0] ;
	                }

	            },
	            "TITO01");

	    new dijit.form.FilteringSelect({
	                store: sTito02,
	                labelAttr: 'DESCRIPTION',
	                searchAttr: 'DESCRIPTION',
	                name: "TITO02",
	                query : { LIV01 : "*"},
	                autoComplete: true,
	                style: "width: 250px;",
	                id: "TITO02",
	                onChange: function(TITO02) {

						dijit.byId('TITO03').query.LIV02 = dijit.byId('TITO02').item.LIV02[0] ;
	                }
	            },
	            "TITO02");
	    new dijit.form.FilteringSelect({
	                store: sTito03,
	                labelAttr: 'DESCRIPTION',
	                searchAttr: 'DESCRIPTION',
	                name: "TITO03",
	                query : {LIV01 : "*" , LIV02 : "*" },
	                autoComplete: true,
	                style: "width: 250px;",
	                id: "TITO03",
	                onChange: function(TITO03) {

						dijit.byId('FASCICOLO').query.TITOLO = TITO03 ;

						return true;
	                }
	            },
	            "TITO03");

	    new dijit.form.FilteringSelect({
	                store: sProvince,
	                labelAttr: 'PROVINCIA',
	                searchAttr: 'PROVINCIA',
	                name: "SIGLA",
	                autoComplete: true,
	                style: "width: 250px;",
	                id: "SIGLA",
	                onChange: function(SIGLA) {
	                	dijit.byId('TITOCOMUNE').query.PROVINCIA = dijit.byId('SIGLA').item.SIGLA[0] ;
						return true;
	                }
	            },
	            "SIGLA");

	    new dijit.form.FilteringSelect({
	                store: sComuni,
	                labelAttr: 'COMUNE',
	                searchAttr: 'COMUNE',
	                name: "TITOCOMUNE",
	                autoComplete: true,
	                style: "width: 250px;",
	                query : { PROVINCIA : "*"},
	                id: "TITOCOMUNE",
	                onChange: function(ID) {

						dijit.byId('FASCICOLO').query.COMUNE = ID ;
						return true;
	                }
	            },
	            "TITOCOMUNE");

	    new dijit.form.FilteringSelect({
	                store: sFascicoli,
	                labelAttr: 'FASCICOLO',
	                searchAttr: 'FASCICOLO',
	                query : { TITOLO : "*", COMUNE: "*" },
	                name: "FASCICOLO",
	                autoComplete: true,
	                style: "width: 250px;",
	                required: false,
	                id: "FASCICOLO",
	                onChange: function(FASCICOLO) {
						return true;
	                }
	            },
	            "FASCICOLO");


	});

</script>
<?php


if ($mode = 'modify' and isSet ($_GET['PRATICA_ID'])) {

	include 'barraEditPratica.inc';
	//print ('<div>' . "\n");
	//print ('<div style="float: left;">' . "\n");

	//print ('<input id="selDocuments">' .
	//'<button id="creaButton" type="button"  disabled="disabled" ' .
	//'	onclick="return creaDaModello(\'' . $ManagedTable->_FormFields['PRATICA_ID']->GetValue() . '\')" >Crea da Modello</button>');

	//print ('</div>' . "\n");
	//print ('<div style="float: right;">' . "\n");
	//if ($ManagedTable->GetFormFieldValue('USCITA') > ' ') {
		//print ('</div>' . "\n");
	//} else {
		//print ('<a href="praticaSospensione.php?PRATICA_ID=' . $PRATICA_ID . '">Sospendi Pratica</a><img src="graphics/alerts/error.gif" style="margin-left:10px; margin-right:10px;"  title="Sospendi Pratica" >');
		//print ('<a href="praticaAttivazione.php?PRATICA_ID=' . $PRATICA_ID . '">Riprendi Pratica</a><img src="graphics/alerts/error.gif" style="margin-left:10px; margin-right:10px;"  title="Riattiva Pratica" >');
		//print ('<a href="praticaVincoli.php?PRATICA_ID=' . $PRATICA_ID . '">Visualizza Vincoli</a><img src="graphics/caution.gif" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli" >');
		//print ('</div>' . "\n");
	//}

	//print ('</div>' . "\n");
	//print ('<div style="clear: both;"></div>' . "\n");
}

if (isSet ($display) and ($display == 'Y')) {
	$ManagedTable->displayForm();
} else {
	$ManagedTable->ShowForm();
}
print('<iframe id="printElement" style="height: 0px; width: 0px; position: absolute"></iframe>');
include ('pagefooter.inc');
?>
