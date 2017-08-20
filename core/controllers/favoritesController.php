<?php
# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class favoritesController extends Controllers {

  public function __construct() {
    parent::__construct(true);
     echo $this->template->render('app/favorites',
     	[
        'token'=>$this->route->getMethod(),
     	'fav'=>(new Favoritos)->myFavorites()
     	]);
  }
}

?>
