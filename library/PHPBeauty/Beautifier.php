    <?php
    /* vim: set expandtab tabstop=4 shiftwidth=4: */
    // +----------------------------------------------------------------------+
    // | PHP version 5 |
    // +----------------------------------------------------------------------+
    // | Copyright (c) 1997-2004 The PHP Group |
    // +----------------------------------------------------------------------+
    // | This source file is subject to version 3.0 of the PHP license, |
    // | that is bundled with this package in the file LICENSE, and is |
    // | available through the world-wide-web at the following url: |
    // | http://www.php.net/license/3_0.txt. |
    // | If you did not receive a copy of the PHP license and are unable to |
    // | obtain it through the world-wide-web, please send a note to |
    // | license@php.net so we can mail you a copy immediately. |
    // +----------------------------------------------------------------------+
    // | Authors: Claudio Bustos <cdx@users.sourceforge.net> |
    // | Jens Bierkandt <schtorch@users.sourceforge.net> |
    // +----------------------------------------------------------------------+
    //
    // $Id:
    /**
    * Php Beautifier: Class to beautify php source code
    *
    * @package PHP_Beautifier
    * @author Claudio Bustos <cdx@users.sourceforge.net>
    * @author Jens Bierkandt <jens@bierkandt.org>
    */
    if (!defined("STDIN")) {
        define("STDIN", fopen('php://stdin', 'r'));
    }
    if (!defined("STDOUT")) {
        define("STDOUT", fopen('php://stdout', 'w'));
    }
    error_reporting(E_ALL);
    /**
    * Require PHP_Beautifier_Filter
    */
    include_once ('Beautifier/Filter.php');
    /**
    * Require PHP_Beautifier_Filter_Default
    */
    include_once ('Beautifier/Filter/Default.filter.php');
    /**
    * PHP_Beautifier
    *
    * Class to beautify php code
    * How to use:
    * # Create a instance of the object
    * # Define the input and output files
    * # Optional: Set one or more Filter. They are processed in LIFO order (last in, first out)
    * # Process the file
    * # Get it, save it or show it.
    *
    * <code>
    * $oToken = new PHP_Beautifier(); // create a instance
    * $oToken->addFilter('ArraySimple');
    * $oToken->addFilter('ListClassFunction'); // add one or more filters
    * $oToken->setInputFile(__FILE__); // nice... process the same file
    * $oToken->process(); // required
    * $oToken->show();
    * </code>
    * @todo create a web interface.
    * @package PHP_Beautifier
    */
    class PHP_Beautifier {
        // public
        /**
        * Tokens created by the tokenizer
        * @var array
        */
        public $aTokens = array();
        /**
        * Tokens codes assigned to method on Filter
        * @var array
        */
        public $aTokenFunctions = array();
        /**
        * Stores the output
        * @var array
        */
        public $aOut = array();
        /**
        * Contains the assigment of modes
        * @var array
        * @see setMode()
        * @see unsetMode()
        * @see getMode()
        */
        public $aModes = array();
        /**
        * Settings for the class
        * @var array
        */
        public $aSettings = array();
        /**
        * Index of current token
        * @var int
        */
        public $iCount = 0;
        /**
        * Level of indent
        * @var int
        */
        public $iIndent = 0;
        /**
        * Chars for indentation
        * @var int
        */
        public $iIndentNumber = 4;
        /**
        * Level of array nesting
        * @var int
        */
        public $iArray = 0;
        /**
        * Level of parenthesis nesting
        * @var int
        */
        public $iParenthesis = 0;
        /**
        * Level of verbosity (debug)
        * @var int
        */
        public $iVerbose = false;
        /**
        * Name of input file
        * @var string
        */
        public $sInputFile = '';
        /**
        * Name of output file
        * @var string
        */
        public $sOutputFile = '';
        /**
        * Type of newline
        * @var string
        */
        public $sNewLine = "\n";
        /**
        * Type of whitespace to use for indent
        * @var string
        */
        public $sIndentChar = ' ';
        /**
        * Save the last whitespace used. Use only for Filter! (i miss friends of C++ :( )
        * @var string
        */
        public $currentWhitespace = '';
        /**
        * Association $aTokens=>$aOut
        * @var string
        */
        public $aAssocs = array();
        // private
        /**
        Constant for last Control
        */
        private $iControlLast;
        /**
        References to PHP_Beautifier_Filter's
        */
        private $aFilters = array();
        /**
        Stack with control construct
        */
        private $aControlSeq = array();
        /**
        List of construct that start control structures
        */
        private $aControlStructures = array();
        /**
        * List of Control for parenthesis
        */
        private $aControlParenthesis = array();
        /**
        List of construct that end control structures
        */
        private $aControlStructuresEnd = array();
        /**
        Dirs for Filters
        */
        private $aFilterDirs = array();
        /**
        Flag for beautify/no beautify mode
        */
        private $bBeautify = true;
        // Methods
        /**
        * Constructor.
        * Assing values to {@link $aControlStructures},{@link $aControlStructuresEnd},
        * {@link $aTokenFunctions}
        */
        function __construct() 
        {
            $this->aControlStructures = array(
                T_IF,
                T_ELSE,
                T_ELSEIF,
                T_WHILE,
                T_DO,
                T_FOR,
                T_FOREACH,
                T_SWITCH,
                T_DECLARE,
                T_CASE,
                T_DEFAULT,
                T_TRY,
                T_CATCH
            );
            $this->aControlStructuresEnd = array(
                T_ENDWHILE,
                T_ENDFOREACH,
                T_ENDFOR,
                T_ENDDECLARE,
                T_ENDSWITCH,
                T_ENDIF
            );
            $this->aTokenFunctions = preg_grep('/^T_/', array_flip(array_filter(get_defined_constants() , 'is_integer')));
            $aTokensToChange = array(
                /**
                PUNCTUATION
                */
                '('=>'T_PARENTHESIS_OPEN',
                ')'=>'T_PARENTHESIS_CLOSE',
                ';'=>'T_SEMI_COLON',
                '{'=>'T_OPEN_BRACE',
                '}'=>'T_CLOSE_BRACE',
                ','=>'T_COMMA',
                '?'=>'T_QUESTION',
                ':'=>'T_COLON',
                '='=>'T_EQUAL_SIGN',
                /**
                INCLUDE *
                */
                T_INCLUDE=>'T_INCLUDE',
                T_INCLUDE_ONCE=>'T_INCLUDE',
                T_REQUIRE=>'T_INCLUDE',
                T_REQUIRE_ONCE=>'T_INCLUDE',
                /**
                LANGUAGE CONSTRUCT
                */
                T_FUNCTION=>'T_LANGUAGE_CONSTRUCT',
                T_PRINT=>'T_LANGUAGE_CONSTRUCT',
                T_EVAL=>'T_LANGUAGE_CONSTRUCT',
                T_RETURN=>'T_LANGUAGE_CONSTRUCT',
                T_ECHO=>'T_LANGUAGE_CONSTRUCT',
                T_NEW=>'T_LANGUAGE_CONSTRUCT',
                T_EXIT=>'T_LANGUAGE_CONSTRUCT',
                T_CLASS=>'T_LANGUAGE_CONSTRUCT',
                T_VAR=>'T_LANGUAGE_CONSTRUCT',
                T_GLOBAL=>'T_LANGUAGE_CONSTRUCT',
                T_STATIC=>'T_LANGUAGE_CONSTRUCT',
                /**
                CONTROL
                */
                T_IF=>'T_CONTROL',
                T_DO=>'T_CONTROL',
                T_WHILE=>'T_CONTROL',
                T_SWITCH=>'T_CONTROL',
                T_CASE=>'T_CONTROL',
                /**
                ELSE
                */
                T_ELSEIF=>'T_ELSE',
                T_ELSE=>'T_ELSE',
                /**
                ACCESS
                */
                T_FINAL=>'T_ACCESS',
                T_ABSTRACT=>'T_ACCESS',
                T_PRIVATE=>'T_ACCESS',
                T_PUBLIC=>'T_ACCESS',
                T_PROTECTED=>'T_ACCESS',
                T_CONST=>'T_ACCESS',
                /**
                COMPARATORS
                */
                T_PLUS_EQUAL=>'T_ASSIGMENT',
                T_MINUS_EQUAL=>'T_ASSIGMENT',
                T_MUL_EQUAL=>'T_ASSIGMENT',
                T_DIV_EQUAL=>'T_ASSIGMENT',
                T_CONCAT_EQUAL=>'T_ASSIGMENT',
                T_MOD_EQUAL=>'T_ASSIGMENT',
                T_AND_EQUAL=>'T_ASSIGMENT',
                T_OR_EQUAL=>'T_ASSIGMENT',
                T_XOR_EQUAL=>'T_ASSIGMENT',
                T_SL_EQUAL=>'T_EQUAL',
                T_SR_EQUAL=>'T_EQUAL',
                T_IS_EQUAL=>'T_EQUAL',
                T_IS_NOT_EQUAL=>'T_EQUAL',
                T_IS_IDENTICAL=>'T_EQUAL',
                T_IS_NOT_IDENTICAL=>'T_EQUAL',
                T_IS_SMALLER_OR_EQUAL=>'T_EQUAL',
                T_IS_GREATER_OR_EQUAL=>'T_EQUAL',
                /* LOGICAL*/
                T_LOGICAL_OR=>'T_LOGICAL',
                T_LOGICAL_XOR=>'T_LOGICAL',
                T_LOGICAL_AND=>'T_LOGICAL',
                T_BOOLEAN_OR=>'T_LOGICAL',
                T_BOOLEAN_AND=>'T_LOGICAL',
                /**
                SUFIX END *
                */
                T_ENDWHILE=>'T_END_SUFFIX',
                T_ENDFOREACH=>'T_END_SUFFIX',
                T_ENDFOR=>'T_END_SUFFIX',
                T_ENDDECLARE=>'T_END_SUFFIX',
                T_ENDSWITCH=>'T_END_SUFFIX',
                T_ENDIF=>'T_END_SUFFIX',
            );
            foreach($aTokensToChange as $iToken=>$sFunction) {
                $this->aTokenFunctions[$iToken] = $sFunction;
            }
            $this->addFilterDirectory(dirname(__FILE__) .'/Beautifier/Filter');
            $this->addFilter('Default');
        }
        /**
        * Add a filter directory
        * @param string path to directory
        * @throw Exception
        */
        public function addFilterDirectory($sDir) 
        {
            $sDir = $this->normalizeDir($sDir);
            if (file_exists($sDir)) {
                array_push($this->aFilterDirs, $sDir);
            } else {
                throw(new Exception("Path '$sDir' doesn't exists"));
            }
        }
        /**
        * Return an array with all the Filter Dirs
        * @return array List of Filter Directories
        */
        public function getFilterDirectories() 
        {
            return $this->aFilterDirs;
        }
        /**
        * Normalize reference to directories
        * @param string path to directory
        * @return string normalized path to directory
        */
        public function normalizeDir($sDir) 
        {
            $sDir = str_replace(DIRECTORY_SEPARATOR, '/', $sDir);
            if (substr($sDir, -1) != '/') {
                $sDir.= '/';
            }
            return $sDir;
        }
        /**
        * Add a Filter to the Beautifier
        * The first argument is the name of the file of the Filter.
        * @tutorial PHP_Beautifier/Filter/Filter.pkg#use
        * @param string name of the Filter
        * @param array settings for the Filter
        * @return bool true if Filter is loaded, false if the same filter was loaded previously
        * @throw Exception
        */
        public function addFilter($sFilter, $aSettings = array()) 
        {
            $sFilterClass = 'PHP_Beautifier_Filter_'.$sFilter;
            if (!class_exists($sFilterClass)) {
                $this->addFilterFile($sFilter);
            }
            $oTemp = new $sFilterClass($this, $aSettings);
            // verify if same Filter is loaded
            if (in_array($oTemp, $this->aFilters, TRUE)) {
                return false;
            } elseif ($oTemp instanceof PHP_Beautifier_Filter) {
                array_unshift($this->aFilters, $oTemp);
                return true;
            } else {
                throw(new Exception("'$sFilterClass' isn't a subclass of 'Filter'"));
            }
        }
        /**
        * Return the Filter Description
        * @see PHP_Beautifier_Filter::__toString();
        * @param string name of the filter
        * @return mixed string or false
        */
        public function getFilterDescription($sFilter) 
        {
            $aFilters = $this->getFilterListTotal();
            if (in_array($sFilter, $aFilters)) {
                $this->addFilterFile($sFilter);
                $sFilterClass = 'PHP_Beautifier_Filter_'.$sFilter;
                $oTemp = new $sFilterClass($this, array());
                return $oTemp;
            } else {
                return false;
            }
        }
        /**
        * Add a new filter to the processor.
        * The system will process the filter in LIFO order
        * @param string name of filter
        * @see process()
        * @return bool
        * @throw Exception
        */
        private function addFilterFile($sFilter) 
        {
            $sFilterClass = 'PHP_Beautifier_Filter_'.$sFilter;
            foreach($this->aFilterDirs as $sDir) {
                $sFile = $sDir.$sFilter.'.filter.php';
                if (file_exists($sFile)) {
                    include_once ($sFile);
                    if (class_exists($sFilterClass)) {
                        return true;
                    } else {
                        throw(new Exception("File '$sFile' exists,but doesn't exists filter '$sFilterClass'"));
                    }
                }
            }
            throw(new Exception("Doesn't exists filter '$sFilter'"));
        }
        /**
        * Get the names of the loaded filters
        * @return array list of Filters
        */
        public function getFilterList() 
        {
            foreach($this->aFilters as $oFilter) {
                $aOut[] = $oFilter->getName();
            }
            return $aOut;
        }
        /**
        * Get the list of all available Filters in all the include Dirs
        * @return array list of Filters
        */
        public function getFilterListTotal() 
        {
            $aFilterFiles = array();
            foreach($this->aFilterDirs as $sDir) {
                $aFiles = $this->getFilesByPattern($sDir, ".*?\.filter\.php");
                array_walk($aFiles, array(
                    $this,
                    'getFilterList_FilterName'
                ));
                $aFilterFiles = array_merge($aFilterFiles, $aFiles);
            }
            sort($aFilterFiles);
            return $aFilterFiles;
        }
        /**
        * Receive a path to a filter and replace it with the name of filter
        */
        private function getFilterList_FilterName(&$sFile) 
        {
            preg_match("/\/([^\/]*?)\.filter\.php/", $sFile, $aMatch);
            $sFile = $aMatch[1];
        }
        /**
        * Search, inside a dir, for a file pattern, using regular expresion
        * Example:
        *
        * <code>$this->getFilesByPattern('.','*.php',true);</code>
        * Search recursively for all the files with php extensions
        * in the current dir
        * @param string path to a dir
        * @param string file pattern
        * @param bool recursive?
        * @return array path to files
        */
        public function getFilesByPattern($sDir, $sFilePattern, $bRecursive = false) 
        {
            if (substr($sDir, -1) == '/') {
                $sDir = substr($sDir, 0, -1);
            }
            $dh = @opendir($sDir);
            if (!$dh) {
                throw(new Exception("Cannot open directory '$sDir'"));
            }
            $matches = array();
            while ($entry = @readdir($dh)) {
                if ($entry == '.' or $entry == '..') {
                    continue;
                } elseif (is_dir($sDir.'/'.$entry) and $bRecursive) {
                    $matches = array_merge($matches, $this->getFilesByPattern($sDir.'/'.$entry, $sFilePattern, $bRecursive));
                } elseif (preg_match("/".$sFilePattern."$/", $entry)) {
                    $matches[] = $sDir."/".$entry;
                }
            }
            return $matches;
        }
        /**
        * Character used for indentation
        * @param string usually ' ' or "\t"
        */
        public function setIndentChar($sChar) 
        {
            $this->sIndentChar = $sChar;
        }
        /**
        * Number of characters for indentation
        * @param int ussualy 4 for space or 1 for tabs
        */
        public function setIndentNumber($iIndentNumber) 
        {
            $this->iIndentNumber = $iIndentNumber;
        }
        /**
        * Character used as a new line
        * @param string ussualy "\n", "\r\n" or "\r"
        */
        public function setNewLine($sNewLine) 
        {
            $this->sNewLine = $sNewLine;
        }
        /**
        * Set the file for beautify
        * @param string path to file
        * @throw Exception
        */
        public function setInputFile($sFile) 
        {
            if ($sFile != STDIN and !file_exists($sFile)) {
                throw(new Exception("File '$sFile' doesn't exists"));
            }
            $this->sText = '';
            $this->sInputFile = $sFile;
            $fp = ($sFile == STDIN) ? STDIN : fopen($sFile, 'r');
            while (!feof($fp)) {
                $this->sText.= fgets($fp, 4096);
            }
            if ($fp != STDIN) {
                fclose($fp);
            }
            return true;
        }
        /**
        * Set the output file for beautify
        * @param string path to file
        */
        public function setOutputFile($sFile) 
        {
            $this->sOutputFile = $sFile;
        }
        /**
        * Save the beautified code to output file
        * @param string path to file. If null, {@link $sOutputFile} if exists, throw exception otherwise
        * @see setOutputFile();
        * @throw Exception
        */
        public function save($sFile = null) 
        {
            if (!$sFile) {
                if (!$this->sOutputFile) {
                    throw(new Exception("Can't save without a output file"));
                } else {
                    $sFile = $this->sOutputFile;
                }
            }
            $sText = $this->get();
            $fp = ($sFile == STDOUT) ? STDOUT : fopen($sFile, "w");
            if (!$fp) {
                throw(new Exception("Can't save file $sFile"));
            }
            fputs($fp, $sText, strlen($sText));
            if ($sFile != STDOUT) {
                fclose($fp);
            }
        }
        /**
        * Set a string for beautify
        * @param string Must be preceded by open tag
        */
        public function setString($sText) 
        {
            $this->sText = $sText;
        }
        /**
        * Reset all properties
        */
        private function resetProperties() 
        {
            $aProperties = array(
                'aTokens'=>array() ,
                'aOut'=>array() ,
                'aModes'=>array() ,
                'iCount'=>0,
                'iIndent'=>0,
                'iArray'=>0,
                'iParenthesis'=>0,
                'currentWhitespace'=>'',
                'aAssocs'=>array() ,
                'iControlLast'=>null,
                'aControlSeq'=>array() ,
                'bBeautify'=>true
            );
            foreach($aProperties as $sProperty=>$sValue) {
                $this->$sProperty = $sValue;
            }
        }
        /**
        * Process the string or file to beautify
        * @return bool true on success
        * @throw Exception
        */
        public function process() 
        {
            $this->resetProperties();
            $this->aTokens = token_get_all($this->sText);
            $this->aOut = array();
            $iTotal = count($this->aTokens);
            $iPrevAssoc = false;
            for ($this->iCount = 0;$this->iCount<$iTotal;$this->iCount++) {
                $aCurrentToken = $this->aTokens[$this->iCount];
                if (is_string($aCurrentToken)) {
                    $aCurrentToken = array(
                        0=>$aCurrentToken,
                        1=>$aCurrentToken
                    );
                }
                $this->controlToken($aCurrentToken);
                $iFirstOut = count($this->aOut); //5
                $bError = false;
                if ($this->bBeautify) {
                    foreach($this->aFilters as $oFilter) {
                        $bError = true;
                        if ($oFilter->handleToken($aCurrentToken) !== FALSE) {
                            $bError = false;
                            break;
                        }
                    }
                } else {
                    $this->add($aCurrentToken[1]);
                }
                $this->controlTokenPost($aCurrentToken);
                $iLastOut = count($this->aOut);
                // set the assoc
                if (($iLastOut-$iFirstOut) >0) {
                    $this->aAssocs[$this->iCount] = array(
                        'offset'=>$iFirstOut
                    );
                    if ($iPrevAssoc !== FALSE) {
                        $this->aAssocs[$iPrevAssoc]['length'] = $iFirstOut-$this->aAssocs[$iPrevAssoc]['offset'];
                    }
                    $iPrevAssoc = $this->iCount;
                }
                if ($bError) {
                    throw(new Exception("Can'process token: ".var_dump($aCurrentToken)));
                }
            } // ~for
            // generate the last assoc
            $this->aAssocs[$iPrevAssoc]['length'] = (count($this->aOut) -1) -$this->aAssocs[$iPrevAssoc]['offset'];
            // Post-processing
            foreach($this->aFilters as $oFilter) {
                $oFilter->postProcess();
            }
            return true;
        }
        /**
        * Get the reference to {@link $aOut}, based on the number of the token
        * @param int token number
        * @return mixed false array or false if token doesn't exists
        */
        public function getTokenAssoc($iIndex) 
        {
            return (array_key_exists($iIndex, $this->aAssocs)) ? $this->aAssocs[$iIndex] : false;
        }
        /**
        * Get the output for specified token
        * @param int token number
        * @return mixed string or false if token doesn't exists
        */
        public function getTokenAssocText($iIndex) 
        {
            if (!($aAssoc = $this->getTokenAssoc($iIndex))) {
                return false;
            }
            return (implode('', array_slice($this->aOut, $aAssoc['offset'], $aAssoc['length'])));
        }
        /**
        * Replace the output for specified token
        * @param int token number
        * @param string replace text
        * @return bool
        */
        public function replaceTokenAssoc($iIndex, $sText) 
        {
            if (!($aAssoc = $this->getTokenAssoc($iIndex))) {
                return false;
            }
            $this->aOut[$aAssoc['offset']] = $sText;
            for ($x = 0;$x<$aAssoc['length']-1;$x++) {
                $this->aOut[$aAssoc['offset']+$x+1] = '';
            }
            return true;
        }
        /**
        * Return the function for a token constant or string.
        * @param mixed token constant or string
        * @return mixed name of function or false
        */
        public function getTokenFunction($sTokenType) 
        {
            return (array_key_exists($sTokenType, $this->aTokenFunctions)) ? strtolower($this->aTokenFunctions[$sTokenType]) : false;
        }
        /**
        * Assign value for some variables with the information of the token
        * @param array current token
        */
        private function controlToken($aCurrentToken) 
        {
            // is a control structure opener?
            if (in_array($aCurrentToken[0], $this->aControlStructures)) {
                $this->pushControlSeq($aCurrentToken);
                $this->iControlLast = $aCurrentToken[0];
            }
            // is a control structure closer?
            if (in_array($aCurrentToken[0], $this->aControlStructuresEnd)) {
                $this->popControlSeq();
            }
            switch ($aCurrentToken[0]) {
                case T_COMMENT:
                    // callback?
                    if (preg_match("/\/\/\s*(.*?)->((.*)\((.*)\))/", $aCurrentToken[1], $aMatch)) {
                        try {
                            if (stristr('php_beautifier', $aMatch[1]) and method_exists($this, $aMatch[3])) {
                                eval ('$this->'.$aMatch[2].";");
                            } else {
                                foreach($this->aFilters as $iIndex=>$oFilter) {
                                    if (strtolower(get_class($oFilter)) == 'php_beautifier_filter_'.strtolower($aMatch[1]) and method_exists($oFilter, $aMatch[3])) {
                                        eval ('$this->aFilters['.$iIndex.']->'.$aMatch[2].";");
                                        break;
                                    }
                                }
                            }
                        }
                        catch(Exception $oExp) {
                        }
                    }
                break;

                case T_FUNCTION:
                    $this->setMode('function');
                break;

                case T_ARRAY:
                    $this->iArray++;
                break;

                case T_WHITESPACE:
                    $this->currentWhitespace = $aCurrentToken[1];
                break;

                case '{':
                    if ($this->getPreviousTokenConstant() == T_VARIABLE or ($this->getPreviousTokenConstant() == T_STRING and $this->getPreviousTokenConstant(2) == T_OBJECT_OPERATOR)) {
                        $this->setMode('string_index');
                    }
                break;

                case '}':
                    if ($this->iVerbose) {
                        echo 'end bracket:'.$this->getPreviousTokenContent() ."\n";
                    }
                    if ($this->getPreviousTokenContent() == ';' or $this->getPreviousTokenContent() == '}' or $this->getPreviousTokenContent() == '{') {
                        $this->popControlSeq();
                    }
                break;

                case '(':
                    $this->iParenthesis++;
                    $this->pushControlParenthesis();
                break;

                case ')':
                    $this->iParenthesis--;
                break;

                case '?':
                    $this->setMode('ternary_operator');
                break;
            }
            if ($this->getTokenFunction($aCurrentToken[0]) == 't_include') {
                $this->setMode('include');
            }
        }
        /**
        * Assign value for some variables with the information of the token, after processing
        * @param array current token
        */
        private function controlTokenPost($aCurrentToken) 
        {
            switch ($aCurrentToken[0]) {
                case ')':
                    if ($this->iArray) {
                        $this->iArray--;
                    }
                    $this->popControlParenthesis();
                break;

                case '}':
                    if ($this->getMode('string_index')) {
                        $this->unsetMode('string_index');
                    } else {
                        $bLast = $this->getControlSeq();
                    }
                break;

                case '{':
                    $this->unsetMode('function');
                break;

                case T_BREAK:
                    if ($this->getControlSeq() == T_CASE) {
                        for ($i = count($this->aControlSeq) -1;$i >= 0;$i--) {
                            if ($this->getControlSeq() != T_CASE) {
                                break;
                            } else {
                                $this->popControlSeq();
                            }
                        }
                    } elseif ($this->getControlSeq() == T_DEFAULT) {
                        $this->popControlSeq();
                    }
                break;
            }
            if ($this->getTokenFunction($aCurrentToken[0]) == 't_colon') {
                $this->unsetMode('ternary_operator');
            }
        }
        /**
        * Push a control construct to the stack
        * @param array current token
        */
        private function pushControlSeq($aToken) 
        {
            if ($this->iVerbose>0) {
                echo 'Push:'.$aToken[0]."->".$aToken[1]."\n";
            }
            array_push($this->aControlSeq, $aToken[0]);
        }
        /**
        * Pop a control construct from the stack
        * @return int token constant
        */
        private function popControlSeq() 
        {
            $aEl = array_pop($this->aControlSeq);
            if ($this->iVerbose>0) {
                echo 'Pop:'.$aEl."\n";
            }
            return $aEl;
        }
        /**
        * Push a new Control Instruction on the stack
        */
        private function pushControlParenthesis() 
        {
            $iPrevious = $this->getPreviousTokenConstant();
            if ($this->iVerbose>0) {
                echo "Push Parenthesis: $iPrevious ->".$this->getPreviousTokenContent() ."\n";
            }
            array_push($this->aControlParenthesis, $iPrevious);
        }
        /**
        * Pop the last Control instruction for parenthesis from the stack
        * @return int constant
        */
        private function popControlParenthesis() 
        {
            $iPop = array_pop($this->aControlParenthesis);
            if ($this->iVerbose>0) {
                echo 'Pop Parenthesis:'.$iPop."\n";
            }
            return $iPop;
        }
        /**
        * Set the Beautifier on or off
        * @param bool
        */
        public function setBeautify($sFlag) 
        {
            $this->bBeautify = (bool)$sFlag;
        }
        /**
        * Show the beautified code
        */
        public function show() 
        {
            echo $this->get();
        }
        /**
        * Returns the beutified code
        * @return string
        */
        public function get() 
        {
            return implode('', $this->aOut);
        }
        /**
        * Returns the value of a settings
        * @param string Name of the setting
        * @return mixed Value of the settings or false
        */
        public function getSetting($sKey) 
        {
            return (array_key_exists($sKey, $this->aSettings)) ? $this->aSettings[$sKey] : false;
        }
        /**
        * Get the token constant for the current control construct
        * @param int current token -'x'
        *@@ return mixed token constant or false
        */
        public function getControlSeq($iRet = 0) 
        {
            $iIndex = count($this->aControlSeq) -$iRet-1;
            return ($iIndex >= 0) ? $this->aControlSeq[$iIndex] : false;
        }
        /**
        * Get the token constant for the current Parenthesis
        * @param int current token -'x'
        * @return mixed token constant or false
        */
        public function getControlParenthesis($iRet = 0) 
        {
            $iIndex = count($this->aControlParenthesis) -$iRet-1;
            return ($iIndex >= 0) ? $this->aControlParenthesis[$iIndex] : false;
        }
        ////
        // Mode methods
        ////
        /**
        * Set a mode to true
        * @param string name of the mode
        */
        public function setMode($sKey) 
        {
            $this->aModes[$sKey] = true;
        }
        /**
        * Set a mode to false
        * @param string name of the mode
        */
        public function unsetMode($sKey) 
        {
            $this->aModes[$sKey] = false;
        }
        /**
        * Get the state of a mode
        * @param string name of the mode
        * @return bool
        */
        public function getMode($sKey) 
        {
            return array_key_exists($sKey, $this->aModes) ? $this->aModes[$sKey] : false;
        }
        /////
        // Filter methods
        /////
        /**
        * Add a string to the output
        * @param string
        */
        public function add($token) 
        {
            $this->aOut[] = $token;
        }
        /**
        * Delete the last added outputs
        * @param int number of outputs to drop
        * @deprecated
        */
        public function pop($iReps = 1) 
        {
            for ($x = 0;$x<$iReps;$x++) {
                $sLast = array_pop($this->aOut);
            }
            return $sLast;
        }
        /**
        * Add a new line to the output
        */
        public function addNewLine() 
        {
            $this->aOut[] = $this->sNewLine;
        }
        /**
        * Add Indent to the output
        * @see $sIndentChar
        * @see $iIndentNumber
        * @see $iIndent
        */
        public function addIndent() 
        {
            if ($this->iIndent<1) {
                $this->iIndent = 1;
            }
            $this->aOut[] = str_repeat($this->sIndentChar, ($this->iIndent) *$this->iIndentNumber);
        }
        //
        ////
        // Methods to lookup previous, next tokens
        ////
        //
        /**
        * Get the 'x' significant (non whiteline)previous token
        * @param int current-x token
        * @return mixed array or false
        */
        private function getPreviousToken($iPrev = 1) 
        {
            for ($x = $this->iCount-1;$x >= 0;$x--) {
                $aToken = &$this->getToken($x);
                if ($aToken[0] != T_WHITESPACE) {
                    $iPrev--;
                    if (!$iPrev) {
                        return $aToken;
                    }
                }
            }
        }
        /**
        * Get the 'x' significant (non whiteline) next token
        * @param int current+x token
        * @return array
        */
        private function getNextToken($iNext = 1) 
        {
            for ($x = $this->iCount+1;$x<(count($this->aTokens) -1);$x++) {
                $aToken = &$this->getToken($x);
                if ($aToken[0] != T_WHITESPACE) {
                    $iNext--;
                    if (!$iNext) {
                        return $aToken;
                    }
                }
            }
        }
        /**
        * Return true if any of the constant defined is param 1 is the previous 'x' constant
        * @param mixed int (constant) or array
        * @return bool
        */
        public function isPreviousTokenConstant($mValue, $iPrev = 1) 
        {
            if (!is_array($mValue)) {
                $mValue = array(
                    $mValue
                );
            }
            $iPrevious = $this->getPreviousTokenConstant($iPrev);
            return in_array($iPrevious, $mValue);
        }
        /**
        * Return true if any of the content defined is param 1 is the previous 'x' content
        * @param mixed string (content) or array
        * @return bool
        */
        public function isPreviousTokenContent($mValue, $iPrev = 1) 
        {
            if (!is_array($mValue)) {
                $mValue = array(
                    $mValue
                );
            }
            $iPrevious = $this->getPreviousTokenContent($iPrev);
            return in_array($iPrevious, $mValue);
        }
        /**
        * Return true if any of the constant defined is param 1 is the next 'x' content
        * @param mixed int (constant) or array
        * @return bool
        */
        public function isNextTokenConstant($mValue, $iPrev = 1) 
        {
            if (!is_array($mValue)) {
                $mValue = array(
                    $mValue
                );
            }
            $iNext = $this->getNextTokenConstant($iPrev);
            return in_array($iNext, $mValue);
        }
        /**
        * Return true if any of the content defined is param 1 is the next 'x' content
        * @param mixed string (content) or array
        * @return bool
        */
        public function isNextTokenContent($mValue, $iPrev = 1) 
        {
            if (!is_array($mValue)) {
                $mValue = array(
                    $mValue
                );
            }
            $iNext = $this->getNextTokenContent($iPrev);
            return in_array($iNext, $mValue);
        }
        /**
        * Get the 'x' significant (non whiteline) previous token constant
        * @param int current-x token
        * @return int
        */
        public function getPreviousTokenConstant($iPrev = 1) 
        {
            $sToken = $this->getPreviousToken($iPrev);
            return $sToken[0];
        }
        /**
        * Get the 'x' significant (non whiteline) previous token text
        * @param int current-x token
        * @return string
        */
        public function getPreviousTokenContent($iPrev = 1) 
        {
            $mToken = $this->getPreviousToken($iPrev);
            return (is_string($mToken)) ? $mToken : $mToken[1];
        }
        /**
        * Get the 'x' significant (non whiteline) next token constant
        * @param int current+x token
        * @return int
        */
        public function getNextTokenConstant($iPrev = 1) 
        {
            $sToken = $this->getNextToken($iPrev);
            return $sToken[0];
        }
        /**
        * Get the 'x' significant (non whiteline) next token text
        * @param int current+x token
        * @return int
        */
        public function getNextTokenContent($iNext = 1) 
        {
            $mToken = $this->getNextToken($iNext);
            return (is_string($mToken)) ? $mToken : $mToken[1];
        }
        /**
        * Remove all whitespace from the previous tag
        */
        function removeWhitespace() 
        {
            for ($i = count($this->aOut) -1;$i >= 0;$i--) {
                if (strlen(trim($this->aOut[$i])) == 0) {
                    array_pop($this->aOut);
                } else {
                    $this->aOut[$i] = rtrim($this->aOut[$i]);
                    break;
                }
            }
        }
        /**
        * Get a token by number
        * @param int number of the token
        * @return array
        */
        public function &getToken($iIndex) 
        {
            return $this->aTokens[$iIndex];
        }
    }
?>

