<?php
/**
 * Example Action Plugin
 */

// must run within Dokuwiki
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_issuetracker extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
         'author' => 'Taggic',
         'email'  => 'Taggic@t-online.de',
         'date'   => '2011-07-21',
         'name'   => 'Issue comments (action plugin component)',
         'desc'   => 'to display comments of a dedicated issue.',
         'url'    => 'http://forum.dokuwiki.org/thread/2456 '.
                     ' http://forum.dokuwiki.org/thread/7182',
         );
  }

  /**
   * Register its handlers with the dokuwiki's event controller
   */
  function register(&$controller) {
    $controller->register_hook('TPL_ACT_RENDER', 'BEFORE',  $this, 'showcase', array());
  }

/******************************************************************************
**  Generate output
*/
    function showcase(&$event, $param){
        global $ACT;
        
        $data = $event->data;
//        print "World parameter: " . $data . ' ' . $ACT ;      
        
        if (! is_array($ACT) || !(isset($ACT['showcase']))) return;
        print "Hello World: " . $ACT .' <-> '. $event->data . "<br> what ever it will be";      
        $event->preventDefault();
        $event->stopPropagation();
    }
}