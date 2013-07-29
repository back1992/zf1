<?php

class ServerController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        var_dump($_SERVER);
        echo $_SERVER['HTTP_USER_AGENT'];
    }

    protected function _byte_format($size, $dec = 2)
    {
        $a = array(
            "B",
            "KB",
            "MB",
            "GB",
            "TB",
            "PB",
            "EB",
            "ZB",
            "YB"
        );
        $pos = 0;
        while ($size >= 1024) {
            $size/= 1024;
            $pos++;
        }
        return round($size, $dec) ." ".$a[$pos];
    }

    public function phpinfoAction()
    {
        // action body
        ini_set('max_execution_time',5000);
        phpinfo();
    }


}



