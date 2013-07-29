<meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
<?php
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '256M');
set_time_limit(300);
//$dir_audio = '/home/www/html/caiji/highschool/highschool3';
////$mode = 0755;
//chmod($dir_audio, 0777);

system('echo "Joomla8" | sudo -u root -S chmod 777 -R /home/www/html/caiji/highschool/highschool3');

//fetch the word need decode
$document_url = 'http://www.hxen.com/englishlistening/zhongkao/lianxi/2012-04-18/177381.html';
$document_ans_url = 'http://www.hxen.com/englishlistening/zhongkao/lianxi/2012-04-18/177381_2.html';
//process word file
$homepage = file_get_contents_utf8($document_url);
$document_ans = file_get_contents_utf8($document_ans_url);


//mp3 silence mark
$file_seri = 'mp3silennce_seri.txt';
$silence_str = file_get_contents($file_seri);
$silence_arr = unserialize($silence_str);

//echo mb_detect_encoding($subject);
// echo $subject for compare the result
//$file_subject = 'subject.html';
//file_put_contents($file_subject, $subject);
//echo $homepage;
//remove the useless content
//$homepage = $subject;
//$homepage_start = strpos($homepage, '音频下载[点击右键另存为]');
$homepage_start = strpos($homepage, '<div class="mp3player">');
$document_ans_start = strpos($document_ans, '<div class="mp3player">');
//$homepage_middle = strpos($homepage, "参考答案", $homepage_start);
$homepage_end = strpos($homepage, '<div class="endPageNum">');
$document_ans_end = strpos($document_ans, '<div class="endPageNum">');
//$subject_title = substr($homepage, $homepage_start, $homepage_end - $homepage_start);
$subject_title = strip_tags(substr($homepage, $homepage_start, $homepage_end - $homepage_start));
//$subject_title = html_entity_decode(substr($homepage, $homepage_start, $homepage_end - $homepage_start));
//$subject_answer = substr($document_ans, $homepage_start, $homepage_end - $homepage_start);
$subject_answer = strip_tags(substr($document_ans, $document_ans_start, $document_ans_end - $document_ans_start));
//$subject_answer = html_entity_decode(substr($document_ans, $homepage_start, $homepage_end - $homepage_start));
// echo $subject for compare the result
//$file_subject = 'subject.html';
//
//$html_head_str = <<<'EOD'
//<!DOCTYPE html>
//<html>
//    <head>
//        <title></title>
//        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
//    </head>
//    <body>
//EOD;
//
//$html_foot_str = <<<'EOD'
//    </body>
//</html>
//EOD;
//
//file_put_contents($file_subject, $html_head_str);
//file_put_contents('$subject', $subject_title);
//file_put_contents($file_subject, $subject_title, FILE_APPEND);
////file_put_contents($file_subject, utf8_encode($subject_title));
//file_put_contents($file_subject, $subject_answer, FILE_APPEND);
//file_put_contents($file_subject, $html_foot_str, FILE_APPEND);


echo $subject_title;
echo '<br />';
echo $subject_answer;
echo '<br />';

//readline
$lines = explode(PHP_EOL, $subject_title);
print_r($lines);
echo '<br />';
$subject_notes = array_values(array_slice($lines, 7, count($lines) - 7 - 1, true));
print_r($subject_notes);
echo '<br />';

$subject_notes_1 = $subject_notes[0] . '<br/>' . $subject_notes[1];
$subject_notes_2 = $subject_notes[8] . '<br/>' . $subject_notes[9];
$subject_notes_3 = $subject_notes[16] . '<br/>' . $subject_notes[17];


$pattern = '/\d{1,2}\.\s[^\r\n]*/';
$pattern_note = '/\d{1,2}\.\s[^\r\n]*/';
$pattern_selections = '/A\.(.*)B\.(.*)C\.(.*)/';

//set the oattern for the correct answer
$pattern_answer = '/([ABC]{4,6})/';


preg_match_all($pattern, $subject_title, $matches);
preg_match_all($pattern_selections, $subject_title, $matches_selections);
preg_match_all($pattern_answer, $subject_answer, $matches_answer);


//print_r($matches);
//echo '<br />';
//print_r($matches_selections);
//echo '<br />';
//print_r($matches_answer);
// generate answer string
$str_answer = implode($matches_answer[1]);

$file = 'test.html';
file_put_contents($file, '< /br>');

for ($i = 0; $i < count($matches[0]); $i++) {

    $result[$i]['A'] = strip_tags($matches_selections[1][$i]);
    $result[$i]['B'] = strip_tags($matches_selections[2][$i]);
    $result[$i]['C'] = strip_tags($matches_selections[3][$i]);
//
//
//        //fetch and process question
//        $matches[2][$j] = str_replace($matches[4][$j], '<span style="background-color: silver" > &nbsp;' . $matches[4][$j] . '&nbsp;</span>; ', $matches[2][$j]);
//
//        str_pad($str,20,".",STR_PAD_LEFT)
//    $result[$i]['no'] = str_pad($i + 1, 3, "0", STR_PAD_LEFT);
    $result[$i]['no'] = $i + 1;
    $result[$i]['title'] = '<p>title</p>';

    switch ($i) {
        case 0:
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
            $result[$i]['title'] = $subject_notes_1;
            $result[$i]['title'] .= '<audio id="mp3" controls="" autoplay="false">';
            $result[$i]['title'] .= '  <source src="images/mp3/chongqin_2011.mp3#t=' . $silence_arr[$i] . ',' . $silence_arr[$i + 1] . '" type="audio/mpeg">';
            $result[$i]['title'] .= '</audio>';
            break;
        case 6:
        case 7:
        case 8:
        case 9:
        case 10:
        case 11:
            $result[$i]['title'] = $subject_notes_2;
            $result[$i]['title'] .= '<audio id="mp3" controls="" autoplay="false">';
            $result[$i]['title'] .= '  <source src="images/mp3/chongqin_2011.mp3#t=' . $silence_arr[$i] . ',' . $silence_arr[$i + 1] . '" type="audio/mpeg">';
            $result[$i]['title'] .= '</audio>';
            break;
        case 12:
            $result[$i]['title'] = $subject_notes_3;
            $result[$i]['title'] .= '<audio id="mp3" controls="" autoplay="false">';
            $result[$i]['title'] .= '  <source src="images/mp3/chongqin_2011.mp3#t=' . $silence_arr[$i] . ',' . $silence_arr[$i + 1] . '" type="audio/mpeg">';
            $result[$i]['title'] .= '</audio>';
            break;
        case 14:
        case 15:
            $result[$i]['title'] = $subject_notes_3;
            break;
        case 16:
            $result[$i]['title'] = $subject_notes_3;
            $result[$i]['title'] .= '<audio id="mp3" controls="" autoplay="false">';
            $result[$i]['title'] .= '  <source src="images/mp3/chongqin_2011.mp3#t=' . $silence_arr[$i] . ',' . $silence_arr[$i + 1] . '" type="audio/mpeg">';
            $result[$i]['title'] .= '</audio>';
            break;
        default:
            $result[$i]['title'] = $subject_notes_3;
            ;
    }
//    $result[$i]['title'] = <<<'EOD'
//<p>title</p>
//<audio id="mp3" controls='' autoplay='false'>
//    <source src="images/mp3/chongqin_2011.mp3#t=$silence_arr[$i],$silence_arr[$i+1]" type='audio/mpeg'>
//</audio>
//EOD;
//
//str_replace(array("\n", "\r"), '', $str);
//        //fetch the correct answer
    $result[$i]['ANS'] = substr($str_answer, $i, 1);

    $result[$i]['note'] = '';
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

$src = "/home/www/html/caiji/highschool/highschool3";
//$dest = "/var/www/html/lesson/utility/ms-listen/";
$dest = "/home/www/html/lesson/utility/ms-listen/";

$status = shell_exec("cp -rf $src $dest");

//recurse_copy($src, $dest);

echo "<H3>Copy Paste completed!</H3>";

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

//change the gb2312 web content to utf8
function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, GB2312', true));
}
?>