function filterClassifica(ev){
	console.log(dojo(ev));
}


dojo.addOnLoad(function() {
	
//	dojo.byId('filesForm').onSubmit(function(e){
//		e.preventDefault();
//	});

	new dijit.form.FilteringSelect({
			store: fascicoloStore,
			labelAttr: 'description',
			searchAttr: 'description',
			required: true,
			name: "fascicolo",
			autoComplete: true,
			style: "width: 400px;",
			id: "fascicolo",

		},
		"fascicolo");


	new dijit.form.FilteringSelect({
			store: classificaStore,
			labelAttr: 'DESCRIPTION',
			searchAttr: 'DESCRIPTION',
			required: true,
			name: "classifica",
			autoComplete: true,
			style: "width: 400px;",
			id: "classifica",
			onChange: function(MODELLO) {
				var classifica = classificaStore._arrayOfAllItems.filter(item => { return item.MODELLO[0] == MODELLO })
				console.log(classifica);
				dijit.byId('classifica2').attr('value','');
				dijit.byId('classifica2').query.classificazione = classifica[0].classificazione[0];
			}

		},
		"classifica");


	new dijit.form.FilteringSelect({
			store: livelloStore,
			labelAttr: 'descrizione',
			searchAttr: 'descrizione',
			valueAttr: 'codice',
			disabled: true,
			name: "classifica2",
			autoComplete: true,
			style: "width: 400px;",
			id: "classifica2",
			query : { classificazione : "*"},
		},
		"classifica2");




	dojo.query('.readonly').forEach(function(node, index, nodelist) {
		var aWidget = dijit.getEnclosingWidget(node);
		aWidget.attr('readOnly', 'readonly');
	});

	var formProtocollazione = dojo.byId("formProtocollazionePec");
	
	
	if(formProtocollazione != null){
	    dojo.connect(formProtocollazione, "onsubmit", function(event) {
			// Stop the submit event since we want to control form submission.

		    formWidget = dijit.byId("formProtocollazionePec");
			// The parameters to pass to xhrPost, the form, how to handle it, and the callbacks.
			// Note that there isn't a url passed.  xhrPost will extract the url to call from the form's
			//'action' attribute.  You could also leave off the action attribute and set the url of the xhrPost object
			// either should work.
            // console.log(dojo.query('input.uoidchk:checked').length);
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
	                            location.reload();

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

	    dojo.query('input[name="tipologia"]',formProtocollazione).forEach(function(node){
	        if(node.value == 'Uscita' && node.checked){
	            dijit.byId('classifica').set('disabled',false);
	            dijit.byId('classifica2').set('disabled',false);
	            dijit.byId('fascicolo').set('disabled', false);
	            dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',true);
	            dijit.byId('clsTestataDocumento_Data').set('disabled', true);
	            dijit.byId('clsTestataDocumento_Arrivo').set('disabled', true);

                dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
                    var aWidget = dijit.getEnclosingWidget(node);
                    aWidget.set('disabled', false);
                });	          
                // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
                //     node.disabled = true;
                //     node.checked = false;
                // });
				dojo.style('mittente','display','block');
				// dojo.style('assegnazioneUo','display','block');

	        } else if(node.value == 'Interno' && node.checked ){
	            dijit.byId('classifica').set('disabled',false);
	            dijit.byId('classifica2').set('disabled',false);
	            dijit.byId('fascicolo').set('disabled', false);
	            dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',true);
	            dijit.byId('clsTestataDocumento_Data').set('disabled', true);
	            dijit.byId('clsTestataDocumento_Arrivo').set('disabled', true);

	            dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
	                var aWidget = dijit.getEnclosingWidget(node);
	                aWidget.set('disabled', false);
	            });
                // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
                //     node.disabled = false;
                // });

	        } else if(node.value == 'Entrata' && node.checked ){
	            dijit.byId('classifica').set('disabled',false);
	            dijit.byId('classifica2').set('disabled',false);
	            dijit.byId('fascicolo').set('disabled', false);
	            dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',false);
	            dijit.byId('clsTestataDocumento_Data').set('disabled', false);
	            dijit.byId('clsTestataDocumento_Arrivo').set('disabled', false);

                dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
                    var aWidget = dijit.getEnclosingWidget(node);
                    aWidget.set('disabled', false);
                });	            
                // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
                //     node.disabled = false;
                // });
				dojo.style('mittente','display','block');
				// dojo.style('assegnazioneUo','display','block');
	        }

	        dojo.connect(node,'onchange',function(){

	            if(node.value == 'Uscita'){
	                dijit.byId('classifica').set('disabled',false);
	                dijit.byId('classifica2').set('disabled',false);
	                dijit.byId('fascicolo').set('disabled', false);
	                
	                dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',true);
	                dijit.byId('clsTestataDocumento_Data').set('disabled', true);
	                dijit.byId('clsTestataDocumento_Arrivo').set('disabled', true);
					dijit.byId('protAssegnazione').set('disabled',false);

                    dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
                        var aWidget = dijit.getEnclosingWidget(node);
                        aWidget.set('disabled',false);
                    });	                
                    // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
                    //     node.disabled = true;
                    //     node.checked = false;
                    // });
					dojo.style('mittente','display','block');
					// dojo.style('assegnazioneUo','display','block');
	            } else if(node.value == 'Interno'){
	                dijit.byId('classifica').set('disabled',false);
	                dijit.byId('classifica2').set('disabled',false);
	                dijit.byId('fascicolo').set('disabled', false);
	                dijit.byId('protAssegnazione').set('disabled',true);
	                dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',true);
	                dijit.byId('clsTestataDocumento_Data').set('disabled', true);
	                dijit.byId('clsTestataDocumento_Arrivo').set('disabled', true);      
	                dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
	                    var aWidget = dijit.getEnclosingWidget(node);
	                    aWidget.set('disabled', true);
	                });
	                // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
	                //     node.disabled = false;
	                // });
					dojo.style('mittente','display','none');
					// dojo.style('assegnazioneUo','display','none');
	            } else {
	                dijit.byId('classifica').set('disabled',false);
	                dijit.byId('classifica2').set('disabled',false);
	                dijit.byId('fascicolo').set('disabled', false);
					dijit.byId('protAssegnazione').set('disabled',false);
	                dijit.byId('clsTestataDocumento_numeroregistrazione').set('disabled',false);
	                dijit.byId('clsTestataDocumento_Data').set('disabled', false);
	                dijit.byId('clsTestataDocumento_Arrivo').set('disabled', false);         
                    dojo.query("[widgetid]", dojo.byId('mittente')).forEach(function(node, index, nodelist) {
                        var aWidget = dijit.getEnclosingWidget(node);
                        aWidget.set('disabled', false);
                    });	                
                    // dojo.query("input[type='checkbox']", dojo.byId('assegnazioneUo')).forEach(function(node, index, nodelist) {
                    //     node.disabled = false;
                    // });
					dojo.style('mittente','display','block');
					// dojo.style('assegnazioneUo','display','block');
	            }
	        });
	        
	    });
	    
	    
	    
		
	}

});