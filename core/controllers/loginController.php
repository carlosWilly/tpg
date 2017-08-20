<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class loginController extends Controllers {

  public function __construct() {
    parent::__construct(false,true);
    echo $this->template->render('home/login');
  }

}

?>
