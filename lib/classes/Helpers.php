<?php
class Helpers {
    static function revertDate($data,$separator='/'){
        if(strpos($data,'-')>0){
            $data = implode($separator,array_reverse(explode('-', $data)));
        } else {
            $data = implode('-',array_reverse(explode($separator, $data)));
        }
        return $data;
    }
}