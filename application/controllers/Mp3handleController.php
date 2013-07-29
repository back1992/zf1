<?php

class Mp3handleController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function mp3spltAction()
    {
        // action body
        $mp3Tag = file_get_contents('audio/mp3tag');

//echo $mp3Tag;
//$mp3TagArray = explode(' ', $mp3Tag);
//print_r($mp3TagArray);

        $pattern = '/\d*\.\d*/';
        preg_match_all($pattern, $mp3Tag, $matches);
//print_r($matches);

        foreach (array_unique($matches[0]) as $matches_value) {
            $mp3TagArrayTmp[] = $matches_value;
        }

        // print_r($mp3TagArrayTmp);
        echo '<br />';

//
//array_shift($mp3TagArray);
//print_r($mp3TagArray);
//echo '<br />';
//
//
        function fine($n) {
            // (($n-1)>0) ? return($n-1) : return($n);
            return($n-1);
        }

        $mp3TagArrayTmp = array_map("fine", $mp3TagArrayTmp);
        array_unshift($mp3TagArrayTmp, "0");
        print_r($mp3TagArrayTmp);
        // print_r($mp3TagArray);
        echo '<br />';


        $file_str_seri = 'audio/encode/mp3silennce_seri.txt';
        // chmod("audio/encode", 0755);
        // $ourFileHandle = fopen($file_str_seri, 'w') or die("can't open file");
        $result_str_seri = serialize($mp3TagArrayTmp);
        file_put_contents($file_str_seri, $result_str_seri);
        // fclose($ourFileHandle);


//array to json
        // $file_str_json = 'audio/encode//mp3silennce_json.txt';
        // $result_str_json = json_encode($mp3TagArray);
        // file_put_contents($file_str_json, $result_str_json);

        // var_dump($this->view->serverUrl());
        $baseUrl = $this->view->serverUrl();

        // for ($i = 0; $i < count($mp3TagArray); $i++) {
        for ($i = 0; $i < count($mp3TagArrayTmp) - 1; $i++) {
            // if (!!$mp3TagArray[$i] && !!$mp3TagArray[$i + 1]) {
                // echo '<audio src="audio/shandong.mp3#t=' . $mp3TagArray[$i] . ',' . $mp3TagArray[$i + 1] . '"  controls >';

            echo '<audio src="'.$baseUrl.'/audio/shandong.mp3#t=' . $mp3TagArrayTmp[$i] . ',' . $mp3TagArrayTmp[$i + 1] . '"  controls >';
            echo 'audio is not supported.';
            echo '</audio><br />';
            echo $i+1 ;
            // echo '<button name="button'.$i.'" type="submit" formaction="http://www.baidu.com">Buggy Whipp Bio</button> <br />';
            echo '<button name="button'.$i.'" type="button" onclick="alert(\'Hello world!\')">Buggy Whipp Bio</button> <br />';
            // echo '<button name="button'.$i.'" type="button" formaction="http://www.baidu.com">Buggy Whipp Bio</button> <br />';
            // echo '<button name="button"'.$i+1.' type="button">Buggy Whipp Bio</button> <br />';

            // }
        }

//Create Form
//         $form = new Zend_Form();
//         $form->setAction('success');
//         $form->setMethod('post');
//         // $form->setDescription("sign up form");
//         // $form->setAttrib('sitename', 'loudbite');
//         $form->addElement('submit', 'submit');
//         $submitButton = $form->getElement('submit');
//         $submitButton->setLabel('merge elements');
// //Add the form to the view
//         $this->view->form = $form;

        // var_dump($form);


        // $this->view->layout()->scriptTags = '<script src="my.js"></script>';

        // $this->view->headScript()->appendFile('/js/my.js');

        // $this->view->layout()->scriptTags = '<script>alert("here\'s my" + "test")</script>';
        // $this->view->layout()->scriptTags = '<script src="js/my.js"></script>';  

        
    }

    public function testJqueryAction()
    {
        // action body
        $baseUrl = $this->view->serverUrl();
        // var_dump($baseUrl);
        $phpArray = array(
          0 => 001-1234567, 
          1 => 1234567, 
          2 => 12345678, 
          3 => 12345678,
          4 => 12345678,
          5 => 'AP1W3242',
          6 => 'AP7X1234',
          7 => 'AS1234',
          8 => 'MH9Z2324', 
          9 => 'MX1234', 
          10 => 'TN1A3242',
          11 => 'ZZ1234'
          );
        // var_dump($phpArray);
// 
        $stdClass = (object) array('one' => 'apple', 'two' => 'banana', 
'three' => 'pear');
        // $this->view->myData = $stdClass;
        $this->view->myDatas = $phpArray;

    }

    public function testJsAction()
    {
        // action body
    }


}



