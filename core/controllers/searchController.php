<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class searchController extends Controllers {

  public function __construct() {
    parent::__construct(true);
    $db = new Conexion;
    $z = $db->select('*','jc_zodiacal');
    $e = $db->select('*','jc_states');
    echo $this->template->render('app/search',
     [
        'token'=>$this->route->getMethod(),        
        //'search'=>(new Search)->optionSearch([]),
        'z'=>$z,
        'e'=>$e
     ]
    );
  }

}

?>
