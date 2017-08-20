<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

abstract class Controllers {

  //------------------------------------------------
 
  protected $template;
  protected $isset_id;
  protected $method;
  protected $route;
  protected $session = null;

  //------------------------------------------------

  /**
    * Constructor, inicializa los alcances de todos los Controladores
    *
    * @param bool $LOGED: Si el controlador en cuestión será exclusivamente para usuarios logeados, se pasa TRUE
    * @param bool $UNLOGED: Si el controlador en cuestión será exclusivamente para usuarios NO logeados, se pasa TRUE
    *
    * @return void
  */
  protected function __construct(bool $LOGED = false, bool $UNLOGED = false) {

    global $router;

    # Accedemos a el router para URL's amigables
    $this->route = $router;

    # Control de vida de sesiones
    if(DB_SESSION) {
      $this->session = new Sessions;
      $this->session->check_life();
    }

    # Restricción para usuarios logeados
    if($LOGED and !isset($_SESSION[SESS_APP_ID])) {
      Func::redir(URL . 'home');
      exit;
    }

    # Restricción de página para ser visa sólamente por usuarios No logeados
    if($UNLOGED and isset($_SESSION[SESS_APP_ID])) {
      Func::redir(URL.'profile/'.$_SESSION[SESS_APP_ID]['u_id']);
      exit;
    }
  
    //Obligamos al usuario a aceptar nuestra promocion de tres meses gratis, tambien comprobamos que su perfil este completo
     if (isset($_SESSION[SESS_APP_ID])) {
         
         if ($_SESSION[SESS_APP_ID]['u_data_profile']=='INCOMPLETE' and $router->getController()!='datacompleteController'   and $router->getController()!='logoutController') {
              Func::redir(URL.'datacomplete');
               exit;
         }

        if (isset($_SESSION[SESS_APP_ID]['u_welcome']) and $_SESSION[SESS_APP_ID]['u_welcome']==0   and $router->getController()!='datacompleteController'   and $router->getController()!='logoutController') {
              Func::redir(URL.'datacomplete');
              exit;

         }
         
         
         
     }

    # Carga del template
    $this->template = new League\Plates\Engine('templates','phtml');
    #lanzamos variables globales para ser usados por todo el template y no ensuciar los controladores
    if (isset($_SESSION[SESS_APP_ID])) {
    $iu = new InfoUsers;

    $menuTop = $iu->infoUserTop();
    $osearch = $iu->optionSearch(false);
    //$fav     = (new Favoritos)->myFavorites();

    $this->template->addData(['menuTop' => $menuTop,'osearch'=>$osearch,/*'fav'=>$fav*/]);
    }
    #-------------------------------------------------------------------------------------------------
    # Debug
    if(DEBUG) {
      $_SESSION['___QUERY_DEBUG___'] = array();
    }

    # Utilidades
    $this->method = ($router->getMethod() != null and Strings::alphanumeric($router->getMethod())) ? $router->getMethod() : null;
    $this->isset_id = ($router->getId() != null and is_numeric($router->getId()) and $router->getId() >= 1);

  }

}

?>
