<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class eprofileController extends Controllers {

  public function __construct() {
    parent::__construct(true);

    //(new Pagos)->userPagado();
    //calculo de deatalles d e cuenta

    echo $this->template->render('app/eprofile',['data'=>(new InfoUsers)->infoUser()['data']]);
  }
}

?>
