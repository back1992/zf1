<?php

class ToolsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        require_once ('PHPBeauty/Beautifier.php');
        $sourceArr = Zend_Registry::get('sourceArr');
        $targetArr = Zend_Registry::get('targetArr');



        foreach ($sourceArr as $sourceDir) {
            $files = $this->_listdir($sourceDir);
            sort($files, SORT_LOCALE_STRING);

//        $files = array('/var/www/html/quiz-de/administrator/components/com_ariquiz/kernel/Components/class.Constants.php', '/var/www/html/quiz-de/administrator/components/com_ariquiz/admin.ariquiz.php', '/var/www/html/quiz-de/administrator/components/com_ariquiz/pages/bank_add.php');


            foreach ($files as $f) {
                if (pathinfo($f, PATHINFO_EXTENSION) == 'php') {
//                    $f = $this->_evalDecode($f);
                    $targetArr[] = $f;
                }
                Zend_Registry::set("targetArr", $targetArr);
            }
        }
    }

    public function indexAction() {
        // action body
    }

    public function decodeAction() {
        // action body
        $sourceArr = Zend_Registry::get('sourceArr');
        $targetArr = Zend_Registry::get('targetArr');

//        foreach ($sourceArr as $sourceDir) {
//            $files = $this->_listdir($sourceDir);
//            sort($files, SORT_LOCALE_STRING);



        foreach ($targetArr as $file) {
            $file = $this->_evalDecode($file);
        }
    }

//    }

    public function getInputFnameAction() {
        // action body
    }

    protected function _listdir($dir = '.') {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array();
        $this->_listdiraux($dir, $files);

        return $files;
    }

    protected function _listdiraux($dir, &$files) {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filepath = $dir == '.' ? $file : $dir . '/' . $file;
            if (is_link($filepath))
                continue;
            if (is_file($filepath))
                $files[] = $filepath;
            else if (is_dir($filepath))
                $this->_listdiraux($filepath, $files);
        }
        closedir($handle);
    }

    protected function _evalDecode($file) {
        $oBeautifier = new PHP_Beautifier();
        $oBeautifier->addFilter('Default');
        $oBeautifier->setIndentChar(' ');
        $oBeautifier->setIndentNumber(4);
        $oBeautifier->setNewLine("\n");

        $str = file_get_contents($file);
        $regPattern = "/eval\s*\(base64_decode\s*\(\s*\'([^']*)'\)\)\;/";
        while (preg_match($regPattern, $str, $match)) {

            $strDecode = base64_decode($match[1]);
            $str_temp = str_replace($match[0], $strDecode, $str);
            $str_final = str_replace('; ;', ';', $str_temp);
//            echo $str_final;
            file_put_contents($file, $str_final);
            echo $file . ' success decoded! <br />';
            $oBeautifier->setInputFile($file);
            $oBeautifier->setOutputFile($file);
            $oBeautifier->process();
            $oBeautifier->save();
            echo '<br><span style="color:green">Beautifer file ok, nice job!</span><br />';
            return $file;
        }
    }

}

