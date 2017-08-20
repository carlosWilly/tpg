<?php
# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class infoController extends Controllers {

  public function __construct() {
     parent::__construct(true);
     
     $info = new ProfileInfo;
     $profile = $info->ProfileGrall(['data-profile'=>$this->route->getMethod()]);
     echo $this->template->render('app/info',
     	[
        'token'=>$this->route->getMethod(),
     	'data'=>$profile['data'],
     	]);


  }

}

?>

