<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class inoauthController extends Controllers {

  public function __construct() {
    parent::__construct();
    
    if (isset($_GET['error'])) {
      echo $this->template->render('error/oauth');
    }else{ 
    //---------------------------------------------------------------------------------------------------
     $li = new LinkedIn\LinkedIn(['api_key' => CLIENT_ID, 'api_secret' => APP_SECRETL, 'callback_url' => 'http://localhost/tpg/inoauth']);
     $token = $li->getAccessToken($_REQUEST['code']);     
     $user = $li->get('/people/~:(id,first-name,last-name,picture-urls::(original),email-address)');
    //---------------------------------------------------------------------------------------------------
    $_SESSION['social']=true;

     #matriz a devolver
     $in = [
               'u_email'=>$user['emailAddress'],
               'u_name'=>$user['firstName'],
               'u_lastname'=>$user['lastName'], 
               'u_picture'=>$user['pictureUrls']['values'],
               'u_slId'=>$user['id'],
               'u_socialTipe'=>1
     ];
     #procesos de registro o logueo
     (new Register)->regMultiple($in);
     Func::redir();exit;
    }

  } 

}

?>
