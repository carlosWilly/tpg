<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class homeController extends Controllers {
  
  public function __construct() {
    parent::__construct(false,true);
    //facebook
    $fb = new Facebook\Facebook(['app_id' => APP_ID,'app_secret' => APP_SECRET,'default_graph_version' => DEFAULT_GRAPH_VERSION]);
	$helper = $fb->getRedirectLoginHelper();
    $permissions = ['email']; // Optional permissions
    $oauthFace = $helper->getLoginUrl('http://localhost/tpg/fboauth', $permissions);
    //linkedin
    $li = new LinkedIn\LinkedIn(['api_key' => CLIENT_ID, 'api_secret' => APP_SECRETL, 'callback_url' => 'http://localhost/tpg/inoauth']);
    $oauthIn = $li->getLoginUrl([$li::SCOPE_BASIC_PROFILE, $li::SCOPE_EMAIL_ADDRESS]);
    echo $this->template->render('home/home',['oauthFace'=>$oauthFace,'oauthIn'=>$oauthIn]);
  }

}

?>
