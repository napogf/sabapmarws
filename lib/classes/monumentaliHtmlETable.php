<?php
/**
 * Created by PhpStorm.
 * User: giacomo
 * Date: 12/11/18
 * Time: 11.10
 */

class monumentaliHtmlETable extends vincoliHtmlETable {

    function vincoliSelezionati(){
        if($vincoliResult=dbselect('select vincolo_id from arc_vincoli_pratiche where tipo = \'M\' and pratica_id ='.$_GET['PRATICA_ID'])){
            foreach ($vincoliResult['ROWS'] as $key => $value){
                $this->_vincoliChecked[]=$value['vincolo_id'];
            }
        }
    }
}