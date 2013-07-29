<?php

class CrawlerController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function addQuestAction() {
        // action body
    }

    public function addListenAction() {
        // action body
        ini_set('error_reporting', E_ALL);


        //set the $url and $refurl
//        $url = "http://quiz-de.local/administrator/index2.php?option=com_ariquiz&task=question_add&quizId=1";
//        $refurl = "http://quiz-de.local/administrator/index2.php?option=com_ariquiz&task=question_list&quizId=1&arimsg=Complete.QuestionSave";
//        $file_seri = APPLICATION_PATH . '/../utility/ms-listen/string_seri.txt';
//        $this->_curl($file_seri, $url, $refurl);


        $url = "http://localhost/j15/administrator/index2.php?option=com_ariquiz&task=question_add&quizId=1";
        $refurl = "http://localhost/j15/administrator/index.php";
//        $url = "http://highschool3.local/administrator/index.php?option=com_ariquizlite&task=question_add&quizId=1";
//        $refurl = "	http://highschool3.local/administrator/index.php";
        $file_seri = APPLICATION_PATH . '/../utility/ms-listen/highschool3/string_seri.txt';
        $this->_curl($file_seri, $url, $refurl);
    }

    protected function _curl($file_seri, $url, $refurl) {
        //set the url login username and password
        $uname = "admin";
        $upswd = "Joomla8";
        //set the cookie for login next
        $cookie_jar = tempnam('./tmp', 'cookielin');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $ret = curl_exec($ch);
// echo $ret;
        if (!preg_match('/name="([a-zA-z0-9]{32})"/', $ret, $spoof)) {
            preg_match("/name='([a-zA-z0-9]{32})'/", $ret, $spoof);
        }

// POST fields
        $postfields = array();
        $postfields['username'] = urlencode($uname);
        $postfields['passwd'] = urlencode($upswd);
        $postfields['lang'] = '';
        $postfields['option'] = 'com_login';
        $postfields['task'] = 'login';
        if (count($spoof))
            $postfields[$spoof[1]] = '1';
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $ret = curl_exec($ch);
        echo $ret;



        $crawl_str = file_get_contents($file_seri);
        $crawl_arr = unserialize($crawl_str);

        //set the CONST of this quiz
        $questionCategoryId = 1;
        $templateId = 0;
        $questionTypeId = 2;
        $score = 1;
        $chkSQRandomizeOrder = 0;
        $ddlSQView = 0;
        $BankCategoryId = 0;
        $ddlBank = 0;
        $questionId = '';
        $hidemainmenu = 1;
        $boxchecked = '';
        $option = 'com_ariquiz';
        $task = 'question_add$apply';
        $quizId = 1;

        foreach ($crawl_arr as $quiz_data) {
            $postquestion = array();
            $postquestion['zQuiz[QuestionCategoryId]'] = $questionCategoryId;
            $postquestion['templateId'] = $templateId;
            $postquestion['questionTypeId'] = $questionTypeId;
            $postquestion['zQuiz[Score]'] = $score;
            $postquestion['zQuiz_Question'] = $quiz_data['title'];
            $postquestion['zQuiz[Question]'] = $quiz_data['title'];
            $postquestion['zQuiz_Note'] = $quiz_data['note'];
            $postquestion['zQuiz[Note]'] = $quiz_data['note'];
            $postquestion['chkSQRandomizeOrder'] = $chkSQRandomizeOrder;
            $postquestion['ddlSQView'] = $ddlSQView;

            $postquestion['tbxAnswer_0'] = $quiz_data['A'];
            $postquestion['hidQueId_0'] = '';
            $postquestion['hidCorrect_0'] = ($quiz_data['ANS'] == 'A') ? TRUE : '';
            $postquestion['tbxScore_0'] = '';
            $postquestion['tblQueContainer_hdnstatus_0'] = '';
            $postquestion['rbCorrect'] = true;
            $postquestion['tbxAnswer_1'] = $quiz_data['B'];
            $postquestion['hidQueId_1'] = '';
            $postquestion['hidCorrect_1'] = ($quiz_data['ANS'] == 'B') ? TRUE : '';
            $postquestion['tbxScore_1'] = '';
            $postquestion['tblQueContainer_hdnstatus_1'] = '';
            $postquestion['tbxAnswer_2'] = $quiz_data['C'];
            $postquestion['hidQueId_2'] = '';
            $postquestion['hidCorrect_2'] = ($quiz_data['ANS'] == 'C') ? TRUE : '';
            $postquestion['tbxScore_2'] = '';
            $postquestion['tblQueContainer_hdnstatus_2'] = '';
//            $postquestion['tbxAnswer_3'] = $quiz_data['D'];
//            $postquestion['hidQueId_3'] = '';
//            $postquestion['hidCorrect_3'] = ($quiz_data['ANS'] == 'D') ? TRUE : '';
//            $postquestion['tbxScore_3'] = '';
//            $postquestion['tblQueContainer_hdnstatus_3'] = '';


            $postquestion['BankCategoryId'] = $BankCategoryId;
            $postquestion['ddlBank'] = $ddlBank;
//            $postquestion['bankQuestionId'] = $BankQuestionId;
            $postquestion['questionId'] = $questionId;
            $postquestion['hidemainmenu'] = $hidemainmenu;
            $postquestion['boxchecked'] = $boxchecked;
            $postquestion['option'] = $option;
            $postquestion['task'] = $task;
            $postquestion['quizId'] = $quizId;


            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_REFERER, $refurl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postquestion);
// $ret = curl_exec($ch);
            if (curl_exec($ch) === false) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                echo "报告首长！ 第" . $quiz_data['no'] . "号试题添加操作完成，没有任何错误</br>\n";
            }
        }
    }

    public function addListen3Action() {
        // action body
        ini_set('error_reporting', E_ALL);


        //set the $url and $refurl
        $url = "http://localhost/highschool3_4/administrator/index.php?option=com_ariquizlite&task=question_add&quizId=1";
        $refurl = "http://localhost/highschool3_4/administrator/index.php";
//        $url = "http://highschool3.local/administrator/index.php?option=com_ariquizlite&task=question_add&quizId=1";
//        $refurl = "	http://highschool3.local/administrator/index.php";
        $file_seri = APPLICATION_PATH . '/../utility/ms-listen/highschool3/string_seri.txt';
        $this->_curl3($file_seri, $url, $refurl);
    }

    protected function _curl3($file_seri, $url, $refurl) {
        //set the url login username and password
//        $uname = "admin";
        $uname = "back1992";
        $upswd = "Joomla8";
        //set the cookie for login next
        $cookie_jar = tempnam('./tmp', 'cookielin');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $ret = curl_exec($ch);
//        echo $ret;
        if (!preg_match('/name="([a-zA-z0-9]{32})"/', $ret, $spoof)) {
            preg_match("/name='([a-zA-z0-9]{32})'/", $ret, $spoof);
        }

// POST fields
        $postfields = array();
        $postfields['username'] = urlencode($uname);
        $postfields['passwd'] = urlencode($upswd);
        $postfields['lang'] = '';
        $postfields['option'] = 'com_login';
        $postfields['task'] = 'login';
        if (count($spoof))
            $postfields[$spoof[1]] = '1';
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $ret = curl_exec($ch);
//        echo $ret;



        $crawl_str = file_get_contents($file_seri);
        $crawl_arr = unserialize($crawl_str);

        //set the CONST of this quiz
        $questionCategoryId = 1;
        $templateId = 0;
        $questionTypeId = 1;
        $score = 1.5;
//        $chkSQRandomizeOrder = 0;
//        $ddlSQView = 0;
        $questionId = '';
//        $hidemainmenu = 1;
//        $boxchecked = '';
        $option = 'com_ariquizlite';
        $task = 'question_add$save';
        $quizId = 1;

        foreach ($crawl_arr as $quiz_data) {
            $postquestion = array();
            $postquestion['zQuiz[QuestionCategoryId]'] = $questionCategoryId;
            $postquestion['templateId'] = $templateId;
            $postquestion['questionTypeId'] = $questionTypeId;
            $postquestion['zQuiz[Score]'] = $score;
            $postquestion['zQuiz[Question]'] = $quiz_data['title'];
//            $postquestion['zQuiz[Note]'] = $quiz_data['note'];
//            $postquestion['chkSQRandomizeOrder'] = $chkSQRandomizeOrder;
//            $postquestion['ddlSQView'] = $ddlSQView;

            $postquestion['tbxAnswer_0'] = $quiz_data['A'];
            $postquestion['hidQueId_0'] = '';
            $postquestion['hidCorrect_0'] = ($quiz_data['ANS'] == 'A') ? TRUE : '';
//            $postquestion['tbxScore_0'] = '';
            $postquestion['tblQueContainer_hdnstatus_0'] = '';
            $postquestion['rbCorrect'] = true;
            $postquestion['tbxAnswer_1'] = $quiz_data['B'];
            $postquestion['hidQueId_1'] = '';
            $postquestion['hidCorrect_1'] = ($quiz_data['ANS'] == 'B') ? TRUE : '';
//            $postquestion['tbxScore_1'] = '';
            $postquestion['tblQueContainer_hdnstatus_1'] = '';
            $postquestion['tbxAnswer_2'] = $quiz_data['C'];
            $postquestion['hidQueId_2'] = '';
            $postquestion['hidCorrect_2'] = ($quiz_data['ANS'] == 'C') ? TRUE : '';
//            $postquestion['tbxScore_2'] = '';
            $postquestion['tblQueContainer_hdnstatus_2'] = '';


            $postquestion['questionId'] = $questionId;
//            $postquestion['hidemainmenu'] = $hidemainmenu;
//            $postquestion['boxchecked'] = $boxchecked;
            $postquestion['option'] = $option;
            $postquestion['task'] = $task;
            $postquestion['quizId'] = $quizId;


            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_REFERER, $refurl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postquestion);
// $ret = curl_exec($ch);
            if (curl_exec($ch) === false) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                echo "报告首长！ 第" . $quiz_data['no'] . "号试题添加操作完成，没有任何错误</br>\n";
            }
        }
    }

}

