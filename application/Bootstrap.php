<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function init()
    {
        Zend_Dojo::enableView($this->view);
    }
    /**
     * init jquery view helper, enable jquery, jqueryui, jquery ui css
     */

    protected function _initJquery() {

        $this->bootstrap('view');
        $view = $this->getResource('view'); //get the view object

        //add the jquery view helper path into your project
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        //jquery lib includes here (default loads from google CDN)
        $view->jQuery()->enable()//enable jquery ; ->setCdnSsl(true) if need to load from ssl location
            ->setVersion('1.5')//jQuery version, automatically 1.5 = 1.5.latest
            ->setUiVersion('1.8')//jQuery UI version, automatically 1.8 = 1.8.latest
            ->addStylesheet('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css')//add the css
            ->uiEnable();//enable ui

    }
}

