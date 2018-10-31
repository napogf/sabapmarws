<?php
class EspiSoapClient extends SoapClient {


    function __doRequest($request, $location, $action, $version) {
        $request = str_replace("\n", '', $request);
        $request = str_replace("\r", '', $request);
        $res = parent::__doRequest($request, $location, $action, $version);
        return ($resx);
    }


}