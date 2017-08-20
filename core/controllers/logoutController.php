<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class logoutController extends Controllers {

  public function __construct() {
    parent::__construct();
    
    (new Logout)->logoutUser();
    
  }

}

?>
