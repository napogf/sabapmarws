// create a variable that closures can relate to in addOnLoad and handleSubmits
// that way we dont have to call dojo.widget.byId multiple times
dojo.hostenv.writeIncludes();
var cPane = null;

function formTreeEvent(){
		dojo.event.topic.subscribe("nodeSelected",
			 function(message) {
				cMessage = dojo.widget.byId('messageBar');
			 	cPane = dojo.widget.byId("formPane");
			 	var topBar = dojo.widget.byId("topBar");
			 	var filter = new RegExp("form_");
			 	if (filter.test(message.node.widgetId)) {
			 			cMessage.setContent('Form : '+message.node.title);
						cPane.href = "xml/getFields.php?formId="+RegExp.rightContext;
						topBar.href = "xml/getFormBar.php?formId="+RegExp.rightContext;
						cPane.refresh();
						topBar.refresh();
			 	} else {
			 		var filter = new RegExp("field_");
			 		if (filter.test(message.node.widgetId)) {
			 			cMessage.setContent('Field : '+message.node.title);
						cPane.href = "xml/getFields.php?fieldId="+RegExp.rightContext;
						topBar.href = "xml/getFormBar.php?fieldId="+RegExp.rightContext;
						topBar.refresh();
						cPane.refresh();
			 		} else {
			 			cMessage.setContent('Form Management Root');
						cPane.href = "xml/getFields.php";
						cPane.refresh();
			 		}
			 	}
			  }
		);
}

function formLanguages(formId,languageId){
	cPane = dojo.widget.byId("formPane");
	cPane.href = 'djForm.php?mode=modify&key_FORM_ID='+formId+'&key_LANGUAGE_ID='+languageId+'&formName=SYS_FORMS_TITLES';
	cPane.refresh();
}

function fieldLanguages(fieldId,languageId){
	cPane = dojo.widget.byId("formPane");
	cPane.href = 'djForm.php?mode=modify&key_FIELD_ID='+fieldId+'&key_LANGUAGE_ID='+languageId+'&formName=SYS_FORMS_FIELDS_LABELS';
	cPane.refresh();
}

function fieldManagement(fieldId){
	cPane = dojo.widget.byId("formPane");
	cPane.href = 'djForm.php?mode=modify&key='+fieldId+'&formName=SYS_FORMS_FIELDS';
	cPane.refresh();
}
function formManagement(formId){
	cPane = dojo.widget.byId("formPane");
	cPane.href = 'djForm.php?mode=modify&key='+formId+'&formName=SYS_FORMS';
	cPane.refresh();
}

function duplicateForm(formId){
	dojo.io.bind({
		url: 'sys_manage_forms.php?duplicate=Y&FORM_ID='+formId,
		handler:function( type, data, evt ){
			cMessage = dojo.widget.byId('messageBar');
			cMessage.setContent(data);
			return true;
		}
	});
}


function deleteForm(formId){
	 if (confirm('Do you want really delete the form?')){
		dojo.io.bind({
			url: 'sys_manage_forms.php?mode=delete&FORM_ID='+formId,
			handler:function( type, data, evt ){
				cMessage = dojo.widget.byId('messageBar');
				cMessage.setContent(data);
				return true;
			}
		});
	}
}


        // set up a listener for form submits inside cPane
        dojo.addOnLoad(function(){
            // set the reference for our ContentPane widget to the variable we created before
            cPane = dojo.widget.byId('formPane');
            // connect a event listener to contentpane domNode
            // If IE had decent DOM we could just settle with connecting a onsubmit event
            // and listen to that when it bubbled up to cPane.domNode,
            // but onsubmit events doesnt bubble in IE
            // so we are forced to iterate through document.forms
            // and create a new FormBind listener each time we load a new content
            dojo.event.connect(cPane, 'onLoad', 'setUpForm');
            // on page initial load cPane onLoad isnt called
            setUpForm();

        });


        // handle form submits
        function setUpForm(){
            // find out if any of document.forms is a ancestor of cPane.domNode
            var node = null;
            for(var i = 0; i < document.forms.length; i++){
                if(dojo.dom.isDescendantOf(document.forms[i],cPane.domNode)){
                    node = document.forms[i];
                    break;
                }
            }

            if(node){
                // create a new FormBind object
                new dojo.io.FormBind({
	                    // evt.target is our formNode
	                    formNode: node,
	                    load: function(load, data, e) {
	                    // relay the server response to cPane.setContent
	                    cPane.setContent(data);
                    }
                });
            }
        };
