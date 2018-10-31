<?php
/**
 * Dialog Box per uploads files
 */
print ('<div id="dlgFilesUploads" dojoType="dijit.Dialog" title="Upload file" >') ;
print ('<script type="text/javascript">
						dojo.require("dijit.layout.ContentPane");
						dojo.require("dojo.io.iframe");
						dojo.require("dijit.ProgressBar");
						dojo.require("dijit.form.Button");
						dojo.require("dojo.parser");
					</script>') ;
print ('<form id="filesForm" enctype="multipart/form-data" accept-charset="utf-8" name="skUploads" action="uploadsIframeUploads.php" method="post" >') ;
print ('<div id="uploadContainer" style="width: 100%; margin-bottom: 2em;" >
						<span id="preamble">Carica il File:</span><br>
					    <span id="inputField">
					    <input type="hidden" id="up_PRATICA_ID" value="' . $this->_FormFields ['PRATICA_ID']->GetValue () . '" name="up_PRATICA_ID">
					    <input type="file" id="up_FILENAME" name="up_FILENAME"></br>
					    <label for="up_DESCRIPTION">Descrizione del file </label><input type="text" id="up_DESCRIPTION" name="up_DESCRIPTION">
						<br>
			            <div id="up_PUBBLICA" >Pubblica <input type="checkbox" name="up_PUBBLICA" id="up_pubblica" value="Y" ></div><br>
						<div id="campiPubblicazioneAtto" style="display: none;">

							<div>
								<label for="AMBITO">Ambito</label>
					            <select name="AMBITO" id="ambito" dojoType="dijit.form.FilteringSelect" style="width: 400px;">
								  <option selected="selected" value="Tutela">Tutela</option>
								  <option value="Valorizzazione">Valorizzazione</option>
					            </select>
							</div>
							<br>
							<div>
								<label for="SETTORE">Settore</label>
								<select name="SETTORE" id="settore" dojoType="dijit.form.FilteringSelect" style="width: 400px;">
								  <option selected="selected" value="Architettonico">Architettonico</option>
								  <option value="Archeologico">Archeologico</option>
								  <option value="Archivistico">Archivistico</option>
								  <option value="Librario">Librario</option>
								  <option value="Paesaggistico">Paesaggistico</option>
								  <option value="Storico Artistico">Storico Artistico</option>
				                </select>
							</div>
							<br>
							<div>
								<label for="NATURA_ATTO">Natura atto</label>
								<select name="NATURA_ATTO" id ="natura_atto" dojoType="dijit.form.FilteringSelect">
							          <option value="ACCORDI">ACCORDI</option>
							          <option value="AUTORIZZAZIONE">AUTORIZZAZIONE</option>
							          <option value="CONCESSIONI">CONCESSIONI</option>
							          <option value="CONVENZIONI">CONVENZIONI</option>
							          <option value="DICHIARAZIONI">DICHIARAZIONI</option>
							          <option value="VERBALE">VERBALE</option>
							          <option value="ALTRO">ALTRO</option>
							        </select>
							</div>
							<br>
							<div>
								<label for="TIPO_PROCEDIMENTO">Procedimento</label>

								<select name="TIPO_PROCEDIMENTO" id="tipo_procedimento" dojoType="dijit.form.FilteringSelect" style="width: 400px;">
									<option value="160">Accettazione di donazioni di modico valore offerta dal privato</option>
									<option value="30">Accettazione di doni e lasciti testamentari in favore degli Istituti archivistici: nei casi di modico valore </option>
									<option value="29">Accettazione di doni e lasciti testamentari in favore degli Istituti archivistici: procedura ordinaria</option>
									<option value="26">Acquisti di cose o beni culturali</option>
									<option value="156">Acquisti in via di prelazione di beni culturali da parte dello Stato in materia di beni architettonici</option>
									<option value="120">Acquisto coattivo di beni archivistici presentati per l\'uscita dal territorio nazionale </option>
									<option value="161">Acquisto coattivo di beni mobili per l\'uscita dal territorio nazionale</option>
									<option value="121">Acquisto di beni archivistici mediante esercizio del diritto di prelazione</option>
									<option value="122">Acquisto di beni archivistici mediante esercizio del diritto di prelazione, in caso di omessa, incompleta o tardiva denuncia</option>
									<option value="162">Acquisto di beni mobili mediante l\'esercizio del diritto di prelazione</option>
									<option value="105">Adozione della tabella triennale degli istituti culturali ammessi al contributo ordinario dello Stato</option>
									<option value="106">Adozione piano di ripartizione dei contributi annuali agli istituti culturali </option>
									<option value="147">Ammissione a consultare archivi privati sottoposti a vincolo </option>
									<option value="135">Ammissione a eseguire ricerche per ragioni di studio</option>
									<option value="125">Ammissione al pagamento delle imposte di successione mediante cessione di beni archivistici</option>
									<option value="159">Ammissione al pagamento delle imposte di successione mediante cessione di beni culturali in materia di beni architettonici (limitatamente alle opere contemporanee)</option>
									<option value="158">Ammissione al pagamento delle imposte dirette mediante cessione di beni culturali in materia di beni architettonici  (limitatamente alle opere contemporanee)</option>
									<option value="124">Ammissione al pagamento delle imposte mediante cessione di beni archivistici </option>
									<option value="95">Approvazione in via sostitutiva dei piani paesaggistici sottoposti a verifica e adeguamento oggetto di accordo tra Ministero e Regione </option>
									<option value="94">Approvazione in via sostitutiva piani paesaggistici sottoposti a verifica e adeguamento </option>
									<option value="42">Approvazione piani di conservazione degli archivi delle PP.AA. statali e non statali </option>
									<option value="20">Approvazione piani paesaggistici in via sostitutiva, limitatamente ai beni paesaggistici </option>
									<option value="99">Attestazione ai fini della riduzione dell\'imposta di successione su beni culturali </option>
									<option value="98">Attestazione esistenza vincolo su beni culturali ai fini dell\'esclusione dall\'attivo ereditario</option>
									<option value="141">Autorizzazione a eseguire con mezzi propri fotografie, calchi e lucidi di documenti conservati negli Archivi di Stato: riproduzione esterna all\'Istituto</option>
									<option value="139">Autorizzazione a eseguire con mezzi propri fotografie, calchi e lucidi di documenti conservati negli Archivi di Stato: riproduzione interna all\'Istituto </option>
									<option value="19">Autorizzazione a pubblicare in fac-simile documenti conservati negli Archivi di Stato: per interi fondi</option>
									<option value="138">Autorizzazione a pubblicare in facsimile documenti conservati negli Archivi di Stato: per singoli documenti</option>
									<option value="4">Autorizzazione ad acquisire in comodato beni culturali di privata proprietà e in deposito beni culturali appartenenti ad enti pubblici</option>
									<option value="8">Autorizzazione al collocamento o all\'affissione di cartelli o altri mezzi di pubblicità sugli edifici e nelle aree tutelati come beni culturali </option>
									<option value="84">Autorizzazione al distacco di affreschi, stemmi, graffiti, lapidi, iscrizioni, tabernacoli ed altri elementi decorativi di edifici, esposti o non alla pubblica vista</option>
									<option value="137">Autorizzazione al rilascio di riproduzioni a pagamento</option>
									<option value="2">Autorizzazione al trasferimento di complessi organici di documentazione di archivi vigilati</option>
									<option value="86">Autorizzazione all\'alienazione di beni culturali appartenenti a Stato, regioni e altri enti pubblici territoriali, nonchè a enti e istituti pubblici o a persone giuridiche private senza fine di lucro</option>
									<option value="85">Autorizzazione all\'alienazione di beni immobili appartenenti al demanio culturale</option>
									<option value="14">Autorizzazione all\'esecuzione di ricerche archeologiche su immobile proprio</option>
									<option value="18">Autorizzazione all\'uso strumentale e precario nonché alla riproduzione di beni culturali</option>
									<option value="133">Autorizzazione alla chiusura temporanea al pubblico degli Archivi di Stato</option>
									<option value="88">Autorizzazione alla permuta di beni culturali pubblici</option>
									<option value="1">Autorizzazione allo scarto di documenti non statali sottoposti a tutela</option>
									<option value="17">Autorizzazione concessione in uso di beni culturali in consegna ad amministrazioni dello Stato, regioni o altri enti pubblici territoriali</option>
									<option value="77">Autorizzazione demolizione, rimozione o spostamento di beni culturali</option>
									<option value="15">Autorizzazione deposito temporaneo materiale archeologico di proprietà dello Stato presso istituti di regioni o altri enti pubblici territoriali</option>
									<option value="79">Autorizzazione interventi su beni culturali ad esclusione di quelli indicati</option>
									<option value="100">Autorizzazione opere nel territorio di Paestum </option>
									<option value="186">Autorizzazione per analisi o interventi di conservazione all estero</option>
									<option value="5">Autorizzazione prestito beni mobili per mostre o esposizioni sul territorio nazionale o all\'estero</option>
									<option value="78">Autorizzazione smembramento collezioni, serie o raccolte di oggetti </option>
									<option value="11">Certificazione di avvenuta spedizione o di avvenuta importazione nel territorio nazionale di beni culturali</option>
									<option value="22">Certificazione, ai fini deducibilità dal reddito d\'impresa, somme erogate a titolo liberale per realizzazione programmi culturali nel settore e per acquisto, manut.ne, protezione e restauro beni culturali </option>
									<option value="103">Certificazione, ai fini della detraibilità dalle imposte dirette, del carattere necessario e della congruità delle spese per interventi conservativi sui beni culturali</option>
									<option value="81">Concessione contributo in conto capitale per spese relative ad interventi conservativi su beni culturali</option>
									<option value="82">Concessione contributo in conto interessi per spese relative ad interventi conservativi su beni culturali</option>
									<option value="6">Concessione garanzia governativa sui beni culturali prestati per mostre e manifestazioni culturali</option>
									<option value="16">Concessione in uso beni culturali dello Stato in consegna al Ministero</option>
									<option value="87">Concessione in uso o locazione di immobili pubblici di interesse culturale per le finalitàdi valorizzazione e utilizzazione, anche a fini economici</option>
									<option value="13">Concessione per l\'esecuzione di ricerche archeologiche su immobile altrui</option>
									<option value="3">Custodia coattiva di beni culturali</option>
									<option value="154">Deposito atti tra vivi di trasferimento diritti d’autore e registrazione sul relativo Registro Pubblico Generale delle opere protette Parte IV</option>
									<option value="152">Deposito opere protette dalla legge sul diritto d’autore e registrazione sul relativo Registro Pubblico Generale Parte I</option>
									<option value="153">Deposito progetti di lavori dell’ingegneria o di lavori analoghi e registrazione sul relativo Registro Pubblico Generale delle opere protette Parte II</option>
									<option value="89">Determinazione del premio per i ritrovamenti  </option>
									<option value="7">Dichi.ne rilevante interesse culturale di mostre, esposizioni ed altre iniziative a carattere culturale ai fini applicazione agev.zioni fiscali</option>
									<option value="76">Dichiarazione di importante carattere artistico</option>
									<option value="36">Dichiarazione di particolare importanza degli archivi degli enti pubblici</option>
									<option value="91">Dichiarazione di pubblica utilità ai fini dell\'espropriazione a favore delle regione e degli altri enti territoriali </option>
									<option value="92">Dichiarazione di pubblica utilità ai fini dell\'espropriazione a favore di persone giuridiche private senza fini di lucro</option>
									<option value="90">Dichiarazione di pubblica utilità ai fini dell\'espropriazione di beni culturali </option>
									<option value="157">Dichiarazione di pubblica utilità per espropriazione di beni immobili per fini strumentali in materia di beni architettonici</option>
									<option value="143">Estrazione temporanea, per motivi di pubblico servizio, di documenti conservati negli Archivi di Stato</option>
									<option value="80">Imposizione interventi conservativi su beni culturali</option>
									<option value="83">Imposizione prescrizioni a garanzia della integrità, prospettiva, condizioni di ambiente e di decoro di beni culturali</option>
									<option value="93">Integrazione in via sostitutiva del contenuto delle dichiarazioni di notevole interesse pubblico di beni paesaggistici </option>
									<option value="43">Iscrizione nell\'elenco degli istituti e dei dipartimenti archeologici universitari, nonché delle persone fisiche titolate, ai fini della verifica preventiva di interesse archeologico  </option>
									<option value="130">Misure a tutela dei documenti degli enti pubblici che si trovino al di fuori dei rispettivi archivi</option>
									<option value="129">Misure a tutela dei documenti dello Stato che si trovino al di fuori dei rispettivi archivi</option>
									<option value="104">Misure di sostegno per gli interventi di tutela e fruizione dei siti italiani di interesse culturale, paesaggistico e ambientale, inseriti nella lista del patrimonio mondiale dell\'UNESCO</option>
									<option value="131">Misure per lo spostamento di beni archivistici,  in conseguenza del trasferimento di sede o di dimora del detentore</option>
									<option value="97">Ordine di pagamento indennità pecuniaria per danni a beni culturali </option>
									<option value="96">Ordine di reintegrazione di beni culturali oggetto di interventi lesivi</option>
									<option value="145">Parere al Ministero dell\'Interno sull\'accertamento della natura riservata di documenti conservati negli Archivi di Stato, prodotti da uffici statali ovvero di proprietà di enti pubblici o privati</option>
									<option value="144">Parere al Ministero dell’Interno ai fini dell’autorizzazione a consultare  per motivi di studio documenti di carattere riservato</option>
									<option value="9">Parere nell\'ambito del proc. di autorizzazione al collocamento di cartelli o altri mezzi di pubblicità lungo le strade site nell\'ambito o in prossimità degli edifici e nelle aree tutelati come beni culturali</option>
									<option value="10">Parere nell\'ambito del proc. di individuazione delle aree pubb. aventi valore archeolog., storico, artistico e paesagg. nelle quali vietare o sottoporre a condizioni particolari l\'esercizio del commercio</option>
									<option value="164">Parere vincolante autorizzazione paesaggistica</option>
									<option value="165">Parere vincolante autorizzazione paesaggistica di lieve entità - procedimento semplificato</option>
									<option value="166">Parere vincolante autorizzazione paesaggistica in sanatoria (per i procedimenti anteriori al 2004)</option>
									<option value="21">Parere vincolante su domande di autorizzazione paesaggistica in sanatoria </option>
									<option value="187">PROCEDIMENTO DA AGGIORNARE</option>
									<option value="146">Riconoscimento del carattere riservato di documenti privati di interesse storico particolarmente importante degli ultimi settanta anni</option>
									<option value="155">Ricorsi amministrativi avverso i provvedimenti conclusivi del procedimento di verifica di interesse culturale in materia di beni architettonici</option>
									<option value="128">Ricorso avverso il diniego dell’attestato di libera circolazione</option>
									<option value="140">Ricorso avverso la verifica di interesse culturale o la dichiarazione di interesse storico particolarmente importante di beni archivistici di proprietà privata</option>
									<option value="136">Rilascio copie autentiche di documenti conservati negli Archivi di Stato, per motivi non di studio</option>
									<option value="12">Rilascio licenza di esportazione di beni culturali </option>
									<option value="123">Rinuncia all\'esercizio del diritto di prelazione e trasferimento  della facoltà all\'ente interessato </option>
									<option value="31">Scambio alla pari di microfilms positivi o negativi con enti culturali o amministrazioni archivistiche straniere  </option>
									<option value="101">Scarto di documenti conservati negli Archivi di Stato </option>
									<option value="118">Scarto documenti degli uffici dello Stato - procedura ordinaria</option>
									<option value="119">Scarto documenti degli uffici dello Stato - procedura semplificata</option>
									<option value="177">VALUTAZIONE AMBIENTALE STRATEGICA (VAS) </option>
							      </select>
							</div>
							<br>
							<div>
								<label for="A_B">Destinatario</label>
								<select name="A_B" id="a_b" dojoType="dijit.form.FilteringSelect" style="width: 400px;">
									<option selected="" value="Pubblico">Pubblico</option>
									<option value="Privato">Privato</option>
						        </select>
							</div>
							<br>
						</div>
					    </span>
					    <span id="progressField" style="display:none;">
					      <div dojoType="dijit.ProgressBar" style="width:200px"
					        indeterminate="true"></div>
					    </span>
					  <br/>
					  <button value="upload" dojoType="dijit.form.Button"
					    onclick="inserisciFile()">Upload</button>
					</div>
					') ;
print ('</form>') ;
print ('</div>') ;
