
<?php

$mp3Tag = file_get_contents('audio/cq2011/mp3Tag');

//echo $mp3Tag;
//$mp3TagArray = explode(' ', $mp3Tag);
//print_r($mp3TagArray);

$pattern = '/\d*\.\d*/';
preg_match_all($pattern, $mp3Tag, $matches);
//print_r($matches);

foreach (array_unique($matches[0]) as $matches_value) {
    $mp3TagArrayTmp[] = $matches_value;
}

print_r($mp3TagArrayTmp);
echo '<br />';

//
//array_shift($mp3TagArray);
//print_r($mp3TagArray);
//echo '<br />';
//
//
function fine($n) {
    return($n - 1);
}

$mp3TagArrayTmp = array_map("fine", $mp3TagArrayTmp);
print_r($mp3TagArrayTmp);
echo '<br />';

$offset = -1;

$mp3TagArray[0] = 0.01;
$mp3TagArray[1] = $mp3TagArrayTmp[3 + $offset];
$mp3TagArray[2] = $mp3TagArrayTmp[4 + $offset];
$mp3TagArray[3] = $mp3TagArrayTmp[5 + $offset];
$mp3TagArray[4] = $mp3TagArrayTmp[6 + $offset];
$mp3TagArray[5] = $mp3TagArrayTmp[7 + $offset];
$mp3TagArray[6] = $mp3TagArrayTmp[8 + $offset];
$mp3TagArray[7] = $mp3TagArrayTmp[9 + $offset];
$mp3TagArray[8] = $mp3TagArrayTmp[11 + $offset];
$mp3TagArray[9] = $mp3TagArrayTmp[12 + $offset];
$mp3TagArray[10] = $mp3TagArrayTmp[13 + $offset];
$mp3TagArray[11] = $mp3TagArrayTmp[14 + $offset];
$mp3TagArray[12] = $mp3TagArrayTmp[15 + $offset];
$mp3TagArray[13] = $mp3TagArrayTmp[17 + $offset];
$mp3TagArray[14] = '';
$mp3TagArray[15] = '';
$mp3TagArray[16] = $mp3TagArrayTmp[17 + $offset];
$mp3TagArray[17] = $mp3TagArrayTmp[20 + $offset];
$mp3TagArray[18] = '';
$mp3TagArray[19] = '';
$mp3TagArray[20] = '';

$file_str_seri = '/home/www/html/caiji/highschool/highschool3/mp3silennce_seri.txt';
$result_str_seri = serialize($mp3TagArray);
file_put_contents($file_str_seri, $result_str_seri);


//array to json
$file_str_json = '/home/www/html/caiji/highschool/highschool3/mp3silennce_json.txt';
$result_str_json = json_encode($mp3TagArray);
file_put_contents($file_str_json, $result_str_json);

//
//    echo '<audio src="audio/cq2011/chongqin_2011.mp3"  controls >';
//    echo 'audio is not supported.';
//    echo '</audio>';
//    echo '<br />';
//
//
//for ($i = 0; $i < count($mp3TagArray)-1; $i++) {
//    
//    echo '<audio src="audio/cq2011/chongqin_2011.mp3#t=' . $mp3TagArray[$i] . ',' . $mp3TagArray[$i + 1] . '"  controls >';
//    echo 'audio is not supported.';
//    echo '</audio>';
//    echo $i + 1 . '<br />';
//}

for ($i = 10; $i < count($mp3TagArray); $i++) {
    if (!!$mp3TagArray[$i] && !!$mp3TagArray[$i + 1]) {
        echo '<audio src="audio/cq2011/chongqin_2011.mp3#t=' . $mp3TagArray[$i] . ',' . $mp3TagArray[$i + 1] . '"  controls >';
        echo 'audio is not supported.';
        echo '</audio>';
        echo $i + 1 . '<br />';
    }
}
?>
