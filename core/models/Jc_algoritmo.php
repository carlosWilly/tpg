<?php

# Seguridad
defined('INDEX_DIR') OR exit('jcode software says .i.');

//------------------------------------------------

final class Jc_algoritmo extends Models implements OCREND {

  private 
  private 
  private 

  public function __construct() {
    parent::__construct();
  }
  //----------------------------------------
  final public function sqlSunMoon(){

  }
  //---------------------------------------
  
  # Control de errores
  final private function errors(array $data) {
	    try {
	      Helper::load('strings');
	      

	      if(false == $this->u or !Strings::chash($this->u[0][1],$data['pass'])) {
	        throw new Exception('<b>Error:</b> Credenciales incorrectas.');
	      }



	      return false;
	    } catch (Exception $e) {
	      return array('success' => 0, 'message' => $e->getMessage());
	    }
  }

  # Inicio de sesión
  final public function calcSigno(array $data) : array {
    $error = $this->errors($data);
    if(!is_bool($error)) {
      return $error;
    }

  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
