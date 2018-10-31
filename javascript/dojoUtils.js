var cPane;

function setImgPane(linkId) {
    cPane = dojo.widget.byId('objectCard');
    cPane.href = 'dojoCard.php?wk_link_id=' + linkId;
    cPane.refresh();
}

function printPec() {

    var content = dojo.byId("pecToPrint");
    var pri = document.getElementById("printElement").contentWindow;

    pri.document.open();
    pri.document.write(content.innerHTML);
    pri.document.close();
    pri.focus();
    pri.print();

}

function popscheda(linkId) {
    //comands for the menu
    var paneId = "fp_" + linkId;
    var menu_comands = {
        cmd_new: function () {
            alert('New file ...');
        },

        cmd_print: function () {
            alert('printing...');
        },

        cmd_close: function () {
            var pane = dojo.widget.byId(paneId);
            if (pane) {
                pane.closeWindow();
            }
        }
    };

    // layout within the floating pane
    var fpLayout = dojo.widget.createWidget("LayoutContainer");
    fpLayout.domNode.style.width = "95%";
    fpLayout.domNode.style.height = "98%";

    // floating pane body
    var fpBody = dojo.widget.createWidget("ContentPane", {
        layoutAlign: "client"
    });

    dojo.io.bind({
        url: "dojoCard.php?wk_link_id=" + linkId,
        mimetype: "text/html",
        load: function (type, data, event) {
            fpBody.setContent(data);
        },
        error: function (type, error) {
            fpBody.setContent(error);
        }
    });

    fpLayout.addChild(fpBody);

    fpBody.domNode.style.width = "95%";
    fpBody.domNode.style.height = "98%";


    // creating the floating pane
    var floatingPane = dojo.widget.createWidget("FloatingPane", {
        id: paneId,
        title: "Scheda Opera",
        resizable: true,
        displayCloseAction: true
    });


    // Add the layout content to the pane
    floatingPane.addChild(fpLayout);

    // Position the window
    var node = floatingPane.domNode;
    node.style.position = "absolute";
    node.style.top = "120px";
    node.style.left = "60px";
    // Attach to the document body
    var body = document.getElementsByTagName('body')[0];
    body.appendChild(floatingPane.domNode);

    floatingPane.resizeTo(600, 600);

    // Disable the launching button
    // var button = dojo.byId('createButton');
    // button.disabled = true;

    // Handle the 'X' button window closing
    dojo.event.connect(floatingPane, "closeWindow",
        menu_comands, "cmd_close");
}

function popimage(url) {
    newwindow = window.open(url, 'immagine', 'height=700,width=500,resizable,scrollbars,dependent');
    if (window.focus) {
        newwindow.focus()
    }
}

function pophtml(url, type) {
    //comands for the menu
    var paneId = "fp_" + type;
    var menu_comands = {
        cmd_new: function () {
            alert('New file ...');
        },

        cmd_print: function () {
            alert('printing...');
        },

        cmd_close: function () {
            var pane = dojo.widget.byId(paneId);
            if (pane) {
                pane.closeWindow();
            }
        }
    };

    // layout within the floating pane
    var fpLayout = dojo.widget.createWidget("LayoutContainer");
    fpLayout.domNode.style.width = "95%";
    fpLayout.domNode.style.height = "98%";

    // floating pane body
    var fpBody = dojo.widget.createWidget("ContentPane", {
        layoutAlign: "client"
    });

    dojo.io.bind({
        url: "parsePopup.php?template=" + url,
        load: function (type, data, event) {
            fpBody.setContent(data);
        },
        error: function (type, error) {
            fpBody.setContent(error);
        }
    });

    fpLayout.addChild(fpBody);

    // creating the floating pane
    var floatingPane = dojo.widget.createWidget("FloatingPane", {
        id: paneId,
        title: "Scheda " + type,
        resizable: true,
        displayCloseAction: true
    });

    // Add the layout content to the pane
    floatingPane.addChild(fpLayout);

    // Position the window
    var node = floatingPane.domNode;
    node.style.top = "120px";
    node.style.left = "60px";
    // Attach to the document body
    var body = document.getElementsByTagName('body')[0];
    body.appendChild(floatingPane.domNode);

    floatingPane.resizeTo(600, 600);

    // Disable the launching button
    // var button = dojo.byId('createButton');
    // button.disabled = true;

    // Handle the 'X' button window closing
    dojo.event.connect(floatingPane, "closeWindow",
        menu_comands, "cmd_close");

}

function editPratica(praticaId) {
    location.href = 'editPratica.php?PRATICA_ID=' + praticaId;
}

function viewPratica(praticaId) {
    location.href = 'dbShow.php?dbTable=PRATICHE&FIELD_NAME=PRATICA_ID&FIELD_VALUE=' + praticaId;
}

function viewVincoli(praticaId) {
    location.href = 'praticaVincoli.php?PRATICA_ID=' + praticaId;
}


// Loading dei contenuti per i toolTips in praticheStatus.php

function getToolTipPratica(pratica_id) {
    dojo.xhrGet({
        url: "djPraticaToolTip.php?pratica_id=" + pratica_id,
        load: function (response, ioArgs) {
            dojo.byId("tool" + pratica_id).innerHTML = response;

            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            dojo.byId("tool" + pratica_id).innerHTML =
                "An error occurred, with response: " + response;
            return response;
        },
        handleAs: "text"
    });

}

function toolTipVincoli(pratica_id) {
    dojo.xhrGet({
        url: "djVincoliToolTip.php?pratica_id=" + pratica_id,
        load: function (response, ioArgs) {
            dojo.byId("ttVinc" + pratica_id).innerHTML = response;

            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            dojo.byId("tool" + pratica_id).innerHTML =
                "An error occurred, with response: " + response;
            return response;
        },
        handleAs: "text"
    });

}

function creaDaModello(nReg) {
    location.href = 'MergeOODocs.php?DOC_ID=' + dijit.byId('selDocuments').value + '&nReg=' + nReg;
}

function setDocuments(modello) {
    var sDocumenti = new dojo.data.ItemFileReadStore({url: 'xml/jsonSql.php?sql=select DOC_ID, DESCRIPTION, MODELLO from arc_documenti where modello is null or modello = ' + modello});
    var selectDoc = dijit.byId('selDocuments');
    selectDoc.store = sDocumenti;
    selectDoc.setDisplayedValue('');


}

function riapriPratica(praticaId) {
    if (confirm('Vuoi riaprire la pratica?')) {
        location.href = 'riapriPratica.php?praticaId=' + praticaId;
    }
}


function openDialog(praticaId) {
    formDlg = dijit.byId('dialogOne');
    formDlg.href = 'getDialog.php?praticaId=' + praticaId;
    formDlg.refresh();
    formDlg.show();
}


function chiudiPratica() {
    formDlg = dijit.byId('dialogOne');
    if (!formDlg.isValid()) {
        alert('Compila tutti i campi!');
        return false;
    }
    data = formDlg.attr('value');
    dojo.xhrGet({
        url: "chiudiPratica.php?praticaId=" + data.prId + "&tipoPratica=" + data.MODELLO + "&esitoId=" + data.ESITO_ID + "&dataUscita=" + dojo.byId('DATAUSCITA').value,
        load: function (response, ioArgs) {
            row = dojo.byId(data.prId);
            row.style.backgroundColor = "gray";
            row.style.color = "yellow";
            formDlg.reset();
            formDlg.hide();
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });
}


function titola(praticaId, titoloId) {
    // titoloId è il modello da cui mi ricavo la classifica

    comuneId = dijit.byId('TITOCOMUNE').item.ID[0];

    test = dijit.byId('FASCICOLO');


    if (test.item == null) {
        fascicoloId = '';
    } else {
        fascicoloId = test.item.ID[0];
    }

    newFascicolo = dojo.byId('FASCICOLO_NEW').value;

    titoloHref = 'praticaTitolazione.php?praticaId=' + praticaId + '&titoloId=' +
        titoloId + '&comuneId=' + comuneId + '&fascicoloId=' + fascicoloId +
        '&newFascicolo=' + newFascicolo;

    dojo.xhrGet({
        url: titoloHref,
        load: function (response, ioArgs) {
            dojo.byId('dispFascicolo').innerHTML = response;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });

}

function inserisciDestinazione(data, praticaId) {

    if (data.DEST_ID > '') {
        insDestHref = 'djInsDestinazioni.php?PRATICA_ID=' + praticaId + '&DEST_ID=' + data.DEST_ID;
    } else {
        insDestHref = 'djInsDestinazioni.php?PRATICA_ID=' + praticaId
            + '&NOME=' + data.AD_NOME
            + '&COGNOME=' + data.AD_COGNOME
            + '&TITOLO=' + data.AD_TITOLO
            + '&TOPONIMO=' + data.AD_TOPONIMO
            + '&CAP=' + data.AD_CAP
            + '&COMUNE=' + data.AD_COMUNE
            + '&PROVINCIA=' + data.AD_PROVINCIA
            + '&LOCALITA=' + data.AD_LOCALITA
            + '&TELEFONO=' + data.AD_TELEFONO
            + '&FAX=' + data.AD_FAX
            + '&CODICEFISCALE=' + data.AD_CODICEFISCALE
            + '&EMAIL=' + data.AD_EMAIL
            + '&PEC=' + data.AD_PEC;
    }
    dojo.xhrGet({
        url: insDestHref,
        load: function (response, ioArgs) {
            if (response.status == 'success') {
                location.reload();
            } else {
                alert(response.message);
            }
            //dojo.byId('dispDestinazioni').innerHTML = response;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.


        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "json"
    });
}

function cancellaDestinazione(destId) {
    if (confirm('Vuoi cancellare la destinazione?')) {
        dojo.xhrGet({
            url: 'djCancDestinazioni.php?destId=' + destId,
            load: function (response, ioArgs) {
                alert(response);
                location.reload();
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error: function (response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs: "text"
        });

    } else {
        return false;
    }
}

function inserisciContributo(data, praticaId) {

    insHref = 'djInsContributi.php?praticaId=' + praticaId + '&RIF_ART=' + data.RIF_ART + '&DESCRIPTION=' + data.DESCRIPTION + '&DETRAZIONE=' + data.DETRAZIONE +
        '&AMMISSIBILE=' + data.AMMISSIBILE + '&INCIDENZA=' + data.INCIDENZA;
    dojo.xhrGet({

        url: insHref,
        load: function (response, ioArgs) {
            dijit.byId('dispContributi').refresh();
            dijit.byId('skContributi').reset();

            //dojo.byId('dispDestinazioni').innerHTML = response;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.

            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });

}

function cancellaContributo(Id) {
    if (confirm('Vuoi cancellare la riga di Contributo?')) {
        dojo.xhrGet({
            url: 'djCancContributi.php?Id=' + Id,
            load: function (response, ioArgs) {
                alert(response);
                dijit.byId('dispContributi').refresh();
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error: function (response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs: "text"
        });

    } else {
        return false;
    }
}


function rimuoviVincolo(praticaId) {
    if (confirm('Vuoi rimuovere il vicolo per questa Pratica?')) {
        dojo.xhrGet({
            url: 'djCancVincoli.php?praticaId=' + praticaId,
            load: function (response, ioArgs) {
                location.href = 'editPratica.php?PRATICA_ID=' + praticaId;
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error: function (response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs: "text"
        });
    } else {
        return false;
    }
}

function impostaTipo(praticaId) {
    formDlg = dijit.byId('dlgTipoPratica');
    formDlg.href = 'getDialog.php?praticaId=' + praticaId + '&dialog=tipoPratica';
    formDlg.refresh();
    formDlg.show();
}


function aggiornaTipo() {
    formDlg = dijit.byId('dlgTipoPratica');
    if (!formDlg.isValid()) {
        alert('Compila i campi obbligatori!');
        return false;
    }
    data = formDlg.attr('value');

    dojo.xhrGet({
        url: "djAggiornaPratica.php?PRATICA_ID=" + data.prId +
        "&MODELLO=" + data.MODELLO +
        '&USCITA=' + dojo.byId('DATAUSCITA').value +
        '&ESITO_ID=' + data.ESITO_ID +
        '&NOTE=' + data.NOTE +
        '&ZONA=' + data.ZONA +
        '&UFFICIO=' + data.UFFICIO

        ,
        load: function (response, ioArgs) {
            console.log(response);
            if (response.status === 'success') {
                column = dojo.byId('tp' + data.prId);
                column.style.textAlign = 'left';
                column.innerHTML = response.message;
                row = dojo.byId(data.prId);
                row.className = "praActive";
            } else {
                alert('Error: ' + response.message);
            }

            formDlg.reset();
            formDlg.hide();
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "json"
    });
}


function impostaFaldone(praticaId) {
    formDlg = dijit.byId('dlgFaldone');
    formDlg.href = 'getDialog.php?praticaId=' + praticaId + '&dialog=faldone';
    formDlg.refresh();
    formDlg.show();
}

function aggiornaFaldone() {
    formDlg = dijit.byId('dlgFaldone');
    if (!formDlg.isValid()) {
        alert('Compila i campi obbligatori!');
        return false;
    }
    data = formDlg.attr('value');
    dojo.xhrGet({
        url: "djAggiornaFaldone.php?praticaId=" + data.prId + "&FALDONE=" + data.FALDONE,
        load: function (response, ioArgs) {
            formDlg.reset();
            formDlg.hide();
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });
}


function toggleFilter() {
    filterDisp = dojo.byId('advSearch').style.display;
    if (filterDisp == 'none') {
        dojo.byId('advSearch').style.display = 'block';
    } else {
        dojo.byId('advSearch').style.display = 'none';
    }
}


function apriDlg(dlg) {
    formDlg = dijit.byId(dlg);
    formDlg.href = 'getDlg.php?dlg=' + dlg;
    formDlg.refresh();
    formDlg.show();
}

function apriPratica(dlg) {
    formDlg = dijit.byId(dlg);
    if (!formDlg.isValid()) {
        alert('Compila tutti i campi!');
        return false;
    }
    data = formDlg.attr('value');
    console.log(data);
    dojo.xhrGet({
        url: "apriPratica.php?tipologia=" + data.DLG_TIPOLOGIA + "&uoid=" + data.DLG_UOID,
        load: function (response, ioArgs) {
            formDlg.reset();
            formDlg.hide();
            if (response.status == 'success') {
                location.href = 'editPratica.php?PRATICA_ID=' + response.pratica_id;
            } else {
                alert(response.message);
            }

            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "json"
    });
}

function sendMailVincoli(data) {

    dojo.xhrGet({
        url: 'djmailVincoli.php?body=' + data.mail,
        load: function (response, ioArgs) {
            dojo.byId('mailMessage').innerHTML = 'mail spedita a ...';
            return true;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });

}


function inserisciFile() {
    //Hide the file input field
    dojo.style('up_FILENAME',"display","none");
    dojo.style('up_DESCRIPTION',"display","none");
    dojo.style('up_PUBBLICA',"display","none");
    //Hide the file input field
    //Show the progress bar
    dojo.style('progressField',"display","inline");
    dojo.byId('preamble').innerHTML = "Uploading ...";
    //Show the progress bar
    // dojo.style('progressField',"display","inline");
    // dojo.byId('preamble').innerHTML = "Uploading ...";


    dojo.io.iframe.send({
      url: "uploadsIframeUploads.php",
      method: "post",
      handleAs: "text",
      form: dojo.byId('filesForm'),
      handle: function(data,ioArgs){
          // location.reload();
        var foo = dojo.fromJson(data);
        if (foo.status == "success"){
          //Show the file input field
          dojo.byId('up_FILENAME').value = '';
          dojo.byId('up_DESCRIPTION').value = '';

          dojo.style(dojo.byId('up_FILENAME'),"display","inline");
          dojo.style(dojo.byId('up_DESCRIPTION'),"display","inline");
          dojo.style(dojo.byId('up_PUBBLICA'),"display","inline");
          //Hide the progress bar
          dojo.style(dojo.byId('progressField'),"display","none");
          dojo.byId('preamble').innerHTML += "Caricato il File: " + foo.details.name
            + " dimensione: " + foo.details.size +"<br>";
          dojo.byId('filesForm').reset();
          dojo.style(dojo.byId('campiPubblicazioneAtto'),'display','none');
          //refresh image table
          dijit.byId('dispUploads').refresh();
          dijit.byId('dispPecs').refresh();
        } else {
              dojo.style(dojo.byId('up_FILENAME'),"display","inline");
              dojo.style(dojo.byId('up_DESCRIPTION'),"display","inline");
          dojo.style(dojo.byId('progressField'),"display","none");
          dojo.byId('preamble').innerHTML = data;
        }
      },
      error: function(response, ioArgs){
    	  console.log(response);
    	  console.log(ioArgs);
          alert(response);
        return response;
      }
    });
}


function delUploads(uploadId) {
    if (confirm('Vuoi rimuovere il file?')) {
        dojo.xhrGet({
            url: 'djDelUpload.php?uploadId=' + uploadId,
            load: function (response, ioArgs) {
                if (response.status == 'success') {
                    dijit.byId('dispUploads').refresh();
                    // location.reload();
                } else {
                    alert(response.message);
                    dijit.byId('dispUploads').refresh();
                }

                //dijit.byId('dispPecs').refresh();
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error: function (response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs: "json"
        });
    } else {
        return false;
    }
}

function toggleDisplay(id) {
    objDisp = dojo.byId(id).style.display;
    if (objDisp == 'none') {
        dojo.byId(id).style.display = 'block';
    } else {
        dojo.byId(id).style.display = 'none';
    }
}

function openUploadsDialog() {
    formDlg = dijit.byId('dlgFilesUploads');
    formDlg.show();
}

function loadPec(uploadID) {
    pecPane = dijit.byId('dispPec');
    pecPane.setHref('djDisplayPec.php?PEC_ID=' + uploadID);
    pecPane.refresh();
}

function getAttachment(uploadId, attachIndex) {
    location.href = 'getAttachment.php?UPLOAD_ID=' + uploadId + '&INDEX=' + attachIndex;
}


function inserisciUnitaOrganizzativa(data, praticaId) {
    insUOHref = 'djInsUnitaOrganizzativa.php?praticaId=' + praticaId + '&uoid=' + data.UOID;
    dojo.xhrGet({
        url: insUOHref,
        load: function (response, ioArgs) {
            dijit.byId('dispUnitaOrganizzative').refresh();

            //dojo.byId('dispDestinazioni').innerHTML = response;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error: function (response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs: "text"
    });
}

function cancellaUnitaOrganizzativa(prauoid) {
    if (confirm('Vuoi cancellare l\'unità organizzativa?')) {
        dojo.xhrGet({
            url: 'djCancUnitaOrganizzativa.php?prauoid=' + prauoid,
            load: function (response, ioArgs) {
                alert(response);
                dijit.byId('dispUnitaOrganizzative').refresh();
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error: function (response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs: "text"
        });

    } else {
        return false;
    }
}

function uploadOnChange(e) {
    var filename = e.value;
    var lastIndex = filename.lastIndexOf("\\");
    if (lastIndex >= 0) {
        filename = filename.substring(lastIndex + 1);
    }
    console.log(filename);
    document.getElementById('up_DESCRIPTION').value = filename;
}

function dialogNote(id) {
    dlg = 'dlgAddNote_' + id;
    formDlg = dijit.byId(dlg);
    formDlg.href = 'Sys_djDlgNote.php?id=' + id;
    formDlg.refresh();
    formDlg.show();
}

function loadCpaneNote(id) {
    // console.log(dijit.byId('SEL_NOTE_'+id).item.ID[0]);
    dijit.byId('cPaneNote_' + id).href = 'Sys_djGetNote.php?TIPO='
        + dijit.byId('SEL_NOTE_' + id).item.ID[0] + '&id=' + id;
    dijit.byId('cPaneNote_' + id).refresh();
}

function addNote(value, id) {
    data = dojo.byId('INT_' + value);
    skNote = dojo.byId(id);
    // console.log(skNote);
    // console.log(skNote.value);
    skNote.value = skNote.value + data.childNodes[0].data;
    dojo.attr('INT_' + value, "style", {
        background: "#eee"
    });
}

function pecDownload(element, pecId, attachIndex) {

    dojo.style(element, 'color', 'blue');
    location.href = 'getPecAttachment.php?PEC_ID=' + pecId + '&INDEX=' + attachIndex;
}

function setUscitaQuick() {
    obj = dijit.byId('dlgProtUscita').item.i.DATAREGISTRAZIONE;
    dataUscita = new Date(obj);
    dijit.byId('DATAUSCITA').attr('value', dataUscita);
}
