<?php
/**
 * Joomla! 1.5 component myApi
 *
 * @version $Id: myapi.php 2010-05-01 08:43:14 svn $
 * @author Thomas Welton
 * @package Joomla
 * @subpackage myApi
 * @license GNU/GPL
 *
 * myApi - Combining the power of the Facebook platform with the ease and simplicity of Joomla.
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * myApi Component myApi Model
 *
 * @author      notwebdesign
 * @package		Joomla
 * @subpackage	myApi
 * @since 1.5
 */
class MyapiModelMyapi extends JModel {
    /**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
    }
	
	function getAvatar($id = NULL){
		$db = JFactory::getDBO();
		$user = JFactory::getUser($id);
		
		  $query = "SELECT avatar FROM ".$db->nameQuote('#__myapi_users')." WHERE userId =".$db->quote($user->id);
		  $db->setQuery($query);
		  $db->query();
		  return 'tn'.$db->loadResult();
	}
	
	//Get the permissions required
	// for now this just returns an array but in future it will only requests permsissions needed
	// for example without jomsocial integration we don't need user_status.
	function getPerms(){
		return array('email','offline_access','user_about_me','user_status','read_stream');
	}
	
	function _getPageCache($id){
		$facebook = plgSystemmyApiConnect::getFacebook();
		return $facebook->api('/'.$id,'get',array($facebook->getAppId().'|'.$facebook->getApiSecret()));
	}
	
	function getPage($id){
		$cache = & JFactory::getCache('com_myapi - Page '.$id);
		$cache->setCaching( 1 );
		$cache->setLifeTime( 60*60*12 );  //Waiting on bug fix change ot 0
		return $cache->call(array('MyapiModelMyapi','_getPageCache'),$id);
	}
	
	function _getFeedCache($id){
		$facebook = plgSystemmyApiConnect::getFacebook();
		$feed  =  $facebook->api('/'.$id.'/feed','get',array('limit' => 3));
		return $feed['data'];	
	}
	function getFeed($id){
		$facebook = plgSystemmyApiConnect::getFacebook();
		$cache = & JFactory::getCache('com_myapi - Feed '.$id);
		$cache->setCaching( 1 );
		$cache->setLifeTime( 60*60*12 );  //Waiting on bug fix change ot 0
		return $cache->call(array('MyapiModelMyapi','_getFeedCache'),$facebook->getAppId());
		
	}
}
?>