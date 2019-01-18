dojo.addOnLoad(function() {
	
//	dojo.byId('filesForm').onSubmit(function(e){
//		e.preventDefault();
//	});
    dojo.query('input[type="TEXT"]').forEach(function(node, index, nodelist) {
        var aWidget = dijit.getEnclosingWidget(node);
        // console.log(aWidget.textbox);
        if(aWidget.textbox !== undefined){
            dojo.attr(aWidget.textbox,"autocomplete", "on");
        }
    });

	mainBox = dojo.position('mainBox');
	dojo.style('praticheTabs','height',mainBox.h - 120 + 'px');

	dijit.byId('praticheTabs').resize();

	dojo.query('.readonly').forEach(function(node, index, nodelist) {
		var aWidget = dijit.getEnclosingWidget(node);
		aWidget.attr('readOnly', 'readonly');
	});

	// dojo.query('[widgetid]',dojo.byClass('protocollazione'))

	var formProtocollazione = dojo.byId("formProtocollazionePec");

	new dijit.form.FilteringSelect({
			store: fascicoloStore,
			labelAttr: 'description',
			searchAttr: 'description',
			name: "fascicolo",
			autoComplete: true,
			style: "width: 400px;",
			id: "fascicolo",
			disabled: true,
			required: false,

		},
		"fascicolo");


	new dijit.form.FilteringSelect({
			store: classificaStore,
			labelAttr: 'DESCRIPTION',
			searchAttr: 'DESCRIPTION',
			name: "classifica",
			autoComplete: true,
			required: false,
			style: "width: 400px;",
			id: "classifica",
			onChange: function(MODELLO) {
				console.log(MODELLO);
				if(MODELLO){
					var classifica = classificaStore._arrayOfAllItems.filter(item => { return item.MODELLO[0] == MODELLO })
					dijit.byId('fascicolo').attr('disabled',false);
					dijit.byId('classifica2').attr('disabled',false);
					dijit.byId('classifica2').attr('value','');
					dijit.byId('classifica2').query.classificazione = classifica[0].classificazione[0];
				} else {

					dijit.byId('fascicolo').attr('disabled',true);
					dijit.byId('fascicolo').attr('value','');
					dijit.byId('classifica2').attr('disabled',true);
					dijit.byId('classifica2').attr('value','');

				}
			}

		},
		"classifica");


	new dijit.form.FilteringSelect({
			store: livelloStore,
			labelAttr: 'descrizione',
			searchAttr: 'descrizione',
			valueAttr: 'codice',
			disabled: true,
			required: false,
			name: "classifica2",
			autoComplete: true,
			style: "width: 400px;",
			id: "classifica2",
			query : { classificazione : "*"},
		},
		"classifica2");

	if(formProtocollazione != null){
	    dojo.connect(formProtocollazione, "onsubmit", function(event) {
			// Stop the submit event since we want to control form submission.
		    formWidget = dijit.byId("formProtocollazionePec");
			// The parameters to pass to xhrPost, the form, how to handle it, and the callbacks.
			// Note that there isn't a url passed.  xhrPost will extract the url to call from the form's
			//'action' attribute.  You could also leave off the action attribute and set the url of the xhrPost object
			// either should work.
			if(formWidget.validate()){
			    formData = formWidget.getValues();
			    if(!('ws_pec_id' in formData) || formData.ws_pec_id == undefined){

			    	return true;
			    }

	            dojo.stopEvent(event);			    
	            var xhrArgs = {
	                    url: 'djProtocollaInEntrata.php',
	                    form : formProtocollazione,
	                    handleAs : "json",
	                    load : function(data) {                 
	                        if(data.status == 'success'){
	                        	alert(data.message);
	                            location.href = '/pecWrkProtocollazione.php';

	                        } else {
	                            console.log(data);
	                            alert(data.message);
	                            dojo.byId('wsErrors').innerHTML = '<p>' + data.message +' </p>';                        
	                        }           
	                    },
	                    error : function(error) {
	                        // We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
	                        // docs server.
	                        alert(error);
	                    }
	                }

	                var deferred = dojo.xhrPost(xhrArgs);
			    
			}
		});

	    dojo.connect(dojo.byId('pecRicercaMittenteButton'),'onclick',function(){
	        
	        id_mittente = dijit.byId('pecRicercaMittente').attr('value');
	        mittente = dijit.byId('pecRicercaMittente').attr('value',id_mittente).item;
	        for(index in mittente['i']){       
	            if( index != 'ITEM' && index != 'DESCRIPTION'){
	                dijit.byId('pec_'+index).attr('value',mittente['i'][index]);
	            }
	            
	        }
	        
	        
	    });


	}

});