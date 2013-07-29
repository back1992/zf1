<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initView() {
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('my project');
        return $view;
    }

    protected function _initNavigation() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }

    protected function _initDb() {
        $resource = $this->getPluginResource('multidb');
        Zend_Registry::set("multidb", $resource);
    }

    // protected function _initViewHelpers()
    // {
    //     $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
    //     $view->jQuery()->addStylesheet('/js/jquery/css/ui-lightness/jquery-ui-1.7.2.custom.css')
    //     ->setLocalPath('/js/jquery/js/jquery-1.3.2.min.js')
    //     ->setUiLocalPath('/js/jquery/js/jquery-ui-1.7.2.custom.min.js');
    // }

}

