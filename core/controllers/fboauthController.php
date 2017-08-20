<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class fboauthController extends Controllers {

  public function __construct() {
    parent::__construct();
    //---------------------------------------------------------------------------------------------------
	  $fb = new Facebook\Facebook([
	  'app_id' => APP_ID,
	  'app_secret' => APP_SECRET,
	  'default_graph_version' => DEFAULT_GRAPH_VERSION,
	  ]);

    $helper = $fb->getRedirectLoginHelper();
    $accessToken = $helper->getAccessToken();
    #obtenemos la bateria de datos para trabajarla con la bd
   	try {
			  // Returns a `Facebook\FacebookResponse` object
			  $response = $fb->get('/me?fields=id,first_name,last_name,email,timezone,picture,gender,age_range', $accessToken->getValue());
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
			}

			$user = $response->getGraphUser();
      
      $_SESSION['social']=true;
			
      #matriz a devolver
			$fb = [
               'u_email'=>$user['email'],
               'u_name'=>$user['first_name'],
               'u_lastname'=>$user['last_name'],
               'u_picture'=>[$user->getPicture()['url']],
               'u_slId'=>$user['id'],
               'u_socialTipe'=>0
			];
     #procesos de registro o logueo
     (new Register)->regMultiple($fb);
      Func::redir();exit;
//---------------------------------------------------------------------------------------------------
  }
}
?>
