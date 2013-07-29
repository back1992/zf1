
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
    * @package PHP_Beautifier
    * @subpackage Filter
    */
    /**
    * Default Filter
    * This filters is loaded by default in {@link PHP_Beautifier}. Can handle all the tokens.
    * If one of the tokens doesn't have a function, is added wihout modification (See {@link __call()})
    * The most important modifications are:
    * - All the statements inside control structures are indented
    * - The function and class defitions have this structure
    * <CODE>
    * function myFunction() {
    * echo 'hi';
    * }
    * </CODE>
    * - All the comments in new lines are indented. In multi-line comments, all the lines are indented, too.
    * This class is final, so don't try to extend it!
    * @package PHP_Beautifier
    * @subpackage Filter
    */
    final class PHP_Beautifier_Filter_Default extends PHP_Beautifier_Filter {
        function __call($sMethod, $aArgs) 
        {
            if (!is_array($aArgs) or count($aArgs) != 1) {
                throw(new Exception('Llamada a Filter::__call con argumento erroneo'));
            }
            $this->oBeaut->add($aArgs[0]);
        }
        // Bypass the function!
        public function off() 
        {
        }
        public function t_access($sTag) 
        {
            $this->oBeaut->add($sTag.' ');
        }
        public function t_open_tag($sTag) 
        {
            $this->oBeaut->add(trim($sTag));
            $this->oBeaut->iIndent = 1;
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
        }
        function t_close_tag($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->addNewLine();
            $this->oBeaut->add($sTag);
        }
        function t_switch($sTag) 
        {
            $this->t_control($sTag);
        }
        function t_control($sTag) 
        {
            $this->oBeaut->add($sTag.' ');
        }
        function t_parenthesis_open($sTag) 
        {
            $this->oBeaut->add($sTag);
        }
        function t_parenthesis_close($sTag) 
        {
            if ($this->oBeaut->getPreviousTokenConstant() != T_COMMENT) {
                $this->oBeaut->removeWhitespace();
            }
            $this->oBeaut->add($sTag.' ');
        }
        function t_open_brace($sTag) 
        {
            if ($this->oBeaut->getPreviousTokenConstant() == T_VARIABLE or ($this->oBeaut->getPreviousTokenConstant() == T_STRING and $this->oBeaut->getPreviousTokenConstant(2) == T_OBJECT_OPERATOR)) {
                $this->add($sTag);
            } else {
                $this->oBeaut->removeWhiteSpace();
                $this->oBeaut->add(' '.$sTag);
                $this->oBeaut->addNewLine();
                $this->oBeaut->iIndent++;
                $this->oBeaut->addIndent();
            }
        }
        function t_close_brace($sTag) 
        {
            if ($this->oBeaut->getMode('string_index')) {
                $this->oBeaut->add($sTag);
            } else {
                $this->oBeaut->removeWhitespace();
                $this->oBeaut->iIndent--;
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
                $this->oBeaut->add($sTag);
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
            }
        }
        function t_semi_colon($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add($sTag);
            if ($this->oBeaut->getControlParenthesis() != T_FOR) {
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
            }
        }
        function t_as($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_new($sTag) 
        {
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_whitespace($sTag) 
        {
        }
        function t_doc_comment($sTag) 
        {
            $this->oBeaut->removeWhiteSpace();
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
            // process doc
            preg_match("/(\/\*\*)(.*?)(\*\/)/sm", $sTag, $aMatch);
            $sDoc = $aMatch[2];
            $aLines = preg_split("/\r\n|\r|\n/", $sDoc);
            $this->oBeaut->add($aMatch[1]);
            foreach($aLines as $sLine) {
                if ($sLine = trim($sLine)) {
                    $this->oBeaut->addNewLine();
                    $this->oBeaut->addIndent();
                    $this->oBeaut->add($sLine);
                }
            }
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
            $this->oBeaut->add($aMatch[3]);
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
        }
        function t_comment($sTag) 
        {
            if (!preg_match("/\r|\n/", $this->oBeaut->currentWhitespace) and !$this->oBeaut->isPreviousTokenConstant(T_COMMENT) and !$this->oBeaut->isPreviousTokenConstant(T_OPEN_TAG)) {
                $this->oBeaut->removeWhitespace();
                $this->oBeaut->add($this->oBeaut->currentWhitespace);
            }
            if (substr($sTag, 0, 2) == '/*') { // Comentario largo
                $this->comment_large($sTag);
            } else { // comentario corto
                $this->comment_short($sTag);
            }
        }
        function comment_short($sTag) 
        {
            $this->oBeaut->add(trim($sTag));
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
        }
        function comment_large($sTag) 
        {
            if ($sTag == '/*{{{*/' or $sTag == '/*}}}*/') { // folding markers
                $this->oBeaut->add(' '.$sTag);
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
            } else {
                $aLines = explode("\n", $sTag);
                foreach($aLines as $sLinea) {
                    $this->oBeaut->add(trim($sLinea));
                    $this->oBeaut->addNewLine();
                    $this->oBeaut->addIndent();
                }
            }
        }
        function t_else($sTag) 
        {
            if ($this->oBeaut->getPreviousTokenContent() == '}') {
                $this->oBeaut->removeWhitespace();
                $this->oBeaut->add(' ');
            } else {
                $this->oBeaut->removeWhitespace();
                $this->oBeaut->iIndent--;
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
            }
            $this->oBeaut->add($sTag.' ');
        }
        function t_equal($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_logical($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_for($sTag) 
        {
            $this->oBeaut->add($sTag.' ');
        }
        function t_comma($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add($sTag.' ');
        }
        function t_include($sTag) 
        {
            $this->oBeaut->add($sTag.' ');
        }
        function t_language_construct($sTag) 
        {
            $this->oBeaut->add($sTag.' ');
        }
        function t_constant_encapsed_string($sTag) 
        {
            $this->oBeaut->add($sTag);
        }
        function t_variable($sTag) 
        {
            if ($this->oBeaut->isPreviousTokenConstant(T_STRING)) {
                $this->oBeaut->add(' ');
            }
            $this->oBeaut->add($sTag);
        }
        function t_question($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_colon($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            if ($this->oBeaut->getMode('ternary_operator')) {
                $this->oBeaut->add(' '.$sTag.' ');
            } else {
                $this->oBeaut->add($sTag);
                if (!$this->oBeaut->isNextTokenConstant(T_CASE)) {
                    $this->oBeaut->iIndent++;
                }
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
            }
        }
        function t_double_colon($sTag) 
        {
            $this->oBeaut->add($sTag);
        }
        function t_break($sTag) 
        {
            if ($this->oBeaut->getControlSeq() == T_CASE or $this->oBeaut->getControlSeq() == T_DEFAULT) {
                $this->oBeaut->removeWhitespace();
                $this->oBeaut->iIndent--;
                $this->oBeaut->addNewLine();
                $this->oBeaut->addIndent();
                $this->oBeaut->add($sTag);
            } else {
                $this->oBeaut->add($sTag);
            }
        }
        function t_default($sTag) 
        {
            $this->oBeaut->add($sTag);
        }
        function t_end_suffix($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->iIndent--;
            $this->oBeaut->addNewLine();
            $this->oBeaut->addIndent();
            $this->oBeaut->add($sTag);
        }
        function t_extends($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_instanceof($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_equal_sign($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add(' '.$sTag.' ');
        }
        function t_assigment($sTag) 
        {
            $this->oBeaut->removeWhitespace();
            $this->oBeaut->add($sTag.' ');
        }
        function t_array($sTag) 
        {
            $this->oBeaut->add($sTag);
        }
    }
?>

