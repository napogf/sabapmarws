function sendPecMail(form, praticaId) {

    var formJson = dojo.formToJson('sendPec');
    var response;

    dojo.xhrPost({
        url : "djSendPec.php?praticaId=" + praticaId,
        form : 'sendPec',
        load : function(response, ioArgs) {
            if (response.status && response.status == 'success') {
                alert('La mail è stata spedita!');
                location.reload();
            } else {
                console.log(response);
                console.log(ioArgs);
                alert(response.message);
            }
            //location.href='editPratica.php?PRATICA_ID='+praticaId;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return true;
        },
        error : function(error) {
            alert('error:' + error);
            return error;
        },
        handleAs : "json"
    });

    return true;
}

function creaProgetto(praticaId) {
    dojo.xhrGet({
        url : "djCreaProgetto.php?praticaId=" + praticaId,
        load : function(response, ioArgs) {
            location.href = 'editPratica.php?PRATICA_ID=' + praticaId;
            //Dojo recommends that you always return(response); to propagate
            //the response to other callback handlers. Otherwise, the error
            //callbacks may be called in the success case.
            return response;
        },
        error : function(response, ioArgs) {
            alert('error:' + response);
            return response;
        },
        handleAs : "text"
    });
}
function deleteProject(projectId, praticaId) {
    if (confirm('Vuoi cancellare il progetto?')) {
        dojo.xhrGet({
            url : 'djDeleteProject.php?projectId=' + projectId,
            load : function(response, ioArgs) {
                location.href = 'editPratica.php?PRATICA_ID=' + praticaId;
                //Dojo recommends that you always return(response); to propagate
                //the response to other callback handlers. Otherwise, the error
                //callbacks may be called in the success case.
                return response;
            },
            error : function(response, ioArgs) {
                alert('error:' + response);
                return response;
            },
            handleAs : "text"
        });

    } else {
        return false;
    }
}
function setUscita(obj) {
    dataUscita = new Date(obj.item['DATAREGISTRAZIONE'][0]);
    dijit.byId('852').attr('value', dataUscita);
}

dojo.addOnLoad(function() {

    // Pubblicazione atti
    dojo.connect(dojo.byId('up_pubblica'), 'onchange', function(node) {
        if(this.checked){
            dojo.style(dojo.byId('campiPubblicazioneAtto'),'display','block');
        }
    });

    mainBox = dojo.position('mainBox');
    dojo.style('praticheTabs','height',mainBox.h - 120 + 'px');

    dijit.byId('praticheTabs').resize();

    dojo.query('.readonly').forEach(function(node, index, nodelist) {
        var aWidget = dijit.getEnclosingWidget(node);
        aWidget.attr('readOnly', 'readonly');
    });

    dojo.query('input[type="TEXT"]').forEach(function(node, index, nodelist) {
        var aWidget = dijit.getEnclosingWidget(node);
        // console.log(aWidget.textbox);
        if(aWidget.textbox !== undefined){
            dojo.attr(aWidget.textbox,"autocomplete", "on");
        }
    });

    var OggProvince = new dojo.data.ItemFileReadStore({
        url : "xml/jsonSql.php?sql=select SIGLA, PROVINCIA from v_province "
    });

    var OggComuni = new dojo.data.ItemFileReadStore({
        url : "xml/jsonSql.php?sql=select DISTINCT COMUNE, PROVINCIA from v_comuni"
    });




    var selDocuments = new dijit.form.FilteringSelect({
        store : sDocumenti,
        labelAttr : 'DESCRIPTION',
        searchAttr : 'DESCRIPTION',
        name : "DOCUMENTO",
        autoComplete : true,
        style : "width: 250px;",
        id : "selDocuments",
        onChange : function(selDocuments) {
            dojo.byId('creaButton').disabled = false;
        }

    }, "selDocuments");


    new dijit.form.FilteringSelect({
            store: fascicoloStore,
            labelAttr: 'description',
            searchAttr: 'description',
            required: false,
            name: "fascicolo",
            autoComplete: true,
            style: "width: 400px;",
            id: "fascicolo",

        },
        "fascicolo");


    // new dijit.form.FilteringSelect({
    //         store: classificaStore,
    //         labelAttr: 'DESCRIPTION',
    //         searchAttr: 'DESCRIPTION',
    //         required: false,
    //         name: "classifica",
    //         autoComplete: true,
    //         style: "width: 400px;",
    //         id: "classifica",
    //         value: parseInt(dojo.query('[name=MODELLO]')[0].value),
    //         onChange: function(MODELLO) {
    //             var classifica = classificaStore._arrayOfAllItems.filter(item => { return item.MODELLO[0] == MODELLO })
    //             console.log(classifica);
    //             dijit.byId('classifica2').attr('value','');
    //             dijit.byId('classifica2').query.classificazione = classifica[0].classificazione[0];
    //         }
    //
    //     },
    //     "classifica");


    new dijit.form.FilteringSelect({
            store: livelloStore,
            labelAttr: 'descrizione',
            searchAttr: 'descrizione',
            valueAttr: 'codice',
            name: "classifica2",
            autoComplete: true,
            style: "width: 400px;",
            id: "classifica2",
            // query : { classificazione : "*"},
        },
        "classifica2");


    dojo.query('textarea[name="OGGETTO"]').attr('readonly', 'readonly');
    dojo.connect(dojo.byId('RicercaMittenteButton'), 'onclick', function() {

        id_mittente = dijit.byId('RicercaMittente').attr('value');
        mittente = dijit.byId('RicercaMittente').attr('value', id_mittente).item['i'];
        for (index in mittente) {
            if (index != 'ITEM' && index != 'DESCRIPTION') {
                dijit.byId('AD_' + index.toUpperCase()).attr('value', (mittente[index] == null ? '' : mittente[index]));
            }

        }

    });

    dojo.query('#associaFascicolo').connect('onclick', function(evt) {

        praticaDaAssociare = dijit.byId('ricercaFascicolo').item.i.ITEM;

        praticaId = dijit.byId(dojo.attr(this, 'id')).attr('value');

        if (praticaId > '') {
            var xhrArgs = {
                url : 'djAssociaFascicolo.php?praticaId=' + praticaId + '&praticaDaAssociare='+praticaDaAssociare,
                handleAs : 'json',
                load : function(data) {
                    if (data.status == 'error') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                },
                error : function(error) {
                    // We'll 404 in the demo, but that's okay. We don't have a 'postIt'
                    // service on the
                    // docs server.
                    alert(error);
                }
            };

            var deferred = dojo.xhrPost(xhrArgs);

        } else {
            alert('Seleziona un Fascicolo!');
        }

    });

    dojo.query('#associaProgetto').connect('onclick', function(evt) {
        projectId = dijit.byId('ricercaProgetto').attr('value');
        praticaId = dijit.byId(dojo.attr(this, 'id')).attr('value');
        if (projectId > '') {
            var xhrArgs = {
                url : 'djCreaProgetto.php?project=' + projectId + '&praticaId=' + praticaId,
                handleAs : 'json',
                load : function(data) {
                    if (data.status == 'error') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                },
                error : function(error) {
                    // We'll 404 in the demo, but that's okay. We don't have a 'postIt'
                    // service on the
                    // docs server.
                    alert(error);
                }
            };

            var deferred = dojo.xhrPost(xhrArgs);

        } else {
            alert('Seleziona un Fascicolo!');
        }

    });

    dojo.query('#creaProgetto').connect('onclick', function(evt) {
        description = dijit.byId('nuovoProgetto').attr('value');
        praticaId = dijit.byId(dojo.attr(this, 'id')).attr('value');
        if (description > '') {
            var xhrArgs = {
                url : 'djCreaProgetto.php?description=' + description + '&praticaId=' + praticaId,
                handleAs : 'json',
                load : function(data) {
                    if (data.status == 'error') {
                        alert(data.message);
                        location.reload();
                    } else {
                        location.reload();
                    }
                },
                error : function(error) {
                    // We'll 404 in the demo, but that's okay. We don't have a 'postIt'
                    // service on the
                    // docs server.
                    alert(error);
                }
            };

            var deferred = dojo.xhrPost(xhrArgs);

        } else {
            alert('Inserisci una descrizione per il Fascicolo!');
        }

    });

    dojo.query('#eliminaProgetto').connect('onclick', function(evt) {
        praticaId = dijit.byId(dojo.attr(this, 'id')).attr('value');
        if (praticaId > '') {
            if(confirm('Vuoi eliminare il protocollo dal fascicolo?')){
                var xhrArgs = {
                    url : 'djDeleteProject.php?praticaId=' + praticaId,
                    handleAs : 'json',
                    load : function(data) {
                        if (data.status == 'error') {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    },
                    error : function(error) {
                        // We'll 404 in the demo, but that's okay. We don't have a 'postIt'
                        // service on the
                        // docs server.
                        alert(error);
                    }
                };

                var deferred = dojo.xhrPost(xhrArgs);
            }


        } else {
            alert('Fascicolo Non associato alla Pratica!');
        }

    });

    dojo.query(".fa").style("cursor", "pointer");
    //dojo.query('textarea[name="OGGETTO"]').attr('readonly','readonly');

    dojo.query('.pratica-fascicolo').connect('onclick', function(event) {
        location.href = 'editPratica.php?PRATICA_ID=' + dojo.attr(this, 'data-pratica-id');
    });

    formPratica = dojo.byId('form-pratica');
    if (dojo.hasClass(formPratica, 'form-readonly')) {
        dojo.query("#form-toolBar").style("display", "none");
        dojo.query("#praticaButtonBar").style("display", "none");
        dojo.query('.dijitContentPane', formPratica).forEach(function(node, index, nodelist) {
            if (dojo.attr(node, 'id') != 'rispostaPec') {
                dojo.query("input, textarea, select", node).attr("readonly", 'readonly').attr('disabled', 'disabled').addClass('readonly');
                dojo.query(".buttons", node).style("display", 'none');
                dojo.query(".delete", node).style("display", "none");
            }
        });


        oggProv = dijit.byId('OGG_PROV');
        if(oggProv){
            oggProv.set('readonly', true)
        }
        oggComune = dijit.byId('OGG_COMUNE');
        if(oggComune){
            oggComune.set('readonly', true);
        }

    }

    dojo.query(".destinatarioPec").forEach(function(node, index, nodelist) {
        var aWidget = dijit.getEnclosingWidget(node);
        aWidget.validator = function(value) {
            return dojox.validate.isEmailAddress(value);
        };
    });

    dojo.query('.readonly').forEach(function(node, index, nodelist) {
        var aWidget = dijit.getEnclosingWidget(node);
        aWidget.attr('readOnly', 'readonly');
    });



    var formProtocollazione = dojo.byId("formProtocollazione");

    if (formProtocollazione != null) {
        dojo.connect(formProtocollazione, "onsubmit", function(event) {
            // Stop the submit event since we want to control form submission.
            dojo.stopEvent(event);
            // The parameters to pass to xhrPost, the form, how to handle it, and the callbacks.
            // Note that there isn't a url passed.  xhrPost will extract the url to call from the form's
            //'action' attribute.  You could also leave off the action attribute and set the url of the xhrPost object
            // either should work.
            // Controllo che ci sia almeno un indirizzo di destinazione non escluso
            var indirizzi = false;
            dojo.query('.escludiDestinatario').forEach(function(node, index, nodelist) {
                if (dijit.getEnclosingWidget(node).getValue() == false) {
                    indirizzi = true;
                }
            });
            if (indirizzi) {
                formData = dojo.byId("formProtocollazione");
                tipologia = dojo.byId('ws_tipologia').value;
                protoUrl = (tipologia == 'U' ? 'djProtocollaInUscita.php' : 'djProtocollaInEntrata.php');
                var xhrArgs = {
                    url : protoUrl,
                    form : formData,
                    handleAs : "json",
                    load : function(data) {
                        if (data.status == 'error') {
                            alert(data.message);
                            dojo.byId('wsErrors').innerHTML = '<p>' + data.message + ' </p>';
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    },
                    error : function(error) {
                        // We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
                        // docs server.
                        alert(error);
                    }
                }

                var deferred = dojo.xhrPost(xhrArgs);
            } else {
                alert('Includi almeno un indirizzo di destinazione per la protocollazione in uscita!');
            }
        });

    }

});
