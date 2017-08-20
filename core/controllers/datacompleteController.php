<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class datacompleteController extends Controllers {
  
  public function __construct() {
    parent::__construct(true);
    if ($_SESSION[SESS_APP_ID]['u_data_profile']=='COMPLETE') {
    	if (isset($_SESSION[SESS_APP_ID]['u_welcome'])) {
    		# code...
    	}else{
          Func::redir();exit;
    	}
    	
    }

    if (isset($_SESSION[SESS_APP_ID]['u_welcome'])) {
    	
    	if ($_SESSION[SESS_APP_ID]['u_welcome']==0) {
    		$view = 'bienvenida';
    	}

    }else{
    		$view = 'datacomplete';
    	}
    echo $this->template->render('app/'.$view);
  }

}

?>
