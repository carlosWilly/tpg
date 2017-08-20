<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');
/**
 * CARWILL INDUSTRIAS NACIONALES SAC
 * http://www.carwillstudios.com/
 * Author: Carlos willy Ruiz Villalobos
 * Email: carwill_12@hotmail.com 
 * Celphone: 959071246
 * Country: Peru
 * Description: Clase que trabaja con el modulo de favoritos en toda la aplicacion
 */

//------------------------------------------------

final class Favoritos extends Models implements OCREND {

  private $user;

  public function __construct() {
    parent::__construct();

    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }
  //------------------------------------------------------------

  final public function myFavorites() : array{
      Helper::load('strings');
      try {
        $data = $this->db->select('fa_id,u_id, IFNULL(iu_img,"no-img.jpg") as u_img,CONCAT_WS(" ",u_nombres,u_apellidos) as u_nombre,u_fecnac,sz_icon as f_icon,sz_name as f_zodiacal,st_name f_estado,(select count(*) FROM jc_blacklist WHERE bl_user_black=u_id) as black','jc_favoritos LEFT JOIN jc_usuarios ON fa_id_fa=u_id LEFT JOIN jc_images_users ON u_id=iu_id_user  and iu_tipo=2  LEFT JOIN jc_zodiacal ON u_zodiacal=sz_id LEFT JOIN jc_states ON u_state=st_id','fa_id_user='.$this->user.' HAVING black!=1','ORDER BY u_id DESC');
      if(false == $data) throw new Exception("No favorites for the moment", 1);
         foreach ($data as $key => $f) {
              $data[$key]['fa_id']=Func::codeNumber($f['fa_id']);
              $data[$key]['u_id']=Func::codeNumber($f['u_id']);
              $data[$key]['u_fecnac']=Strings::calculate_age(Func::dateSpanish($f['u_fecnac']));
          }
        return ['success'=>1,'data'=>$data];
      } catch (Exception $e) {
        return ['success'=>0,'message'=>$e->getMessage()];
      }

  }
//---------------------------------------------------------------------------------
 final public function addFavorites(array $data):array{
   Helper::load('strings');
   try {
     if (!isset($data['jc-token']) || empty($data['jc-token'])) {
         throw new Exception("No vars");
     }
     if (!Func::decodeNumber($data['jc-token'])) {
       throw new Exception("Error codification");
     }
    $cuenta  = $_SESSION[SESS_APP_ID]['u_account']['tipoAccount'];

     //verificamos  el vencimiento de la cuenta
     if ($cuenta != 'FREE') {
        $days = Strings::date_difference(date('d-m-Y',time()),date('d-m-Y',intval($_SESSION[SESS_APP_ID]['u_account']['accountDateLimit'])));
        if ($days <=0) {
          throw new Exception("The time period of your account is over, please renew your plan or improve it.");//El período de tiempo de su cuenta ha terminado, renueve su plan o mejórelo.

        }
     }
     //----------------------------------------------------------------------------


     //verificacion de consumo de la cuenta
     if ((new Account)->veryLimisAccounts('favoritos')) {
       throw new Exception("You have reached your account limit to add favorites");//Has alcanzado el límite de tu cuenta para agregar favoritos

     }
     //-----------------------------------------------------------------------------

      $id_user = intval(Func::decodeNumber($data['jc-token']));
     //averguamos si ya esta en nuestra lista de favoritos
     $prev = $this->db->select('fa_id','jc_favoritos','fa_id_user='.$this->user.' and fa_id_fa='.$id_user.'','LIMIT 1');
     if (false != $prev) {
        throw new Exception("This person is already on your favorites list");//Esta persona ya está en tu lista de favoritos

     }

   } catch (Exception $e) {
     return ['success'=>0,'message'=>$e->getMessage()];
   }
  
     
       
      $a=['fa_id_user'=>$this->user,'fa_id_fa'=>$id_user];
      $this->db->insert('jc_favoritos',$a); 

      //actualizamos  la tabla de consumo en favoritos y la session favoritos
      if ($cuenta!='FULLPACK') {
         (new Account)->updateStatusAccount('favoritos');
       }

      $success =1;
      $message = 'Has been successfully added to your favorites list';


   return ['success'=>$success,'message'=>$message];
 }

 //delete person of my favorites
 final public function deleteFavorites(array $data):array{
     //var_dump($data['tokenUser']);die();
    try {
      if (!isset($data['tokenUser'])) {
          throw new Exception("Could no Process"); 
      }
       
      if (!Func::decodeNumber($data['tokenUser'])) {
       throw new Exception("Could no Process"); 
      }
      
    } catch (Exception $e) {
      return ['success'=>0,'message'=> $e->getMessage()];
    }
    //------------------------------
    $tokenUser = Func::decodeNumber($data['tokenUser']);
    $this->db->delete('jc_favoritos','fa_id_user='.$this->user.' and fa_id_fa='.intval($tokenUser).'');

    $consumo =  $_SESSION[SESS_APP_ID]['u_account']['u_consumo']['c_favor'];
    $act =           $_SESSION[SESS_APP_ID]['u_account']['u_consumo']['c_favor']  = $consumo -1;
    $this->db->update('jc_userconsumoacount',['uc_fav'=>$act],'uc_id_user='.$this->user.'');

    return ['success'=>1,'message'=>'This person has been removed from your favorites'];

 }
//------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
