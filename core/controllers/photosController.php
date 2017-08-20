<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class photosController extends Controllers {

  public function __construct() {
    parent::__construct(true);

    $info = new ProfileInfo;
    $iu = new InfoUsers;
     //ddd($info->profilePhotos(['data-require'=>$this->route->getMethod()]));
     $profile = $info->ProfileGrall(['data-profile'=>$this->route->getMethod()]);
     echo $this->template->render('app/photos',
     	[
        'token'=>$this->route->getMethod(),
     	'data'=>$profile['data'],
     	'photos'=>$info->profilePhotos(['data-require'=>$this->route->getMethod()],false),
     	]);
  }

}

?>
