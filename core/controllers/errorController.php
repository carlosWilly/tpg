<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class errorController extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('error/error',array('controller' => $this->route->getController()));
  }

}

?>
