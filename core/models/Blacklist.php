<?php

# Seguridad
defined('INDEX_DIR') OR exit('jcode software says .i.');
/**
 * CARWILL INDUSTRIAS NACIONALES SAC
 * http://www.carwillstudios.com/
 * Author: Carlos willy Ruiz Villalobos
 * Email: carwill_12@hotmail.com 
 * Celphone: 959071246
 * Country: Peru
 * Description: Clse diseñada por los general verificar  antes de realizar un proceso si el usuario se encuentra en la lista negra
 * esto evita y asegura a los usuarios de no estar en contacto con personas que no desean.
 */

//------------------------------------------------

final class Blacklist extends Models implements OCREND {
  
  protected $user;
  
  public function __construct() {
    parent::__construct();
    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }
////..............................................................................................................................................

 /**
   *Detecta si el usuario se encuentra en la lista negra
   *@param $user tipo entero id del usuario a comprobar
   *@return boleano true si esta o false si no lo esta
 */
 final public function blacklistDetect(int $user):bool{
   $b = $this->db->select('count(*)','jc_blacklist','bl_user_main='.intval($user).' and bl_user_black='.$this->user.'');
   return $b[0][0] > 0 ? true : false;
 }
 //---------------------------------------------------------------------------------------------------------------------


 /**
   * Metodo para listar a las personas que se encuentran en mi lista negra, por lo genral dedicado a las vistas
   *@param ninguno
   *@return array con los datos de las personas o errores indicados
 */
 final public  function blacklistInfo():array{
   $bl = $this->db->select('bl_id,bl_user_black,u_nombres,u_apellidos,YEAR(CURDATE())-YEAR(u_fecnac) as edad,iu_img','jc_blacklist LEFT JOIN jc_usuarios ON bl_user_black=u_id LEFT JOIN jc_images_users ON iu_id_user=bl_user_black AND iu_tipo=2','bl_user_main='.$this->user.'');
   try {
    
    if (!$bl) {
        throw new Exception('You do not have people on your blacklist yet');
     } 

   } catch (Exception $e) {
     return ['success'=>0,'message'=>$e->getMessage()];
   }

     return ['success'=>1,'message'=>'You have people on your blacklist','data'=>$bl];
 }
//----------------------------------------------------------------------------------------------------------------------


/**
  *Metodo que permite agregar a una persona a mi lista negral personal
  *@param $data tipo array conteniendo el id del usuario codificado ['token'=>]
  *@return array con los mensajes resultantes del proceso
*/
 final public function blacklistAdd(array $data):array{
   try {

     #verificamos la existenci de la variable
     if (!isset($data['token'])) {
         throw new Exception('Could not process');
         
     }
     #verificamos que la id  que viene codificada sea correcrta
     if (!Func::decodeNumber($data['token'])) {
         throw new Exception('Could not process');
      }
      $tokenUser = Func::decodeNumber($data['token']);


      $bl = $this->db->select('COUNT(*)','jc_blacklist','bl_user_main='.$this->user.' and bl_user_black='.$tokenUser.'');
      
      #verificamos si el usuario  ya se eucentra registrado en nuestra black list
      if ($bl[0][0] >0) {
         throw new Exception('This user has already registered with your blacklist');
         
      }

   } catch (Exception $e) {
     return ['success'=>0,'message'=>$e->getMessage()];
   }

   $bls = [
     'bl_user_main'=>$this->user,
     'bl_user_black'=>intval($tokenUser),
     'bl_time_black'=>time()
   ];

   #insertamos  el usaurario a la black list
   $this->db->insert('jc_blacklist',$bls);
   #insertamos notificacion al usuario a quien le insertando en la blacklist
   /*$noti=[
     'sl_id_remite'=>$this->user,
     'sl_id_destino'=>$tokenUser,
     'sl_fecha'=>time(),
     'sl_tipo'=>3,//agregar a la lista negra
     'sl_status'=>0,
     'sl_visto'=>0
   ];
   $this->db->insert('jc_solicitudes',$noti);*/

   #devolvemos el mensaje
   return ['success'=>1,'message'=>'This user has been registered to your blacklist'];
 }
//---------------------------------------------------------------------------------------------------------------------------

/**
  * Metodo que retira a un usuario de la lista negra
  *@param array que contiene el id codificada del usuario a retirar de la lista negra
  *@return array con los mensajes resultantes del proceso  
*/
 final public function blacklistDelete(array $data):array{
    try {
      if(!isset($data['token'])){
         throw new Exception('Could not process');
      }

      if (!Func::decodeNumber($data['token'])) {
         throw new Exception('Could not process');
      }

    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }
    
    $token = intval(Func::decodeNumber($data['token']));

    $destination = $this->db->select('bl_user_black','jc_blacklist','bl_id='.$token.' and bl_user_main='.$this->user.'');

    $this->db->delete('jc_blacklist','bl_id='.$token.' and bl_user_main='.$this->user.'');
    //insertamos notificacion al usuario a quien le insertando en la blacklist
   /*$noti=[
     'sl_id_remite'=>$this->user,
     'sl_id_destino'=>$destination[0][0],
     'sl_fecha'=>time(),
     'sl_tipo'=>4,//Te han retirado de la black list
     'sl_status'=>0,
     'sl_visto'=>0
   ];
   $this->db->insert('jc_solicitudes',$noti);*/
    return ['success'=>1,'message'=>'Successful process','data'=>Func::codeNumber($destination[0][0])];

 }
//---------------------------------------------------------------------------------------------------------------------------------

////...............................................................................................................................
  public function __destruct() {
    parent::__destruct();
  }

}

?>
