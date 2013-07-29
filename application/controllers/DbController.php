<?php

class DbController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
// action body
    }

    public function curdAction() {
// action body
        $multidb = Zend_Registry::get("multidb");
        $db = $multidb->getDb('s485825db2');
//fetch question
        $table = new Application_Model_DbTable_AriQuizQuestionVersion();
        $where = array('QuestionVersionId' => 400);
        $result = $table->getQuestions($where, 'QuestionVersionId')->toArray();
        var_dump($result);
//        $sql = 'SELECT * FROM quiz_ariquiz';
//        $result = $db->fetchAll($sql, 2);
//        var_dump($result);
//        $sql = "SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) 
//    AS statement FROM information_schema.tables 
//    WHERE table_name LIKE 'toefl_%'";
//        $result = $db->fetchAll($sql);
//        echo '<br />';
//        print_r($result);
//
//        $sql2 = $result[0]["statement"];
//        echo $sql2;
//        $db->query($sql2);
//        $m = $db->delete('quiz_ariquizquestion', 'QuizId != 1');
//        $n = $db->delete('quiz_ariquizquestionversion', 'QuestionId >= 395');
//        var_dump($m);
    }

    public function localCurdAction() {
// action body
        $multidb = Zend_Registry::get("multidb");
        $db = $multidb->getDb('s485825db2');
//        $sql = 'SELECT Question FROM quiz_ariquizquestionversion where QuestionVersionId > 400';
//        $result = $db->fetchRow($sql);
//        var_dump($result);

        $table = new Application_Model_DbTable_AriQuizQuestionVersion();
        $where = array('QuestionVersionId' => 400);
        $result = $table->getQuestions($where, 'QuestionVersionId')->toArray();
        var_dump($result);

        for ($i = 0; $i < count($result); $i++) {
            $set = array(
                'Question' => 'yellow',
            );

            $where = $db->quoteInto('first_name = ?', 'Robin');

            $rows_affected = $table->update($set, $where);
        }



//
//
//        $where = $db->quoteInto('QuestionVersionId >= ?', '400');
//        $order = 'QuestionVersionId';
//        $count = 20;
//        $offset = 10;
//
//
//        $rowset = $table->fetchAll($where, $order, $count, $offset);
//        var_dump($rowset);
//
//        foreach ($rowset as $row) {
//            foreach ($tables as $key => $value) {
//                echo $value . '<br />';
//            }
//        }
//        $row = $table->fetchRow($table->select()->where('QuestionVersionId  >= ?', 400));
//
//        $rowArray = $row->toArray();
//
//// Now use it as a normal array
//        foreach ($rowArray as $column => $value) {
//            echo "Column: $column\n";
//            echo "Value:  $value\n";
//        }
//
//        foreach ($result as $tables) {
//            foreach ($tables as $key => $value) {
//                echo $value . '<br />';
//            }
//        }
//        $m = $db->delete('quiz_ariquizquestion', 'QuizId != 1');
//        $n = $db->delete('quiz_ariquizquestionversion', 'QuestionId >= 395');
//        var_dump($m);
    }

    public function dropTablePrefixAction() {
// action body
//        local
//        $multidb = Zend_Registry::get("multidb");
//        $db = $multidb->getDb('db-quiz-de');
//server
        $multidb = Zend_Registry::get("multidb");
//        $db = $multidb->getDb('s485825db2');
        $db = $multidb->getDb('j3');
        $sql = "show tables like 'jos_ari%'";
//        $sql = "SHOW TABLES WHERE `Tables_in_s485825db2` NOT LIKE 'quiz%';";
        $result = $db->fetchAll($sql);
        var_dump($result);

        foreach ($result as $tables) {
            foreach ($tables as $key => $value) {
                echo $value . '<br />';
                $sql = 'drop table `' . $value . '`';
                echo $sql;
                $db->query($sql);
            }
        }
    }

    public function importAction() {
// action body
        ini_set('max_execution_time', 5000);
        $multidb = Zend_Registry::get("multidb");
//        $db = $multidb->getDb('db-quiz-de');
        $db = $multidb->getDb('s485825db2');
        $db->beginTransaction();
//        $schema = file_get_contents(APPLICATION_PATH . '/../utility/quiz-de.sql');
        $schema = file_get_contents(APPLICATION_PATH . '/../utility/6.sql');
        $schema = str_replace('`quiz_', '`tofel_', $schema);
//        echo $sql;
//        $db->query($sql);
// $schema is content of *.sql file 
        $schema = explode(";\n", $schema);
        $schema = array_map('trim', $schema);
        $schema = array_filter($schema, 'strlen');

        try {
            foreach ($schema as $sql) {
                echo $sql . '<br />';
                $db->query($sql);
            }
        } catch (Exception $e) {
// [...] 
        }
        $db->commit();
//        $db->close();
    }

    public function multiQueryAction() {
// action body
        $sqlFile = APPLICATION_PATH . '/../utility/quiz-de.sql';
        $sql = file_get_contents($sqlFile);
        str_replace('`quiz_', '`tofel_', $sql);
        $mysqliobject = new mysqli(
                'localhost', 'root', 'Joomla8', 'quiz-de'
        );
        $mysqliobject->multi_query($sql);

        if ($mysqliobject->errno <> 0) {
            throw new Exception('Error making temp db in (' . __FILE__ . ' on line ' . __LINE__ . ')');
        }

        $mysqliobject->close();



        $config = $bootstrap->getResource('db')->getConfig();
        $runCommand = "mysql -h " . $config["host"] . " -P " . $config["port"] . " -u'" . $config["username"] . "' -p'" . $config["password"] . "' " . $config["dbname"] . " < " . $sqlFile;
        echo $runCommand;
        system($runCommand);
    }

    public function updateQuestionAction() {
// action body
//formlocal
        $multidb = Zend_Registry::get("multidb");
        $db = $multidb->getDb('db-quiz-de');
        $table = new Application_Model_DbTable_AriQuizQuestionVersion();


//formServer
//        $multidb = Zend_Registry::get("multidb");
//        $db = $multidb->getDb('s485825db2');
//        $table = new Zend_Db_Table('toefl_ariquizquestionversion');
//        
//        
//        
//        
        $where = array('QuestionVersionId' => 400);
        $result = $table->getQuestions($where, 'QuestionVersionId')->toArray();
//        var_dump($result);
//
        for ($i = 0; $i < count($result); $i++) {
            $no[$i] = str_pad($i + 1, 3, "0", STR_PAD_LEFT);
            $QuestionVersionId = $result[$i]['QuestionVersionId'];
            $question = $result[$i]['Question'];
            echo $QuestionVersionId . '--->' . $question;
//            $set = array(
//                'Question' => "<p>$no[$i]
//<audio controls='' autoplay='true'>
//  <source src='images/stories/toefl/$no[$i].mp3' type='audio/mpeg'>
//  <source src='images/stories/toefl/$no[$i].ogg' type='audio/ogg'>
//</audio></p>",
//            );
//            $tableName= 'quiz_ariquizquestionversion';
//
//            $where = $db->quoteInto('QuestionVersionId = ?', $QuestionVersionId);
//
//            $rows_affected = $db->update($tableName, $set, $where);
        }
    }

    public function compDbAction() {
// action body
//        function tablemap($subject) {
//        $func = function($subject) {
//            return(preg_replace('/^[^_]*_/', '', $subject));
//        };

//server
        $multidb = Zend_Registry::get("multidb");
        $db1 = $multidb->getDb('s485825db2');
        $db2 = $multidb->getDb('highschool31');
//        $sql = "show tables like 'toefl_%'";
        $sql = "show tables like '%\_ari%'";
        $result1 = $db1->fetchAll($sql);
//        $result1 = array_values(array_map($func2, $result1));
        var_dump($result1);
        $result2 = $db2->fetchAll($sql);
//        $result2 = array_map($func2, $result2);
        var_dump($result2);

        $result = array_diff($result1, $result2);
        var_dump($result);
//        foreach ($result as $value) {
//            $sql2 = "create table highschool31.hs_".$value." select * from s485825db2.quiz_".$value;
//            echo $sql2;
////            $db2->query($sql2);
//        }
////        
    }

    public function compTableAction() {
// action body
        $multidb = Zend_Registry::get("multidb");
        $db1 = $multidb->getDb('s485825db2');
        $db2 = $multidb->getDb('highschool31');
//        $sql = "show tables like 'toefl_%'";
        $sql = "show tables like '%\_ari%'";
        $result1 = $db1->fetchAll($sql);
//        var_dump($result1);
        $result2 = $db2->fetchAll($sql);
//        $result2 = array_map('_fetchArrValue', $result2);
//        var_dump($result2);
        for ($i = 0; $i < count($result1); $i++) {
            $sql31 = "SHOW COLUMNS FROM " . key(array_flip($result1[$i]));
            $sql32 = "SHOW COLUMNS FROM " . key(array_flip($result2[$i]));

//            echo $sql31.' no1<br/>';
//            echo $sql32.' no2<br/>';
//        var_dump(array_diff_key($db1->fetchAll($sql31), $db2->fetchAll($sql32)));
////            $table_alter = array_diff_key($db1->fetchAll($sql31), $db2->fetchAll($sql32));
            $table1[$i] = $db1->fetchAll($sql31);
            $table2[$i] = $db2->fetchAll($sql32);

            $table_alter = array_diff_assoc($table1[$i], $table2[$i]);
//            var_dump($table_alter);
            if (!!$table_alter) {
//                print_r($table_alter);
//                print_r($table1[$i]);
                echo '----------------------<br />';
                foreach ($table1[$i] as $key => $value1) {
//                print_r($value);
//                    echo $value['Field'];
                    $table1_col[$i][] = $value1['Field'];
                }
                foreach ($table2[$i] as $key => $value2) {
//                print_r($value);
//                    echo $value['Field'];
                    $table2_col[$i][] = $value2['Field'];
                }

                $col_alter = array_diff($table1_col[$i], $table2_col[$i]);

//                print_r($col_alter);
//                echo '<br /><br />' . $result1[$i] . '<br />';_
                foreach ($col_alter as $value) {
//                    var_dump($value);
//                    $col = $db2->query("SELECT " . $value['Field'] . " FROM " . $result2[$i]);
//                    $col = $db2->query("SHOW columns from `yourtable` where field='yourfield'");

                    $sql = "SHOW columns from " . key(array_flip($result1[$i])) . " where field='" . $value . "'";
//                    echo $sql.'<br />';
                    $col = $db1->fetchAll($sql);
//                    var_dump($col);
                    if ($col) {
                        foreach ($col as $colkey => $colvalue) {
                            var_dump($colvalue);
                            $sqlAlter = "ALTER TABLE `" . key(array_flip($result2[$i])) . "`  ADD  `" . $colvalue['Field'] . "` " . $colvalue['Type'];
                            echo $sqlAlter . '<br />';
                            $db2->query($sqlAlter);
                        }
                    }
                }
            }
        }
    }

    private function _fetchArrValue($subject) {
        return(key(array_flip($subject)));
    }

    public function copyTableAction() {
// action body
//server
                $func2 = function($subject) {
//                    return(key(array_flip($subject)));
                    return(preg_replace('/^[^\_]*_/', '', key(array_flip($subject))));
                };
                
        $multidb = Zend_Registry::get("multidb");
       $db1 = $multidb->getDb('s485825db2');
//        $db1 = $multidb->getDb('highschool31');
        // $db1 = $multidb->getDb('highschool1');
        $db2 = $multidb->getDb('j3');
//        $sql = "show tables like 'toefl_%'";
          $sql = "show tables like '%\_ari%'";
        $result1 = $db1->fetchAll($sql);
        $result = array_values(array_map($func2, $result1));
        var_dump($result);
        foreach ($result as $value) {
           $sql2 = "create table j3.jos_".$value." select * from s485825db2.quiz_".$value;
//            $sql2 = "create table highschool34.hs_".$value." select * from highschool31.hs_".$value;
            // $sql2 = "create table highschool35.hs_".$value." select * from highschool3.hs_".$value;
            echo $sql2;
            $db2->query($sql2);
        }
                
                
    }

    public function compColumnAction() {
// action body
        for ($i = 0; $i < count($result1); $i++) {
            $sql31 = "SHOW COLUMNS FROM " . $result1[$i];
            $sql32 = "SHOW COLUMNS FROM " . $result2[$i];
//        var_dump(array_diff_key($db1->fetchAll($sql31), $db2->fetchAll($sql32)));
////            $table_alter = array_diff_key($db1->fetchAll($sql31), $db2->fetchAll($sql32));
//            $table_alter = array_diff_assoc($db1->fetchAll($sql31), $db2->fetchAll($sql32));
//            var_dump($table_alter);
        }
    }

}

