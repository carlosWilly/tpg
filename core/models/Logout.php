<?php

# Seguridad
defined('INDEX_DIR') OR exit('Jcode software says .i.');

//------------------------------------------------

final class Logout extends Models implements OCREND {



  public function __construct() {
    parent::__construct();
  }
  //-----------------------------------------------------------
  final public function logoutUser(){
    
    if (isset($_SESSION[SESS_APP_ID])) {
      $this->db->update('jc_usuarios',['u_estado'=>$_SESSION[SESS_APP_ID]['u_data_profile']=='INCOMPLETE' ? 5 : 0],'u_id = '.Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']).'');
    }
    if(DB_SESSION) {

      (new Sessions)->check_life(true);

    } 
    return 1;
  }
  //-----------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
