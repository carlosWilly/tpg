<?php

# Seguridad
defined('INDEX_DIR') OR exit('jcode software says .i.');

//------------------------------------------------

final class Photos extends Models implements OCREND {
  protected $user;
  protected $route_photos;
  

  public function __construct() {
    parent::__construct();
    $this->route_photos ='../views/assets/images/users/';
    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }

  #averiguamos la existencia de la imgen previa a actualizar ya sea perfil o portada
  final private function ifExist(string $campos,int $iu_img){
     //averiguamos  la imagen de portada que se  ecuentra actualmente o si es que existe
        $pimage = $this->db->select($campos,'jc_images_users','iu_id_user='.$this->user.' and iu_tipo='.$iu_img.'','LIMIT 1');
        if (false != $pimage) {
           
           if (file_exists($this->route_photos.$pimage[0][0])) {
              unlink($this->route_photos.$pimage[0][0]);
           }
           //eliminamos la imagen de portada anterior
           $this->db->delete('jc_images_users','iu_id='.$pimage[0][1].' and iu_id_user='.$this->user.'');
        }
  }
  
 # Con esta función sabemos si el archivo es peligroso (PHP, JS, CSS, XML, JSON, DB, SQL)
  final private function is_danger(string $file) : bool {
      Helper::load('files');
      if(in_array(strtolower(Files::get_file_ext($file)),['php','php5','html','phtml','js','css','xml','json','db','sql'])) {
          return true;
      }
      return false;
  }

/**
  *Metodo que permite al usuario cargar imagenes desde su pc ya sea al catalogo,portada o perfil
*/
  final  public function photosProfile(array $data):array{
    Helper::load('files');
   
    try {
      
    if (!isset($data['p_type'],$_FILES['jc-picture']['name'])) {//comprobamos que exitan las vAIABLES DE TIPO Y la imagen 
      throw new Exception("Please select an a image"); 
    }
     
    if (Func::e($data['p_type'],$_FILES['jc-picture']['name'])) {//evitamos que el usuario no suba imagenesvacias
       throw new Exception("Please select an a image"); 
    }

    if (!Strings::only_letters($data['p_type'])) {//comprobamos que el tipo de imagen no este vacia
     throw new Exception("An error has occurred"); 
    }
    //------------------------------------------------------------------------------------------------------------------------------//
        $cuenta  = $_SESSION[SESS_APP_ID]['u_account']['tipoAccount'];

        //verificamos  el vencimiento de la cuenta
        if ($cuenta != 'FREE') {
            $days = Strings::date_difference(date('d-m-Y',time()),date('d-m-Y',intval($_SESSION[SESS_APP_ID]['u_account']['accountDateLimit'])));
            if ($days <=0) {
              throw new Exception("The time period of your account is over, please renew your plan or improve it.");//El período de tiempo de su cuenta ha terminado, renueve su plan o mejórelo.

            }
        }
     $consumo = $_SESSION[SESS_APP_ID]['u_account']['u_consumo']['c_photo'];
     //----------------------------------------------------------------------------
       //verificamos el consumo del usuario en fotos
       if ((new Account)->veryLimisAccounts('fotos')) {
         throw new Exception("You have reached your account limit to add photos");//Has alcanzado el límite de tu cuenta para agregar favoritos

       }
     //--------------------------------------------------------------------------------------------------------------------------//
    
    $img_name = $_FILES['jc-picture']['name'];

    if ($this->is_danger($img_name)) {//forato de imagen no permitido
       throw new Exception("Format file not allowed(1)");
    }

    if(!Files::is_image($img_name)){//formato de imagen no permitido
       throw new Exception("Format file not allowed(2)");
    }

    if(Files::file_size($_FILES['jc-picture']['tmp_name']) > 2048){// solo se permiten imagenes <=2mb
      throw new Exception("Size not allowed, only 2MB");
    }

    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }
    #//procesmiento detos de la cargade imagenes
    
    //calculamos el tipo de imagen
    switch ($data['p_type']) {
      case 'catalogo':
        $image_tipo =0;
        break;
      
      case 'portada':
        $image_tipo =1;
            $this->ifExist('iu_img,iu_id',1);
        break;

      case 'profile':
         $image_tipo =2;
         $this->ifExist('iu_img,iu_id',2);

        break;

      default:
        $image_tipo = 0;
      break;
    }
    
    //procedemos a la carga de la imagen en el servidor
      $img_gname = uniqid();
        //upload
      $image = new Upload($_FILES['jc-picture']);
      if ($image->uploaded) {
          $image->file_new_name_body   = $img_gname;
          $image->image_resize         = false;
          $image->image_ratio          = true;
          $image->process($this->route_photos);
        if ($image->processed) {

          $image->clean();
          // if image is upload
          $e=[
            'iu_id_user'=>$this->user,
            'iu_img'=>$img_gname.'.'.Files::get_file_ext($img_name),
            'iu_tipo'=>$image_tipo
          ];
          $this->db->insert('jc_images_users',$e);
          //---------------//---------------------------------//
            //actualizamos  la tabla de consumo en favoritos y la session favoritos
            if ($cuenta!='FULLPACK') {
               (new Account)->updateStatusAccount('fotos');
             }
          //---------------//---------------------------------//
          
          $success = 1;
          $message = 'Image upload successfull';
        } else {
          
          $success = 0;
          $message = 'error : ' . $image->error;
        }
        }
    //actualizamos la imagen de perfil si esta es asi
    $image_tipo == 2 ? $_SESSION[SESS_APP_ID]['u_img'] = $img_gname.'.'.Files::get_file_ext($img_name) :''; 


    return ['success'=>$success,'message'=>$message];
  }
  //---------------------------------------
  
  /**
    *retorna array de fotos
    *@param no existe params
    *@return array con las fotos del usuario en session para ser usadas  ya sea  como fotos de portada o perfil
  */
  final public function myPictures():array{
    $photo = $this->db->select('iu_id,iu_img','jc_images_users','iu_id_user='.$this->user.' and iu_tipo=0','ORDER BY iu_id DESC');
    try {
      if ($photo== false) {
        throw new Exception('You have not uploaded a photo yet');
        
      }
    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }

    return ['success'=>1,'message'=>'success','data'=>$photo];
  }

  //--------------------------------------------------------------------------------------------------------------
  /**
    *Actualiza foto de portada o perfil del usuario seleccionado una de su catalogo personal.
    *@param no existe params
    *@return array con las fotos del usuario en session para ser usadas  ya sea  como fotos de portada o perfil
  */

  final public function updateSelecion(array $data):array{
    
    try {

       if (!isset($data['token-type'],$data['token-user'])) {
          throw new Exception("No var");
       }
       if (!Func::all_full($data)) {
          throw new Exception("Complete all fields");
       }
       
       switch ($data['token-type']) {
         case 'portada':
           $type=1;//imagen de portada
           break;
        case 'perfil'://imagen de perfil 0 seria imagen de catalogo
           $type=2;
           break;
         
         default:
            throw new Exception("No process");
           break;
       }
       
       //averiguamos si la imagen que se esta queriendo actualizar pertenece a este usuario
       $photo = $this->db->select('iu_img','jc_images_users','iu_id ='.intval($data['token-user']).' and  iu_id_user='.$this->user.'','LIMIT 1');
      if ($photo== false) {
        throw new Exception('This image can not be used');
      }
    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }
  //------------------------------------------------------------------------------------------------------------------------------------
   

  //averiguamos si ya tiene una imagen de portada o no si la tiene obtenemos los datos
  $pante = $this->db->select('iu_id,iu_img','jc_images_users','iu_id_user='.$this->user.' and iu_tipo='.$type.'','LIMIT 1');
  
  $fto = explode('.',$photo[0][0]);
  $newPicture = uniqid().'.'.$fto[1];
  $copia = copy($this->route_photos.$photo[0][0], $this->route_photos.$newPicture);

  if (false == $pante) {
      //insertamos la copia de la imagen en la bd
      $matriz = [
          'iu_id_user'=>$this->user,
          'iu_img'=>$newPicture,
          'iu_tipo'=>$type
      ];
      $this->db->insert('jc_images_users',$matriz);
  }else{
     //eliminamos la imagen previa
     unlink($this->route_photos.$pante[0][1]);
     //actualizamos el nombre de la foto de portada en la bd
     $this->db->update('jc_images_users',['iu_img'=>$newPicture],'iu_id_user='.$this->user.' and iu_tipo='.$type.'');
  }
  
   
    //actualizamos la imagen de perfil si esta es asi
    $type == 2 ? $_SESSION[SESS_APP_ID]['u_img'] = $newPicture :''; 

    return ['success'=>1,'message'=>'success','data'=>$newPicture];
  }


  //--------------------------------------------------------------------------------------------------------------
  /**
    *Elimina imagenes de los usuarios por session e id
    *@param $data e sun array con los paramtros venidos por ajax
    *@return array success y message
  */
  final public function deleteImgUsers(array $data):array{
    // var_dump($data);die();
    try {
       if (!isset($data['token-user'])) {
          throw new Exception("No Variables");
       }

       if (empty($data['token-user'])) {
         throw new Exception("Empty Var");
       }
       if (!is_numeric($data['token-user'])) {
         throw new Exception("No numeric");
       }
       
       //averiguamos si existe la imagen
       $pante = $this->db->select('iu_img','jc_images_users','iu_id_user='.$this->user.' and iu_id='.intval($data['token-user']).'');
       //obtenemos el id de la ex imagen de portada par actualizarla luego
       if ($pante == false) {
          throw new Exception("Can not be deleted");
       }
       

    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }
    //--------------------------------------------------------------------------------
    if (file_exists($this->route_photos.$pante[0][0])) {
           unlink($this->route_photos.$pante[0][0]);
    }

    $this->db->delete('jc_images_users','iu_id_user='.$this->user.' and iu_id='.intval($data['token-user']).''); 
    

    return ['success'=>1,'message'=>'Successfully deleted'];
  }

  //-------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
