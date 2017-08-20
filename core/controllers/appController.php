<?php

# Seguridad
defined('INDEX_DIR') OR exit('carwill software says .i.');

//------------------------------------------------

class appController extends Controllers {
 
  public function __construct() {
    parent::__construct(true);
    //-----------------------------------------------------
   
  
    //-----------------------------------------------------
    
    echo $this->template->render('app/profile',['data'=>$profile]);
  }

}

?>
