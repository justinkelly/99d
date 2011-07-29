<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'test'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Facebook/src/facebook.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


switch(getenv('APPLICATION_ENV'))
{
case 'development':
    Zend_Registry::set('fbAppId', '128075177284337');
    Zend_Registry::set('fbApikey','128075177284337');
    Zend_Registry::set('fbSecret','66e2fe797c7cbc934ca0e3a26a1eec82');
    Zend_Registry::set('fb_baseurl', 'http://localhost.local/99d/public');
    break;
case 'test':
    Zend_Registry::set('fbAppId', '230808733626136');
    Zend_Registry::set('fbApikey','230808733626136');
    Zend_Registry::set('fbSecret','ecea142cda55c3c7815752e9eca3123c');
    Zend_Registry::set('fb_baseurl', 'http://kelly.org.au/dev/99d/public');
}
$facebook = new Facebook(array(
    'appId'  => Zend_Registry::get('fbAppId'),
    'secret' => Zend_Registry::get('fbSecret'),
    'cookie' => true
));

Zend_Registry::set('fbUser', $facebook->getUser());
Zend_Registry::set('facebook', $facebook);

if (Zend_Registry::get('fbUser')) {
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        Zend_Registry::set('fbuser_profile', $facebook->api('/me'));
        $view->fbuser_profile = Zend_Registry::get('fbuser_profile');
    } catch (FacebookApiException $e) {
        error_log($e);
        Zend_Registry::set('fbuser_profile', null);
        Zend_Registry::set('fbUser', null);
    }
}

if (Zend_Registry::get('fbUser')) {
    Zend_Registry::set('fbUrl', $facebook->getLogoutUrl(array('next'=> Zend_Registry::get('fb_baseurl') . "/fb/logout")));
} else {
    Zend_Registry::set('fbUrl', $facebook->getLoginUrl(
        array(
            'display'   => 'popup',
            'scope'         => 'email,offline_access,user_birthday,user_location,user_about_me,user_hometown,publish_stream',
            'next'      =>  Zend_Registry::get('fb_baseurl') . "/fb/login",
            'cancel_url'      =>  Zend_Registry::get('fb_baseurl') . "/test/fsetse",
            'redirect_uri' => Zend_Registry::get('fb_baseurl') . "/fb/login"
        )
    ));
}

$view->fbUrl = Zend_Registry::get('fbUrl');
$view->fbuser = Zend_Registry::get('fbUser');
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$application->bootstrap()
    ->run();
