<?php

class Uploader extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Con esta función sabemos si el archivo es peligroso (PHP, JS, CSS, XML, JSON, DB, SQL)
    final private function is_danger(string $file) : bool {
        if(in_array(strtolower(Files::get_file_ext($file)),['php','php5','html','phtml','js','css','xml','json','db','sql'])) {
            return true;
        }

        return false;
    }

    # Captamos los posibles errores
    final public function Errors(array $data, bool $freelancer) {
        try {
            Helper::load('strings');
            Helper::load('emails');

            # Seguridad
            if(isset($_SESSION['pe_time']) and end($_SESSION['pe_time']) >= time()) {
              throw new Exception('No puedes realizar tantas acciones seguidas.');
            }

            # Sólo estos son requeridos
            if(Func::e($data['email_ag'],$data['pass_ag'],$data['name_ag'],$data['phone_ag'],$data['cel_ag'],$data['direc_ag'],$data['rif_ag'])) {
                # PD: las comillas dobles consumen más recursos que las simples.
                throw new Exception('<b>Error: </b>Todos los campos marcados con <span class="red">*</span> deben estar llenos.');
            }

            # Y esos son solo requeridos si se está solicitando una agencia
            if(!$freelancer and Func::e($data['gerent'],$data['cel'],$data['tlf'],$data['corr'])) {
                throw new Exception('<b>Error: </b>Todos los campos marcados con <span class="red">*</span> deben estar llenos.');
            }

            if (!Strings::is_email($data['email_ag'])) {
                throw new Exception('<b>Error: </b>El email debe tener un formato válido.');
            }
            if (!is_numeric($data['phone_ag']) or !is_numeric($data['cel_ag'])) {
                throw new Exception('<b>Error: </b>Los números de teléfono solo deben contener números.');
            }

            return false;
        } catch (Exception $e) {
            return Func::sendResponse(false, $e->getMessage());
        }
    }

    # Creamos un directorio temporal
    final public function tmp_dir(){
        $tmp = uniqid();
        $dir = 'views/app/.tmp/';
        if (!is_dir($dir . $tmp)) {
            mkdir($dir . $tmp, 0777,true);
        }else{
            $tmp = $tmp . md5(time());
            mkdir($dir . $tmp, 0777,true);
        }
        return $tmp;
    }

    # Eliminamos un directorio que no se esté utilizando cada 2 horas si no ha habido un cambio en el
    final public function limpiar_dir(){
        $tmp = 'views/app/.tmp/';
        Helper::load('files');
        foreach (glob($tmp . '*') as $dir) {
            if (filectime($dir) < (time() - (60*60*2))) {
                Files::rm_dir($dir);
                rmdir($dir);
            }
        }
    }

    # Cargamos los archivos a la carpeta temporal
    final public function upload_archive(){
        if (!empty($_FILES)) {
            Helper::load('files'); # Se llama al Helper Files

            $dir = '../views/app/.tmp/' .$_POST['tmp_dir'] .'/';
            $name = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];

            if (file_exists($dir . $name)) {
                $dir = $dir . time() . $name;
            } else {
                $dir = $dir . $name;
            }

            # Protección contra el agujero de seguridad que permitiría un buen ataque
            # Esta función internamente usa El HELPER FILES, por lo que para usarla debemos llamar antes al helper
            if(!$this->is_danger($name)) {
                move_uploaded_file($tmpName, $dir);
            }
        }
        Func::sendResponse(true, 'Funcionando');
    }

    # Con esta funcion movemos los archivos a la carpeta correspondiente
    final public function move_files(int $id, string $tmp_dir){
        $file_dir = '../views/app/.tmp/'.$tmp_dir.'/';
        Helper::load('files');
        $new_dir = '../views/app/images/documentos/'.$id.'/';
        if (!is_dir($new_dir)) {
            mkdir($new_dir, 0777, true);
        }
        foreach (glob($file_dir . '*') as $file) {
            $name = explode('/', $file);
            $name = end($name);
            if (file_exists($new_dir . $name)) {
                unlink($new_dir . $name);
            }
            copy($file, $new_dir . $name);
            unlink($file);
        }
    }

    # Borra un archivo en el directorio temporal
    final public function delete_archive(string $name, string $dir) {
        $dir = '../views/app/.tmp/' .$dir .'/' . $name;

        if(file_exists($dir)) {
            unlink($dir);
        }

        return array('success' => 1, 'message' => $dir);
    }

    public function __destruct() {
        parent::__destruct();
    }
}
?>
