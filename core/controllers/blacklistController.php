<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class blacklistController extends Controllers {

  public function __construct() {
    parent::__construct(true);
   
    $iu = new InfoUsers;
    echo $this->template->render('app/blacklist',[
         'user'=>$iu->infoUser(),
         'bl'=>(new Blacklist)->blacklistInfo(),
    	]);
  }

}

?>
