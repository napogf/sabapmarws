<?php

class formPratica extends formExtended
{
    protected $_praticaObj;

    function ShowForm()
    {
        $this->_praticaObj = new Pratica();

        $this->_praticaObj->setId($this->_FormFields['PRATICA_ID']->GetValue());
        include('skUploads.inc');


        $this->editForm();
    }

    function editForm()
    {

        /* Form container */
        print('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

        print('<!-- Form open -->');
        print("\n");
        print($this->GetFormHeader());
        $this->_FormFields['ZONA']->hideField();


        print('<div style="background-color: azure; font-size: 1.5em; display: block">');
            print('<div style="float:left">' . $this->GetFormTitle() . '</div>'); // Protocollo data e oggetto
            print('<div style="float:right">' . $this->GetLastUpdate() . '</div>'); // Utente e data ultimo update
        print('</div><div style="clear: both"></div> ');


        $this->formMessageShow();
        $this->formAttachmentsShow();
        // $this->showButtonBar(FALSE);



        $this->showButtonBar();
        // Pane Container
//        print('</div>' . "\n");

        print('<div id="praticheTabs" dojoType="dijit.layout.TabContainer" class="dbFormContainer"
							style="width:98%; height: 800px; margin:0px;" >');

        $this->editMain();
        $this->praticheFascicolo();
        $this->editOggetto();
        $this->editProprietario();
        $this->editMittente();
        $this->editAltriDestinatari();
//        $this->editTitolazione();
        $this->editVincoli();

        $this->editIstruttoria();
        $this->editIntegrazioni();

        // Skede dipendenti dal modello
        if ($this->_FormFields['MODELLO']->GetValue() > '') {
            if ($tipoPraticaResult = dbselect('SELECT * FROM arc_modelli WHERE modello = ' . $this->_FormFields['MODELLO']->GetValue())) {
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
        if ($this->_FormFields['NUMEROREGISTRAZIONE']->GetValue() == '') {
            $this->protocollaEntrata();
        }
        print('</div>');

        print('</form>' . "\n");

    }

    /* costruisce la form graficamente a partire dagli oggetti caricati */

    function getFormHeader()
    {
        $_formHeader = '<FORM ACTION="' . $this->getFormDestination() . $this->getFormAction() . '" ' .
            $this->getFormEnctype() . $this->getFormMethod() . 'name="' . $this->getFormName() .
            '" id="form-pratica" class="' .
            // ((($this->_FormFields['USCITA']->GetValue() > '') or ($this->_FormFields['TIPOLOGIA']->GetValue() == 'U')) ? 'form-readonly' :'showed')
            ((($this->_FormFields['USCITA']->GetValue() > '')) ? 'form-readonly' : 'showed')
            . '" >' . "\n";
        $_formHeader .= '<input type="hidden" name="dbTable" value="' . $this->getFormName() . '">' . "\n";

        return $_formHeader;

    }

    function getFormTitle()
    {
        if ($this->getFormName() == 'PRATICHE' and $this->GetFormMode() == 'modify') {
            $tipoPratica = array(
                'I' => 'Interno',
                'E' => 'Entrata',
                'U' => 'Uscita'
            );

            $formTitle = '<span id="oggettoEspi" style="cursor: pointer" >Prot. ' . $tipoPratica[$this->_FormFields['TIPOLOGIA']->GetValue()] . ' Nr: ' . $this->GetFormFieldValue('numeroregistrazione') . ' - Data Reg.: ' .
                ($this->GetFormFieldValue('dataregistrazione') > '' ? (new Date($this->GetFormFieldValue('dataregistrazione')))->format('d/m/Y') : '') . '</span>';
            $formTitle .= '<span dojoType="dijit.Tooltip" id ="ttOggettoEspi" connectId="oggettoEspi" style="display:none;"><div class="djToolTipContainer" >' . $this->GetFormFieldValue('OGGETTO') . '</div></span>';
            return ($formTitle);
        } else {
            return $this->_FormName;
        }
    }

    function showButtonBar($mode = null)
    {
//         $isProtocollatore = Db_Pdo::getInstance()->query('SELECT * from sys_responsabilities resp
//             RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
//             WHERE surr.user_id IN (:admin, :user_id) AND resp.description = "Protocollazione"',array(
//                         ':admin' => 1,
//                         ':user_id' => $_SESSION['user_id']
//                     ))->fetch();

        print('<div id="praticaButtonBar" style="background-color: #FFFFCC; height:22px; padding: 2px 30px 2px 30px;">');
        print('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" style="float:left;" />');
        if ($this->_FormFields['MODELLO']->GetValue() > ''
            and $this->_FormFields['ESITO_ID']->GetValue() > ''
            //and $isProtocollatore
        ) {
            print('<input type="submit" value="Protocolla e Chiudi" name="protocollazione" class="buttons"
                        style="float:left;background-color:red; color: white;" />');
        }
        //print('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
        print('</div>');
    }

    protected function editMain()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Pratica" 
                selected="' . $this->isPaneSelected('main') . '" >');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");

        print('<fieldset style="border:none; padding: 5px;">' . "\n");
        $this->_FormFields['PRATICA_ID']->showDivField();
        $this->_FormFields['DATAREGISTRAZIONE']->showDivField();
        $this->_FormFields['NUMEROREGISTRAZIONE']->showDivField();
        $this->_FormFields['MODELLO']->showDivField();
//        print('<label for="MODELLO" >Tipo di Pratica<font face="Arial, Helvetica, sans-serif" >*</font></label>
//                <div dojoType="dojo.data.ItemFileReadStore" url="xml/jsonSql.php?nullValue=N&sql=select distinct am.MODELLO, am.description as DESCRIPTION  from arc_modelli am  order by 2" jsId="MODELLO_classifica" ></div>
//                <div dojoType="dijit.form.FilteringSelect"  store="MODELLO_classifica"
//							searchAttr="DESCRIPTION"
//							name="MODELLO"
//							id="modello"  value="' . $this->_FormFields['MODELLO']->getValue() . '"
//							style="width:450px;"
//							queryExpr="${0}*"
//							searchDelay="1000"
//							pageSize="100" ></div><br/>');



//        $this->_FormFields['ZONA']->showDivField();
//        $this->_FormFields['UFFICIO']->showDivField();
        $this->_FormFields['DATAARRIVO']->showDivField();
        $this->_FormFields['FUNZIONARIO']->showDivField();
        $this->_FormFields['FIRMA']->showDivField();

        print('<label for="PRATICA_USCITA_ID" >Uscita - Num. Prot.</label>
					<div dojoType="dojo.data.ItemFileReadStore"
						url="xml/jsonSql.php?nullValue=Y&sql=select pratiche.PRATICA_ID, ' .
						        'DATAREGISTRAZIONE ,substring(concat(numeroregistrazione,\'-\',DATE_FORMAT(dataregistrazione,\'%d/%m/%Y\'),\'-\',oggetto),1,100) as DESCRIPTION ' .
						        'from pratiche ' .
						        'LEFT JOIN pratiche_fascicoli ON (pratiche_fascicoli.pratica_id = pratiche.pratica_id) ' .
						        'where pratiche.tipologia = \'U\' ' .
                                ' AND pratiche.PRATICA_ID > ' . $this->_FormFields['PRATICA_ID']->GetValue() .
						        ' AND pratiche_fascicoli.pratica_id IS NULL order by 1 desc "
						jsId="PRATICA_USCITA_ID_1439" ></div>
					<div dojoType="dijit.form.FilteringSelect"
							store="PRATICA_USCITA_ID_1439"
							searchAttr="DESCRIPTION"
							name="PRATICA_USCITA_ID"
							id="1439"
							queryExpr="${0}*"
    							searchDelay="500" 
    							autocomplete="false" 
							value="' . $this->_FormFields['PRATICA_USCITA_ID']->GetValue() . '"
							onchange="setUscita(this);"
							style="width:400px;"							
							 ></div><br/>');
        $this->_FormFields['USCITA']->showDivField();

        // $this->_FormFields['PROTUSCITA']->showDivField();
        if ($this->_FormFields ['MODELLO']->GetValue() > '') {
            $getModelloQuery = 'SELECT scadenza FROM arc_modelli WHERE modello =' . $this->_FormFields ['MODELLO']->GetValue();
            if ($getModelloResult = dbselect($getModelloQuery)) {
                if ($getModelloResult ['ROWS'] [0] ['scadenza'] == '0') {
                    $this->_FormFields ['SCADENZA']->showDivField();
                } else {
                    $this->_FormFields ['SCADENZA']->dispDivField();
                }
            }
        }
//        $this->_FormFields['RESPONSABILE']->showDivField();
//        $this->_FormFields['RESPONSABILE_ID']->showDivField();
//        $this->_FormFields['FALDONE']->showDivField();
        // $this->_FormFields['ESITO_ID']->showDivField();

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
								name="RESPONSABILE_ID" ' . 'value="' . $this->_FormFields['RESPONSABILE_ID']->GetValue() . '" ' . '></div>');

        print('<br>');

        $this->_FormFields['FASIDENTIFICATIVO']->showDivField();
        $this->_FormFields['CAUSALE']->showDivField();



        print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' . 'jsId="testJsonPhp" ' . '/>');
        print('<label for="ESITO_ID">Esito</label>');
        print('<div dojoType="dijit.form.FilteringSelect" style="width: 400px;" ID="SEL_ESITO"
								store="testJsonPhp"
								labelAttr="DESCRIPTION"
                                queryExpr="*${0}*"
    							searchDelay="500" 
    							autocomplete="false" 
								searchAttr="DESCRIPTION"
								name="ESITO_ID" ' . 'value="' . $this->_FormFields['ESITO_ID']->GetValue() . '" ' . '></div>');

        print('<br>');

        if ($this->_FormFields['SCADENZA']->GetValue() > ' ') {

            $alertDays = dbselect('SELECT allarme FROM arc_modelli WHERE modello = ' . $this->_FormFields['MODELLO']->GetValue());

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

            print('<LABEL>Avanzamento</LABEL><div style="width:340px;  background: ' . $bgColor . ' none repeat;" ' . 'annotate="true"
								  		maximum="' . $maxVal . '" id="setTestBar" ' . 'progress="' . $advVal . '" ' . 'label="Prova giorni" ' . 'dojoType="dijit.ProgressBar">' . '<script type="dojo/method" event="report">' . '	var test = dojo.query(".dijitProgressBarFull","setTestBar"); ' . '   return dojo.string.substitute("Gg. Trascorsi ' . $actualDays . ' di ' . $fullDays . '", [this.progress, this.maximum]);
										  </script>
								  		 </div><br>');
        }
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function isPaneSelected($pane)
    {
        if (isSet($_SESSION['paneSelected']) and $_SESSION['paneSelected'] == $pane) {
            unset($_SESSION['paneSelected']);
            return 'true';
        }

        return 'false';
    }

    protected function praticheFascicolo()
    {
        if($this->_praticaObj->TIPOLOGIA !== 'I'){

            $db = Db_Pdo::getInstance();
            $fascicoli = array();
            print('<div dojoType="dijit.layout.ContentPane" title="Fascicolo" id="fascicoloPane" selected="' . $this->isPaneSelected('fascicolo') . '" >');
            /* Verifico che una delle pratiche del fascicolo non sia già associata ad un progetto
             * altrimenti propongo l'associazione ad un progetto oppure la creazione di un progetto
             */

            print('<table class="progetto">');
            if ($project = $this->_praticaObj->getInfo('progetto')) {
                print('<tr>
                <td><button dojoType="dijit.form.Button" id="eliminaProgetto" value="' . $this->_FormFields['PRATICA_ID']->GetValue() . '"><i class="fa fa-trash"> </i>&nbsp;Rimuovi il protocollo dal Fascicolo</button></td>
			    <td>' . $project['DESCRIPTION'] . '</td>
		        </tr>');

            } else {
                print('<tr><td>
                    <button dojoType="dijit.form.Button" id="associaFascicolo" value="' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >
                                        <i class="fa fa-link"> </i>&nbsp;Associa ad un Protocollo</button>
                </td><td>');
                print('<div dojoType="dojox.data.QueryReadStore" ' .
                    'url="xml/jsonRicercaProtocollo.php" ' .
                    'jsId="fascRicerca" ' .
                    '></div>');
                print('<div dojoType="dijit.form.FilteringSelect"  ' .
                    'store="fascRicerca" searchAttr="DESCRIPTION" ' .
//                ' pageSize="50" ' .
                    ' autoComplete="false" ' .
                    ' required="false" ' .
                    'name="ricercaFascicolo" ' .
                    'id="ricercaFascicolo" ' .
                    ' value="" ' .
                    ' queryExpr= "*${0}*", ' .
                    ' style="width:500px;" ' .
                    ' searchDelay="1000" ' .
                    '></div>');


                print('</td></tr>');


                print('<tr><td>
                    <button dojoType="dijit.form.Button" id="associaProgetto" value="' . $this->_FormFields['PRATICA_ID']->GetValue() . '" ><i class="fa fa-link"> </i>&nbsp;Associa ad un Fascicolo esistente</button>
                </td><td>');

                print('<div dojoType="dojo.data.ItemFileReadStore" ' .
                    'url="xml/jsonSql.php?sql=select PROJECT_ID as ITEM, DESCRIPTION as DESCRIPTION from arc_pratiche_prj " ' .
                    'jsId="prjRicerca" ' .
                    '></div>');


                print('<div dojoType="dijit.form.FilteringSelect"  ' .
                    'store="prjRicerca" searchAttr="DESCRIPTION" ' .
//                ' pageSize="50" ' .
                    ' autoComplete="false" ' .
                    ' required="false" ' .
                    'name="ricercaProgetto" ' .
                    'id="ricercaProgetto" ' .
                    ' value="" ' .
                    ' queryExpr= "*${0}*", ' .
                    ' style="width:500px;" ' .
                    // ' searchDelay="1000" ' .
                    '></div>');


                print('</td></tr>');
                print('<tr>
                <td><button dojoType="dijit.form.Button" id="creaProgetto" value="' . $this->_FormFields['PRATICA_ID']->GetValue() . '"><i class="fa fa-plus-circle"> </i>&nbsp;Crea un Fascicolo</button></td>
			    <td><input type="text" id="nuovoProgetto" name="nuovoProgetto" required="false" style="width:500px;" dojoType="dijit.form.ValidationTextBox"/></td>
		        </tr>');
            }
            print('</table>');

            print('<div><ul class="fascicoloPratiche selected">Protocollo');

            if ($this->_praticaObj->fascicolo) {
                foreach ($this->_praticaObj->fascicolo as $pratica) {
                    print('<li>' . ($pratica['tipologia'] == 'E' ? '<i class="fa fa-arrow-circle-right"> </i>&nbsp;Entrata' : 'Uscita&nbsp;<i class="fa fa-arrow-circle-left"> </i>'));
//                 print('<ul>');
                    switch ($pratica['funzione']) {
                        case 'inizio_sospensione':
                            $iconaSospensione = '<i class="fa fa-pause"> </i>';
                            break;
                        case 'fine_sospensione':
                            $iconaSospensione = '<i class="fa fa-play"> </i>';
                            break;
                        default:
                            $iconaSospensione = '';
                            break;
                    }

                    if ($praticaDesc = $db->query('SELECT pratica_id, numeroregistrazione, dataregistrazione, oggetto FROM pratiche WHERE pratica_id = :pratica_id', array(
                        ':pratica_id' => $pratica['pratica_id'],
                    ))->fetch()
                    ) {
                        print('<ul><li><i class="fa fa-edit fa-2x pratica-fascicolo"  data-pratica-id="' . $praticaDesc['pratica_id'] . '" > </i>' .
                            $praticaDesc['numeroregistrazione'] . ' - ' .
                            (new Date($praticaDesc['dataregistrazione']))->toReadable() .
                            ' : ' . $praticaDesc['oggetto'] . $iconaSospensione .
                            '</li></ul>');
                    }
                }
//                 print('</ul>');
                print('</li>');
            } else {
                print('<li>' . ($this->_praticaObj->TIPOLOGIA == 'E' ? '<i class="fa fa-arrow-circle-right"> </i>&nbsp;Entrata' : 'Uscita&nbsp;<i class="fa fa-arrow-circle-left"> </i>'));
                print('<ul>');
                print('<li><i class="fa fa-edit fa-2x pratica-fascicolo"  data-pratica-id="' . $this->_praticaObj->PRATICA_ID . '" > </i>' .
                    $this->_praticaObj->getInfo('NUMEROREGISTRAZIONE') . ' - ' .
                    (new Date($this->_praticaObj->getInfo('DATAREGISTRAZIONE')))->toReadable() .
                    ' : ' . $this->_praticaObj->getInfo('OGGETTO') .
                    '</li>');
                print('</ul>');
                print('</li>');

            }
            print('</ul></div>');


            if ($project = $this->_praticaObj->progetto) {
                $fascicoliProgetto = $db->query('
                    SELECT DISTINCT pratiche_fascicoli.fascicolo_id FROM pratiche
                    RIGHT JOIN arc_pratiche_prj ON (arc_pratiche_prj.PRATICA_ID = pratiche.PRATICA_ID)
                    LEFT JOIN pratiche_fascicoli ON (
                        pratiche_fascicoli.pratica_id = pratiche.pratica_id
                    )                    
                    WHERE arc_pratiche_prj.project_id = :project_id                    
                    AND pratiche_fascicoli.fascicolo_id NOT IN
                        (SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratiche_fascicoli.pratica_id = :pratica_id)
                    UNION 
                    SELECT DISTINCT pratiche_fascicoli.fascicolo_id FROM pratiche 
                    LEFT JOIN pratiche_fascicoli ON (
                        pratiche_fascicoli.pratica_id = pratiche.pratica_id
                    ) 
                    WHERE pratiche_fascicoli.pratica_id IN (
                            SELECT pratica_id FROM pratiche WHERE pratiche.PROJECT_ID = :project_id
                            )
                    AND pratiche_fascicoli.fascicolo_id NOT IN  
                        (SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratiche_fascicoli.pratica_id = :pratica_id)
                    ', [
                    ':project_id' => $project['PROJECT_ID'],
                    ':pratica_id' => $this->_praticaObj->PRATICA_ID,
                ])->fetchAll();

                foreach ($fascicoliProgetto as $fascicolo) {
                    $praticheFascicolo = $db->query('SELECT pratica_id FROM pratiche
            	        WHERE pratica_id IN (SELECT pratica_id FROM pratiche_fascicoli WHERE fascicolo_id = :fascicolo_id)
            	        ORDER BY pratiche.pratica_id', [
                        ':fascicolo_id' => $fascicolo['fascicolo_id']
                    ])->fetch();

                    $praticaFascicolo = new Pratica();
                    $praticaFascicolo->setId($praticheFascicolo['pratica_id']);
                    print('<div><ul class="fascicoloPratiche selected">Protocollo');

                    if ($praticaFascicolo->fascicolo) {
                        foreach ($praticaFascicolo->fascicolo as $pratica) {
                            print('<li>' . ($pratica['tipologia'] == 'E' ? '<i class="fa fa-arrow-circle-right"> </i>&nbsp;Entrata' : 'Uscita&nbsp;<i class="fa fa-arrow-circle-left"> </i>'));
                            //                 print('<ul>');
                            switch ($pratica['funzione']) {
                                case 'inizio_sospensione':
                                    $iconaSospensione = '<i class="fa fa-pause"> </i>';
                                    break;
                                case 'fine_sospensione':
                                    $iconaSospensione = '<i class="fa fa-play"> </i>';
                                    break;
                                default:
                                    $iconaSospensione = '';
                                    break;
                            }

                            if ($praticaDesc = $db->query('SELECT pratica_id, numeroregistrazione, dataregistrazione, oggetto FROM pratiche WHERE pratica_id = :pratica_id', array(
                                ':pratica_id' => $pratica['pratica_id'],
                            ))->fetch()
                            ) {
                                print('<ul><li><i class="fa fa-edit fa-2x pratica-fascicolo"  data-pratica-id="' . $praticaDesc['pratica_id'] . '" > </i>' .
                                    $praticaDesc['numeroregistrazione'] . ' - ' .
                                    (new Date($praticaDesc['dataregistrazione']))->toReadable() .
                                    ' : ' . $praticaDesc['oggetto'] . $iconaSospensione .
                                    '</li></ul>');
                            }
                        }
                        //                 print('</ul>');
                        print('</li>');
                    }
                    print('</ul></div>');
                }
            }


            print('</div>');
        }

        return $this;
    }

    protected function editOggetto()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Oggetto" selected="' . $this->isPaneSelected('oggetto') . '" >');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");
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
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editProprietario()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Proprietario" selected="' . $this->isPaneSelected('proprietario') . '" >');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");

        $this->_FormFields['PNOME']->showDivField();
        $this->_FormFields['PTOPONIMO']->showDivField();
        $this->_FormFields['PCIVICO']->showDivField();
        $this->_FormFields['PCAP']->showDivField();
        $this->_FormFields['PCOMUNE']->showDivField();
        $this->_FormFields['PPROVINCIA']->showDivField();
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editMittente()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Mittente/Destinatario" selected="' . $this->isPaneSelected('mittente') . '" >');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");
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
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editAltriDestinatari()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Altri Destinatari" id="altriDestinatari" selected="' . $this->isPaneSelected('destinatari') . '" >');
        print('<div dojoType="dijit.form.Form" jsId="altraDestinazione" id="altraDestinazione" encType="multipart/form-data" action="" method="">');
        print('<script type="dojo/method" event="onReset">');
        print('return true;');
        print(' </script>');
        print('<script type="dojo/method" event="onSubmit">');
        print('if (this.validate()) {
			        	data = this.attr(\'value\');
						inserisciDestinazione(data,' . $this->_FormFields['PRATICA_ID']->GetValue() . ');
			            return false;
			        } else {
			            alert(\'Mancano dati - correggi e aggiorna!\');
			            return false;
			        }
			        return false;');
        print('</script>');


        print('<table class="altriDestinatari">');
        print('<tr><td>
            <button dojoType="dijit.form.Button" id="RicercaMittenteButton">Seleziona</button></td><td>');
        print('<div dojoType="dojox.data.QueryReadStore" ' .
            'url="xml/jsonRicercaMittente.php" ' .
            'jsId="pecRicercaMittenteStore" ' .
            '></div>');

        print('<div dojoType="dijit.form.FilteringSelect"  ' .
            'store="pecRicercaMittenteStore"
							searchAttr="DESCRIPTION" ' .
//            ' pageSize="50" ' .
            ' autoComplete="true" ' .
            'name="RicercaMittente" ' .
            'id="RicercaMittente" ' .
            ' value="" ' .
            ' style="width:800px;" ' .
            ' required="false" ' .
            ' searchDelay="500" ' .
//            ' pageSize="50" ' .
            '></div>');


        print('</td></tr>');

        print('<tr><td class="label">Nome</td>
			            <td><input type="text" id="AD_NOME" name="AD_NOME" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Cognome</td>
			            <td><input type="text" id="AD_COGNOME" name="AD_COGNOME" required="true" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');

        print('<tr><td class="label">Titolo</td><td>');
//        print('<div dojoType="dojo.data.ItemFileReadStore" ' .
//              'url="xml/jsonSql.php?nullValue=N&sql=SELECT distinct titolo as CODE, titolo as DESCRIPTION FROM pratiche ORDER BY 1" ' .
//              'jsId="ad_tipo_anagrafica" ' . '></div>');
//        print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="ad_tipo_anagrafica"
//							searchAttr="DESCRIPTION" ' . 'name="AD_TITOLO" ' . 'id="AD_TITOLO" ' . ' value="" ' . ' style="width:300px;" ' . ' pageSize="100" ' . '></div>');


        print('<div dojoType="dojo.data.ItemFileReadStore" ' .
            'url="xml/jsonSql.php?nullValue=N&sql=SELECT value as CODE, value as DESCRIPTION FROM sys_fields_validations WHERE field_name = \'tipo_anagrafica\' ORDER BY 1" ' .
            'jsId="ad_tipo_anagrafica" ' . '></div>');

        print('<div dojoType="dijit.form.FilteringSelect"  ' .
            'store="ad_tipo_anagrafica" searchAttr="DESCRIPTION" ' .
            'name="AD_TITOLO" ' .
            ' queryExpr="*${0}*" ' .
            'id="AD_TITOLO" ' .
            'required="false" ' .
            'value="" ' .
            ' style="width:300px;" ' . '></div>');
        print('</td></tr>');

        print('<tr><td class="label">Toponimo</td>
			        	<td><input type="text" id="AD_TOPONIMO" name="AD_TOPONIMO" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');

        print('<tr><td class="label">C.A.P.</td>
			            <td><input type="text" id="AD_CAP" name="AD_CAP" required="false" dojoType="dijit.form.ValidationTextBox" /></td>
			        </tr>');
        print('<tr><td class="label">Comune</td>
			            <td><input type="text" id="AD_COMUNE" name="AD_COMUNE" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Provincia</td>
			        	<td><input type="text" id="AD_PROVINCIA" name="AD_PROVINCIA" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Località</td>
			        	<td><input type="text" id="AD_LOCALITA" name="AD_LOCALITA" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');

        print('<tr><td class="label">Telefono</td>
			        	<td><input type="text" id="AD_TELEFONO" name="AD_TELEFONO" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');

        print('<tr><td class="label">Fax</td>
			        	<td><input type="text" id="AD_FAX" name="AD_FAX" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Codice fiscale</td>
			        	<td><input type="text" id="AD_CODICEFISCALE" name="AD_CODICEFISCALE" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Email</td>
			        	<td><input type="text" id="AD_EMAIL" name="AD_EMAIL" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('<tr><td class="label">Pec</td>
			        	<td><input type="text" id="AD_PEC" name="AD_PEC" required="false" dojoType="dijit.form.ValidationTextBox"/></td>
			        </tr>');
        print('</table>');
        print('<button dojoType="dijit.form.Button" type="submit" name="submitButton" value="Submit">Aggiungi Indirizzo</button>');
        print('<button dojoType="dijit.form.Button" type="reset">Annulla</button>');
        print('</div>');
        print('<div dojoType="dijit.layout.ContentPane" id="dispDestinazioni" href="djGetDestinazioni.php?praticaId=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
        print('</div>');
        print('</div>');
    }

    protected function editVincoli()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Vincoli" selected="' . $this->isPaneSelected('vincoli') . '" >');
        include('skVincoli.inc');
        print('</div>');
    }

    protected function editIstruttoria()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Istruttoria" selected="' . $this->isPaneSelected('istruttoria') . '" >');
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['ISTR01']->showDivField();
        $this->_FormFields['ISTR02']->showDivField();
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editIntegrazioni()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Monumentale" selected="' . $this->isPaneSelected('integrazioni') . '" >');
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['NOTE01']->showDivField();
        $this->_FormFields['ISTR03']->showDivField();
        $this->_FormFields['NOTE02']->showDivField();
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editPaesaggio()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Paesaggistica" selected="' . $this->isPaneSelected('paesaggio') . '" >');
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['PAE_DATA_PARERE']->showDivField();
        $this->_FormFields['PAE_LOC_INTERVENTO']->showDivField();
        $this->_FormFields['PAE_VAL_NCONDIVISE']->showDivField();
        $this->_FormFields['PAE_DESC_INCOMPATIBILITA']->showDivField();
        $this->_FormFields['PAE_MOTIVAZIONI']->showDivField();
        $this->_FormFields['PAE_NOTE_PRESCRIZIONI']->showDivField();
        $this->_FormFields['PAE_INTEGRAZIONI']->showDivField();
        $this->_FormFields['DESCRIZIONE']->showDivField();
        print('</fieldset>' . "\n");
        print('</div>');
    }

    protected function editContributi()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Contributi" selected="' . $this->isPaneSelected('contributi') . '" >');
            print('<fieldset style="border:none">' . "\n");
            $this->_FormFields['CONTRIBUTI']->showDivField();
            $this->_FormFields['CONT_RIFAUTLAV']->showDivField();
            $this->_FormFields['CONT_TIPINT']->showDivField();
            $this->_FormFields['CONT_TIPNONAMM']->showDivField();
            $this->_FormFields['CONT_DATAIST']->showDivField();
            include('skContributi.inc');
            print('</fieldset>' . "\n");

//            print('<div dojoType="dijit.layout.ContentPane" id="dispContributi" href="djGetContributi.php?praticaId='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
//            print ('</div>');
        print ('</div>');

    }

    function editOrganizzazione()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Unit&agrave; Organizzative" id="unitaOrganizzative" selected="' . $this->isPaneSelected('uo') . '" >
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
                    <div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=select UOID, DESCRIPTION from arc_organizzazione  where uoid <> 1 order by DESCRIPTION" ' . 'jsId="selUOID" ' . '>
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

        return $this;
    }

    protected function editUploads()
    {
        print('<div dojoType="dijit.layout.ContentPane" id="paneUploads" title="Uploads" selected="' . $this->isPaneSelected('paneUploads') . '" >');
        print('<div style="margin:10px 5px;">
						<a  href="#" onclick="openUploadsDialog()">
                            <i class="fa fa-upload" > </i>
							<span style="margin:5px;">Carica Files</span>

						</a>
				</div>');
        print('<div dojoType="dijit.layout.ContentPane" id="dispUploads" href="djGetUploads.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
        print('</div>');
        print('</div>');
    }

    protected function editPecfiles()
    {
        print('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC" selected="' . $this->isPaneSelected('pec') . '" >');
        print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djGetEmlpecFile.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '&type=PEC" >');
        print('</div>');
        print('</div>');
    }

    protected function protocollaEntrata()
    {
        $isProtocollatore = Db_Pdo::getInstance()->query('SELECT * FROM sys_responsabilities resp
            RIGHT JOIN sys_user_resp_reference surr ON (resp.resp_id = surr.resp_id)
            WHERE surr.user_id IN (:admin, :user_id) AND resp.description = "Protocollazione"', array(
            ':admin' => 1,
            ':user_id' => $_SESSION['user_id']
        ))->fetch();
        if ($isProtocollatore) {
            if (is_array($this->_praticaObj->getInfo('progetto'))) {
                $fascicoloDescription = $this->_praticaObj->getInfo('progetto')['DESCRIPTION'];
            }
            print('<div dojoType="dijit.layout.ContentPane" id="protocollaUscita" title="Protocollazione" selected="' . $this->isPaneSelected('protocollazione') . '" >');
            print('<div class="dbFormContainer protocollazione">');
            print('<div dojoType="dijit.form.Form" jsId="formProtocollazione" id="formProtocollazione" encType="multipart/form-data"
                        action="" method="">');
            print('<input type="hidden" maxlength="150" size="10" value="' . $this->_FormFields['PRATICA_ID']->getValue() . '"
                            name="ws_pratica_id" >');
            if ($uoid = Db_Pdo::getInstance()->query('SELECT uoid FROM arc_pratiche_uo WHERE pratica_id = :pratica_id', [
                ':pratica_id' => $this->_FormFields['PRATICA_ID']->getValue()
            ])->fetchAll()
            ) {
                $x = 0;
                foreach ($uoid as $uo) {
                    print('<input type="hidden"  value="' . $uo['uoid'] . '"
                            name="uoid[' . $x . ']" />');
                    $x++;
                }
            }
            print('<input id="ws_tipologia" type="hidden" maxlength="150" size="10" value="' . ($this->_FormFields['TIPOLOGIA']->getValue()) . '" name="ws_protocollazione" >');
            print('<fieldset><legend>Pratica</legend>');
            // Fascicolo
            print('<label>Fascicolo</label>
                        <input class="djCodice" type="TEXT" maxlength="150" size="10" value="
                        ' . $fascicoloDescription . '
                        "
                            id="DesFascicolo"
                            name="DesFascicolo" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');

            // Titolario
            $modello = Db_Pdo::getInstance()->query('SELECT * FROM arc_modelli WHERE modello = :modello', array(
                ':modello' => $this->_FormFields['MODELLO']->getValue()
            ))
                ->fetch();
            $titolario = array(
                'ClasseTitolario' => $modello['CLASSIFICAZIONE'],
                'DesTitolario' => $modello['DESCRIPTION']
            );
            print('<label>Titolario</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="' . $modello['CLASSIFICAZIONE'] . '" id="ClasseTitolario" name="ClasseTitolario" dojoType="dijit.form.ValidationTextBox"> -
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="40" value="' . $modello['DESCRIPTION'] . '" id="DesTitolario" name="DesTitolario" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            // Testata Documento
            print('<label>Oggetto</label>');
            print('<textarea  id="clsTestataDocumento_Oggetto" name="clsTestataDocumento[Oggetto]" dojoType="dijit.form.Textarea">' . $this->_FormFields['COMUNEOGG']->getValue() . '</textarea>');
            print('<br />');
            print('<label>Data Protocollazione</label><input class=" djCodice" type="TEXT" maxlength="150" size="20" value="' . date('d/m/Y') . '" id="clsTestataDocumento_Data" name="clsTestataDocumento[Data]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Note</label>');
            print('<textarea  name="clsTestataDocumento[Note]" dojoType="dijit.form.Textarea">' . $this->_FormFields['NOTE']->getValue() . '</textarea>');
            print('</fieldset>');
            // Mittente
            $i = 0;
            print('<fieldset><legend>Mittente</legend>');

            print('<label>Escludi indirizzo dal protocollo</label><input class="escludiDestinatario" name="clsTMittenteDestinatario[escludi]" dojoType="dijit.form.CheckBox" value="1" >');
            print('<br />');
            print('<label>Titolo</label>');
            print('<div dojoType="dojo.data.ItemFileReadStore" ' . 'url="xml/jsonSql.php?nullValue=N&sql=SELECT distinct titolo as CODE, titolo as DESCRIPTION FROM arc_mittenti ORDER BY 1" ' . 'jsId="sel_tipo_anagrafica" ' . '></div>');
            print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="sel_tipo_anagrafica"
							searchAttr="DESCRIPTION" ' . 'name="clsTMittenteDestinatario[DesTipoAnagrafica]" ' . 'id="clsTMittenteDestinatario[DesTipoAnagrafica]" ' . ' value="' . $this->_FormFields['TITOLO']->getValue() . '" ' . ' style="width:300px;" ' . '></div>');
            print('<br />');
            print('<label>Nome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['NOME']->getValue() . '"
                                name="clsTMittenteDestinatario[Nome]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Cognome</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['COGNOME']->getValue() . '"
                                name="clsTMittenteDestinatario[Cognome]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Codice Fiscale</label>
                            <input class="djDescrizione" type="TEXT" maxlength="150" size="30" value="' . $this->_FormFields['CODICEFISCALE']->getValue() . '"
                                name="clsTMittenteDestinatario[CF]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Indirizzo</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="100" value="' . $this->_FormFields['TOPONIMO']->getValue() . '"
                                name="clsTMittenteDestinatario[Indirizzo]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Località</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['LOCALITA']->getValue() . '"
                                name="clsTMittenteDestinatario[Localita]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>C.A.P.</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="10" value="' . $this->_FormFields['CAP']->getValue() . '"
                                name="clsTMittenteDestinatario[CAP]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Comune</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="80" value="' . $this->_FormFields['COMUNE']->getValue() . '"
                                name="clsTMittenteDestinatario[Comune]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Provincia</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['PROVINCIA']->getValue() . '"
                                name="clsTMittenteDestinatario[Provincia]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Telefono</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['TELEFONO']->getValue() . '"
                                name="clsTMittenteDestinatario[Telefono]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Fax</label>
                            <input class=" djCodice" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['FAX']->getValue() . '"
                                name="clsTMittenteDestinatario[Fax]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Email</label>
                            <input class=" djDescrizione" type="TEXT" maxlength="150" size="50" value="' . $this->_FormFields['EMAIL']->getValue() . '"
                                name="clsTMittenteDestinatario[Email]" dojoType="dijit.form.ValidationTextBox">');
            print('<br />');
            print('<label>Per Conoscenza</label><input name="clsTMittenteDestinatario[PerConoscenza]" dojoType="dijit.form.CheckBox" value="1" >');
            print('</fieldset>');
            $praticheRel = Db_Pdo::getInstance()->query(
                'SELECT * FROM pratiche_fascicoli pf1
                                RIGHT JOIN pratiche_fascicoli pf2 ON (pf2.fascicolo_id = pf1.fascicolo_id)
                                 WHERE pf1.pratica_id = :pratica_id ',
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

            $altreDestinazioni = Db_Pdo::getInstance()->query('SELECT * FROM arc_destinazioni WHERE pratica_id IN (' . implode(',', $arrayPratiche) . ')');

            while ($destinazione = $altreDestinazioni->fetch()) {
                $i++;
                print('<fieldset><legend>Altra Destinazione</legend>');
                print('<label>Escludi indirizzo dal protocollo</label><input class="escludiDestinatario" name="clsTMittenteDestinatario[' . $i . '][escludi]" dojoType="dijit.form.CheckBox" value="1" >');
                print('<br />');
                print('<label>Titolo</label>');

                print('<div dojoType="dijit.form.FilteringSelect"  ' . 'store="sel_tipo_anagrafica"
        							searchAttr="DESCRIPTION" ' . 'name="clsTMittenteDestinatario[' . $i . '][DesTipoAnagrafica]" ' . 'id="clsTMittenteDestinatario[' . $i . '][DesTipoAnagrafica]" ' . '
                        value="' . $destinazione['TITOLO'] . '" ' . ' style="width:300px;" ' . '></div>');
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

    function displayForm()
    {
        /* Form container */
        // var_dump($this->_FormFields['USCITA']);
        print('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");
        print($this->GetFormHeader());
        print('<form id="99" name="PRATICHE" method="POST" enctype="multipart/form-data" action="?PRATICA_ID=' . $this->GetFormFieldValue('PRATICA_ID') . '">');
        // Titolo
        print('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        $this->showButtonBar();
        print('<div id="praticheTabs" dojoType="dijit.layout.TabContainer"
					style="width:100%; height:100%; margin:0px;" doLayout="false">');
        print('<div dojoType="dijit.layout.ContentPane" title="Pratica">');
        // print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
        print('<fieldset style="border:none">' . "\n");
        $this->_FormFields['PRATICA_ID']->showDivField();
        $this->_FormFields['DATAREGISTRAZIONE']->SetShowed('R');
        $this->_FormFields['DATAREGISTRAZIONE']->dispDivField();
        $this->_FormFields['NUMEROREGISTRAZIONE']->SetShowed('R');
        $this->_FormFields['NUMEROREGISTRAZIONE']->dispDivField();

        print('<label for="PRATICA_USCITA_ID">Pratica</label>');

        $modello = Db_Pdo::getInstance()->query('SELECT * FROM arc_modelli WHERE modello = :modello', array(
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
            ->fetch()
        ) {
            print('<span class="protoUscita">
		                  <a href="editPratica.php?PRATICA_ID=' . $protoUscita['PRATICA_USCITA_ID'] . '" title="Apri Protocollo in Uscita">
		              ' . $protoUscita['NUMEROREGISTRAZIONE'] . ' del ' . (new Date($protoUscita['DATAREGISTRAZIONE']))->toReadable() . '</a>
	              </span>');
        } else {
            print('<span class="protoUscita">Protocollata manualmente</span>');
        }
        print('<br />');

        $this->_FormFields['SCADENZA']->dispDivField();
         $this->_FormFields['RESPONSABILE']->dispDivField();
//        $this->_FormFields['RESPONSABILE_ID']->dispDivField();
        $this->_FormFields['ESITO_ID']->dispDivField();
//        $this->_FormFields['FALDONE']->showDivField();

        // print ('<label for="TITOLAZIONE">Titolazione</label>');
        //
        // $findFascicoloSql = 'select al1.description as LIV01,' .
        // 'al2.description as LIV02, ' .
        // 'al3.description as LIV03, ' .
        // 'concat(ac.comune,\' ( \',ac.provincia,\')\') as COMUNE, ' .
        // 'at.fascicolo as FASCICOLO ' .
        // 'from arc_titolazioni at ' .
        // 'right join pratiche pr on (pr.titolazione = at.id) ' .
        // 'right join arc_comuni ac on (ac.id = at.comune) ' .
        // 'right join arc_titolario al3 on (al3.titolo = at.titolo) ' .
        // 'right join arc_tito02 al2 on ((al2.liv01 = al3.liv01) and (al2.liv02 = al3.liv02)) ' .
        // 'right join arc_tito01 al1 on (al1.liv01 = al2.liv01) ' .
        // 'where pr.pratica_id='.$this->_FormFields['PRATICA_ID']->GetValue();
        //
        // print('<div style="display: block; font-weight: bold;" id="dispFascicolo">');
        // if ($fascicolazioneResult=dbselect($findFascicoloSql)){
        // print($fascicolazioneResult['ROWS'][0]['LIV01'].'->'.$fascicolazioneResult['ROWS'][0]['LIV02'].'->'.$fascicolazioneResult['ROWS'][0]['LIV03'].'->');
        // print($fascicolazioneResult['ROWS'][0]['COMUNE'].'->');
        // print($fascicolazioneResult['ROWS'][0]['FASCICOLO'].'<br/>');
        // $buttonTitola='Riesegui Titolazione';
        // } else {
        // print('- Titolazione non eseguita -');
        // }
        // print('</div>');
        //

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
        include('skVincoli.inc');
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

            if ($tipoPraticaResult = dbselect('SELECT * FROM arc_modelli WHERE modello = ' . $this->_FormFields['MODELLO']->GetValue())) {

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

    protected function dispUploads()
    {
        print('<div dojoType="dijit.layout.ContentPane" title="Uploads" id="uploadsPane">');

        print('
						<a href="#" onclick="openUploadsDialog()">
							<span style="margin-bottom:20px; margin-top: 0px;">Carica Files</span>
							<img src="graphics/uploads_32.png" >
							</a>
							');

        print('<div dojoType="dijit.layout.ContentPane" id="dispUploads" href="djGetUploads.php?mode=ro&PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
        print('</div>');
        print('</div>');
    }

    protected function dispPecfiles()
    {
        print('<div dojoType="dijit.layout.ContentPane" id="pecPane" title="mail PEC">');
        print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djGetEmlpecFile.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '" >');
        print('</div>');
        print('</div>');
    }

    function FormPostValidation()
    {

        return TRUE;
    }

    public function editMenu()
    {


        print ('<div>' . "\n");
        print ('<div style="float: left;">' . "\n");

        print ('<input id="selDocuments">' .
            '<button id="creaButton" type="button"  disabled="disabled" ' .
            '	onclick="return creaDaModello(\'' . $this->_FormFields['PRATICA_ID']->GetValue() . '\')" >Crea da Modello</button>');


        print ('</div>' . "\n");
        print ('<div class="editMenu">' . "\n");

        if ($this->GetFormFieldValue('USCITA') > ' ') {
            print('Pratica chiusa');
        } else {
            if ($this->_FormFields['TIPOLOGIA']->GetValue() == 'E') {
                if (!$sospensione = Db_Pdo::getInstance()->query('SELECT * FROM arc_sospensioni WHERE pratica_id = :pratica_id OR protouscita = :pratica_id OR protoentrata = :pratica_id', [
                    ':pratica_id' => $_GET['PRATICA_ID']
                ])->fetch()
                ) {

                    /*
                     * La pratica non è sospesa ne ha sospeso o riattivato un altra pratica perciò puo essere sospesa o riprendere una pratica sospesa
                     */
                    print ('<a href="?sospensione=Y&PRATICA_ID=' . $_GET['PRATICA_ID'] . '"><i class="fa fa-pause"> </i>Sospendi Procedimento</a>');
                    print ('<a href="praticaRiattivazione.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '"><i class="fa fa-play"> </i>Riprendi Procedimento</a>');

                }

            }
        }

        print ('<a href="vincoliPaesaggistici.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '">Vincoli Paesaggistici</a><img src="graphics/photo_1.png" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli Paesaggistici" >');

        print ('<a href="vincoliMonumentali.php?PRATICA_ID=' . $this->_FormFields['PRATICA_ID']->GetValue() . '' .
            //				'&keyword=' .$ManagedTable->GetFormFieldValue('OGGETTO').
            '&foglioFilter=' .$this->GetFormFieldValue('FOGLIO').
            '&mappaleFilter=' .$this->GetFormFieldValue('MAPPALE').
            '&anaFilter=' .$this->GetFormFieldValue('MAPPALE').
            '">Vincoli Monumentali</a><img src="graphics/home.png" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli Monumentali" >');



        print ('</div>' . "\n");
        print ('<div style="clear: both;"></div>' . "\n");


        return $this;
    }

    protected function editTitolazione(){
        // Titolazione
        if($this->_FormFields['MODELLO']->GetValue() > ''){
            print ('<div dojoType="dijit.layout.ContentPane" title="Titolazione" id="titolazione">');
            //		print ('<div style="background-color: azure; font-size: 1.5em;">' . $this->GetFormTitle() . '</div>' . "\n");
            print ('<fieldset style="border:none">' . "\n");

            $titoloId = false;

            $classifica = Db_Pdo::getInstance()->query('SELECT CLASSIFICAZIONE, DESCRIPTION FROM arc_modelli WHERE MODELLO = :modello',[
                ':modello' => $this->_FormFields['MODELLO']->GetValue(),
            ])->fetch();

            if(is_array($classifica)){
                print ('<label>Classifica</label>');
                $cl = explode('.', $classifica['CLASSIFICAZIONE']);

                $titoloId = Db_Pdo::getInstance()->query('SELECT TITOLO FROM arc_titolario 
                WHERE LIV01 = :liv01 AND LIV02 = :liv02 AND LIV03 = :liv03', [
                    ':liv01' => $cl[0],
                    ':liv02' => $cl[1],
                    ':liv03' => $cl[2],
                ])->fetchColumn();

            } else {
                print ('<label>Impostare il tipo di procedimento</label>');
            }




            if(!$titoloId){
                print ('Voce di titolario ' . $classifica['CLASSIFICAZIONE'] . ' - ' . $classifica['DESCRIPTION'] . ' non trovata');
            } else {

                print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
                    'url="xml/jsonSql.php?sql=select * from arc_province " ' .
                    'jsId="sProvince" ' .
                    '></div>');
                print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
                    'url="xml/jsonSql.php?sql=select * from arc_comuni " ' .
                    'jsId="sComuni" ' .
                    '></div>');

                print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
                    'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_titolazioni where TITOLO = ' . $titoloId . ' "' .
                    'jsId="sFascicoli" ' .
                    '></div>');

                print ('<script language="JavaScript" type="text/javascript">

                dojo.addOnLoad(function(){
                    new dijit.form.FilteringSelect({
                            store: sProvince,
                            labelAttr: \'PROVINCIA\',
                            searchAttr: \'PROVINCIA\',
                            name: "SIGLA",
                            autoComplete: true,
                            style: "width: 250px;",
                            id: "SIGLA",
                            onChange: function(SIGLA) {
                                dijit.byId(\'TITOCOMUNE\').query.PROVINCIA = dijit.byId(\'SIGLA\').item.SIGLA[0] ;
                                return true;
                            }
                        },
                        "SIGLA");

                    new dijit.form.FilteringSelect({
                            store: sComuni,
                            labelAttr: \'COMUNE\',
                            searchAttr: \'COMUNE\',
                            name: "TITOCOMUNE",
                            autoComplete: true,
                            style: "width: 250px;",
                            query : { PROVINCIA : "*"},
                            id: "TITOCOMUNE",
                            onChange: function(ID) {

                                dijit.byId(\'FASCICOLO\').query.COMUNE = ID ;
                                return true;
                            }
                        },
                        "TITOCOMUNE");

                    new dijit.form.FilteringSelect({
                            store: sFascicoli,
                            labelAttr: \'FASCICOLO\',
                            searchAttr: \'FASCICOLO\',
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

            </script>');

                print( $classifica['CLASSIFICAZIONE'] . ' - ' . $classifica['DESCRIPTION'] );
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
                    'onClick="return titola(\'' . $this->_FormFields['PRATICA_ID']->GetValue() . '\',\'' . $titoloId . '\');">' .
                    $buttonTitola .
                    '</button>');

            }

            print ('</div>');

        }

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

    protected function getLastUpdate(){
        $latUpdate = Db_Pdo::getInstance()->query('SELECT DATE_FORMAT(pratiche.updated, "%d/%m/%Y") as updated,
	            pratiche.updated as data,
	            sys_users.last_name, sys_users.first_name FROM pratiche
	            LEFT JOIN sys_users ON (sys_users.user_id = pratiche.updated_by)
	            WHERE pratiche.pratica_id = :pratica_id',array(
            ':pratica_id' => $this->GetFormFieldValue('PRATICA_ID')))->fetch();


        return ($latUpdate ? $latUpdate['first_name'] . ' ' . $latUpdate['last_name'] . ' - ' . $latUpdate['updated'] : '');
    }

}
