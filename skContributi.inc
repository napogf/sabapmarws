<script language="JavaScript" type="text/javascript">
  	dojo.require("dijit.form.NumberTextBox");
  	dojo.require("dijit.form.CheckBox");
</script>

<div dojoType="dijit.form.Form" jsId="skContributi" id="skContributi" encType="multipart/form-data" action="" method="">
    <script type="dojo/method" event="onReset">
        return true;
    </script>
    <script type="dojo/method" event="onSubmit">
        if (this.validate()) {
        	data = this.attr('value');
			inserisciContributo(data,<?php print($this->_FormFields['PRATICA_ID']->GetValue()); ?>);
            return false;
        } else {
            alert('Mancano dati - correggi e aggiorna!');
            return false;
        }
        return false;
    </script>

    <table style="border: 1px solid #9f9f9f; margin: 5px;" cellspacing="10">
        <tr>
            <th>
                    Rif. Art.
            </th>
            <th>
                    Descrizione Voce
            </th>
            <th>
                    Detrazione
            </th>
            <th>
                    Incidenza
            </th>
        </tr>
        <tr>
            <td>
                <input type="text" id="RIF_ART" name="RIF_ART" required="true" dojoType="dijit.form.ValidationTextBox"
                 style="width: 4em; " />
            </td>
            <td>
                <input type="text" id="DESCRIPTION" name="DESCRIPTION" required="true" dojoType="dijit.form.ValidationTextBox"
                 style="width: 40em; " />
            </td>
            <td>
                <input id="DETRAZIONE" type="text" dojoType="dijit.form.NumberTextBox" name="DETRAZIONE" value="0" constraints="{pattern: '#,###.00'}" required="true">
            </td>
            <td>
                <input id="INCIDENZA" type="text" dojoType="dijit.form.NumberTextBox" name="INCIDENZA" value="0" constraints="{pattern: '#.##'}" required="true">
            </td>
            <td>
			    <button dojoType="dijit.form.Button" type="submit" name="submitButton"
			    value="Submit">
			        Aggiungi Voce
			    </button>

            </td>

        </tr>

    </table>
</div>
<?php
		print('<div dojoType="dijit.layout.ContentPane" id="dispContributi" href="djGetContributi.php?praticaId='.$this->_FormFields['PRATICA_ID']->GetValue().'" >');
		print ('</div>');
?>