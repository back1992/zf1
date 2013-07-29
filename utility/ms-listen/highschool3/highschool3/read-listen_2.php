<meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
<?php
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '256M');

//fetch the word need decode
$url = 'http://www.rrting.com/English/zhongkao/244207/';
//process word file
$homepage = file_get_contents_utf8($url);

//readline
//echo $homepage;
//remove the useless content
$homepage_start = strpos($homepage, "第1卷(共100分)");
$homepage_middle = strpos($homepage, "参考答案", $homepage_start);
$homepage_end = strpos($homepage, "特别声明：本栏目");
$subject_title = substr($homepage, $homepage_start, $homepage_middle - $homepage_start);
//$subject_title = html_entity_decode(substr($homepage, $homepage_start, $homepage_middle - $homepage_start));
$subject_answer = substr($homepage, $homepage_middle, $homepage_end - $homepage_middle);
// echo $subject for compare the result
$file_subject = 'subject.html';
//file_put_contents($file_subject, $subject_title.'< /br>'.$subject_answer);
file_put_contents($file_subject, $subject_title);


//readline
//$lines = explode(PHP_EOL, $subject_title);
//set the pattern for the whole question
//$pattern = '/(^\d+[^\(\n]*)?\([A-D]\)\.\s[^\(\n]+/';
$pattern = '/(\d{1,2})\.([^A]*)/';
//set the oattern for the correct answer
$pattern_selections = '/(A\..*)(?=\<BR\>)/U';
$pattern_select = '/(A\..*(?=B\.))(B\..*(?=C\.))(C\..*)/';

//set the oattern for the correct answer
$pattern_answer = '/([ABC]{4,6})/';
//set the pattern for  beizhu
//$pattern_note = '/^\d+\.\s[^\d()]+/m';
//process regular expression


preg_match_all($pattern, $subject_title, $matches);
preg_match_all($pattern_selections, $subject_title, $matches_selections);
preg_match_all($pattern_answer, $subject_answer, $matches_answer);
//preg_match_all($pattern_note, $subject, $matches_note);
//explode $matches_selections to $matche_select array use &nbsp 
$matches_selection = array();
for ($i = 0; $i < count($matches_selections[1]); $i++) {
//    $matches_selection[$i] = explode("&nbsp;&nbsp;&nbsp;", $matches_selections[1][$i]);
    $matches_selection[$i] = str_replace("&nbsp;", " ", $matches_selections[1][$i]);
    preg_match_all($pattern_select, $matches_selection[$i], $matches_select[$i]);
//    preg_match_all($pattern_select, $matches_selection[$i], $matches_select[$i]);
}




//
//print_r($matches);
//print_r($matches_selections);
//print_r($matches_answer);
//print_r($matches_note);
////build a arrray to put the match answer in the model of [no]=>ABCD
//$matches_answer_filter = array();
// generate answer string
$str_answer = implode($matches_answer[1]);

$file = 'test.html';

file_put_contents($file, '< /br>');
//$tmp_num = 0;
$result = array();
for ($i = 0; $i < count($matches[0]); $i++) {
//    //fetch selections
//    $select_matches = array();
//    preg_match_all($pattern_select, $matches[5][$j], $select_matches);
//
//    //process selections
//    if (count($select_matches[0]) == 4) {
////        echo $matches[1][$j] . '<br />';
////        break;
////    } else {
    $result[$i]['A'] = trim($matches_select[$i][1][0]);
    $result[$i]['B'] = trim($matches_select[$i][2][0]);
    $result[$i]['C'] = trim($matches_select[$i][3][0]);
//
//
//        //fetch and process question
//        $matches[2][$j] = str_replace($matches[4][$j], '<span style="background-color: silver" > &nbsp;' . $matches[4][$j] . '&nbsp;</span>; ', $matches[2][$j]);
//
//        str_pad($str,20,".",STR_PAD_LEFT)
//    $result[$i]['no'] = str_pad($i + 1, 3, "0", STR_PAD_LEFT);
    $result[$i]['no'] = $matches[1][$i];
    $result[$i]['title'] = '<p>No.' . $i . '{mp3}' . $result[$i]['no'] . 'mp3{/mp3} <audio controls='' autoplay='true'>
  <source src='images/highschool/011.mp3' type='audio/mpeg'>
</audio></p>';
//
//str_replace(array("\n", "\r"), '', $str);
//        //fetch the correct answer
    $result[$i]['ANS'] = substr($str_answer, $i, 1);
//    $result[$i]['note'] = str_replace(array("\n", "\r"), '', $matches_note[0][$i]);
    //
    file_put_contents($file, $result[$i]['no'] . $result[$i]['title'], FILE_APPEND | LOCK_EX);
    file_put_contents($file, '(A)' . $result[$i]['A'] . '  (B)' . $result[$i]['B'] . '  (C)' . $result[$i]['C'] . '</br>', FILE_APPEND | LOCK_EX);
    file_put_contents($file, 'the correct answer is ' . $result[$i]['ANS'] . '</br>', FILE_APPEND | LOCK_EX);
//    file_put_contents($file, 'the tp iis ' . $result[$i]['note'] . '</br>', FILE_APPEND | LOCK_EX);
//
}
//array to string
$file_str_seri = 'string_seri.txt';
$result_str_seri = serialize($result);
file_put_contents($file_str_seri, $result_str_seri);

//array to json
$file_str_json = 'string_json.txt';
$result_str_json = json_encode($result);
file_put_contents($file_str_json, $result_str_json);

//change the gb2312 web content to utf8
function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, GB2312', true));
}
?>