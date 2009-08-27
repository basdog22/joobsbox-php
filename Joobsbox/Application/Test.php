<?php
error_reporting(E_ALL | E_NOTICE | E_STRICT);
ini_set("display_errors", "on");
ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . APPLICATION_DIRECTORY);

require_once APPLICATION_DIRECTORY . "/Joobsbox/Application/Development.php";
require_once APPLICATION_DIRECTORY . "/config/config.php";
require_once APPLICATION_DIRECTORY . "/config/viewRenderer.php";

Zend_Controller_Front::getInstance()->setControllerDirectory(APPLICATION_DIRECTORY . '/Joobsbox/Controllers');

Zend_Registry::set("PluginLoader",      new Joobsbox_Plugin_Loader);
Zend_Registry::set("EventHelper", 		  new Joobsbox_Helpers_Event);
Zend_Registry::set("FilterHelper", 		  new Joobsbox_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Joobsbox_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

Zend_Controller_Action_HelperBroker::addPrefix("Joobsbox_Helpers");

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new Joobsbox_Plugin_Controller);

//configureTheme();
Zend_Registry::get("EventHelper")->fireEvent("joobsbox_init");