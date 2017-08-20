<?php

# Seguridad
defined('INDEX_DIR') OR exit('Jcode software says .i.');

//------------------------------------------------

class Sessions extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  /**
    * Genera una sesión por un tiempo determinado para un usuario.
    *
    * @param int $id: Id de usuario para generar la sesión
    *
    * @return void
  */
  public function generate_session(array $session) {
    
    $_SESSION[SESS_APP_ID] = $session;
    $id = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
    $e['u_session'] = time() + SESSION_TIME;
    $this->db->update('jc_usuarios',$e,"u_id='$id'",'LIMIT 1');
  }

  /**
    * Chequea el uso de la sesión en un usuario.
    *
    * @param int $id: Id de usuario para generar la sesión 1483030757  1483017312
    *
    * @return bool: TRUE si el usuario tiene la sesión iniciada, FALSE si no
  */
  public function session_in_use(int $id) : bool {
    $time = time();
    if($this->db->rows($this->db->query("SELECT u_id FROM jc_usuarios WHERE u_id='$id' AND u_session >= '$time' LIMIT 1;")) > 0) {
      return true;
    }

    return false;
  }

  /**
    * Chequea la vida de una sesión, si ésta caduca se culmina la sesión existente.
    *
    * @param bool $force: Fuerza la culminación de una sesión que pueda existir.
    *
    * @return void
  */
  public function check_life(bool $force = false) {
    if(isset($_SESSION[SESS_APP_ID])) {

      $id = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
      $time = time();

      if($force || $this->db->rows($this->db->query("SELECT u_id FROM jc_usuarios WHERE u_id='$id' AND u_session <= '$time' LIMIT 1;")) > 0) {

        $e['u_session'] = 0;
        $this->db->update('jc_usuarios',$e,'u_id='.$id.'','LIMIT 1');

        unset($_SESSION[SESS_APP_ID]);
        unset($_SESSION['info']);
        unset($_SESSION['pe_time']);
        unset($_SESSION['limitControllerSearch']);
        unset($_SESSION['msgLimit']); 
        unset($_SESSION['chatTime']);
        unset($_SESSION['u_data_profile']);
        unset($_SESSION['limitTopSearch']);
        unset($_SESSION['social']);
        
        session_write_close();
        session_unset();
      }
        
        

    }

   
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
