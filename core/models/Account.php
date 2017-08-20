<?php

# Seguridad
defined('INDEX_DIR') OR exit('Jcode software says .i.');
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

//Clase diseñada por Carwill Industrias para playground

final class Account extends Models implements OCREND {

 private $user;
 private $account=[];

  public function __construct() {
    parent::__construct();

    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
    
  }
//-----------------------------------------------------------------------------
/**
  *Generador de transacion al momento del registro si éste no pasa atraves de paypal/ inserta la transaccion en la bd
  *@return void();
*/
final public function accountGenerateTransaction(int $token, string $code = null, string $hash = null, int $typeAccount = null, int $status = null, string $url= null):int{
  Helper::load('strings');
   //preparamos matriz a insertar
  $c = 'CWR-'.uniqid();
  $timeinit = time();

  $tr = [
   'tr_id_user'=> $token,
   'tr_id_tr'=>   $code == null ? $c : $code,
   'tr_hash'=>    $hash == null ? Strings::hash($c) : $hash,
   'tr_time'=>    $timeinit,
   'tr_tipo_account'=> $typeAccount == null ? 5 : $typeAccount,
   'tr_status'=> $status == null ? 1 : $status,
   'tr_url'=> $url == null ? '' : $url
  ];
  //insertamos la transaccion en la bd
  $this->db->insert('jc_transacciones',$tr);

  return $timeinit;
}
//------------------------------------------------------------------------------


/**
  *Creacion de infoaccount User en la base de datos, de ésta depende el control de tiempo de la cuenta
  *@param token entero contiende la id del usuario de quien se insertara la transacion
  *@param account entero  con el id de la cuenta que se esta insertando
  *@param timeinit entero con el tiempo en el que se inicio la transaccion en el metodo accountGenerateTransaction()
  *@param status pago entero  con el detalle de si se pago o no la cuenta
  *@param tipoProceso boleano true si se esta insertando  el fullpack por tres meses ó false si se inserta una cuenta comun por 1 mes
  *@return vacio
*/
final public function infoAccountUser(int $token,int $account, int $timeinit,int $statusPago, bool $tipoProceso){
   
  //preparamos array para insertar en la bd
  $ia = [
   'iau_id_user'=> $token,
   'iau_count'=> $account,
   'iau_timeinit'=>$timeinit,
   'iau_timeend'=> $timeinit + ($tipoProceso ? 7776000 /* 3 meses */ : 2592000 /* 1 mes*/),
   'iau_statuspago'=>$statusPago
  ];
  //insertamos datos en la tabla infoaccounUser
  $this->db->insert('jc_infoacountuser',$ia);

  //insertamos el consumo basico del usuario en la tabla jc_userconsumoaccount
  $icu = [
     'uc_id_user'=>$token,
     'uc_chatmsj'=>0,
     'uc_fav'=>0,
     'uc_photo'=>0,
     'uc_friendchat'=>0,
     'uc_match'=>0

  ];
  $this->db->insert('jc_userconsumoacount',$icu);
}
//-------------------------------------------------------------------------------


 /**
   *Clase metodo que permite verificar si el usuario ha llegado al limite de consumo de su cuenta
   *@param $rubro  de tipo string segun los detalles configurados en playground: mensajes,favoritos,fotos,amigos en chat y matchins.
   *@return booleano true si ha llegado al tope que se esta validando o false si todo anda bien con el limite
  */
  final public function veryLimisAccounts(string $rubro):bool{
     $this->account = $_SESSION[SESS_APP_ID]['u_account'];
     $success = false;
   //verificacion del tipo de cuenta
     $cuenta      = $this->account['tipoAccount'];
     //uso de variables
     switch ($rubro) {
       case 'mensajes':
         $consumo = $this->account['u_consumo']['c_chats'];
         $limit   = $this->account['u_limited']['l_chats'];
         break;
       
       case 'favoritos':
         $consumo = $this->account['u_consumo']['c_favor'];
         $limit   = $this->account['u_limited']['l_favor'];;
         break;

       case 'fotos':
        $consumo = $this->account['u_consumo']['c_photo'];
        $limit   = $this->account['u_limited']['l_photo'];
         break;
       case 'amigosChat':
         $consumo = $this->account['u_consumo']['c_fchat'];
         $limit   = $this->account['u_limited']['l_fchat'];
         break;
       case 'match':
         $consumo = $this->account['u_consumo']['c_match'];
         $limit   = $this->account['u_limited']['l_match'];
         break;
     }
     if ($cuenta!='PRO II' and $cuenta!='FULLPACK'  and $consumo >=$limit) {
         $success = true;
     }
     return $success;
  }
  //--------------------------------------------------------------------------------------------


  /**
    *Actualiza el consumo de los datos de la cuenta del usuario una ves terminado los procesos respectivos
    *al mismo tiempo que actualiza la session correspondiente al rubro segun los parametros correcpondientes en la cuenta
    *@param $rubro tipo string  segun los detalles configurados en playground: mensajes,favoritos,fotos,amigos en chat y matchins.
    *@param $token tipo entero valor requerido para actualizar consumo de amigos en chat , unico para este rubro, por defecto es null
    *@return void
  */

  final public function updateStatusAccount(string $rubro,int $token = null){
     $this->account = $_SESSION[SESS_APP_ID]['u_account'];
     switch ($rubro) {
       case 'mensajes':
         $campo   = 'uc_chatmsj';
         $quantity = $this->account['u_consumo']['c_chats'];
         $sess = 'c_chats';
         break;
       
       case 'favoritos':
         $campo   = 'uc_fav';
         $quantity = $this->account['u_consumo']['c_favor'];
         $sess = 'c_favor';
         break;

       case 'fotos':
        $campo   = 'uc_photo';
        $quantity = $this->account['u_consumo']['c_photo'];
        $sess = 'c_photo';
         break;
       case 'amigosChat':
         $campo   = 'uc_friendchat';
         $quantity = $this->account['u_consumo']['c_fchat'];
         $sess = 'c_fchat';
         if ($token != null) {
           $this->db->update('jc_userconsumoacount',[$campo =>$quantity +1],'uc_id_user='.$token.'');
         }
         
         break;
       case 'match':
         $campo   = 'uc_match';
         $quantity = $this->account['u_consumo']['c_match'];
         $sess = 'c_match';
         break;
     }
      
      //actualizamos table de consumo y  contador session

         $this->db->update('jc_userconsumoacount',[$campo =>$quantity +1],'uc_id_user='.$this->user.'');
         $_SESSION[SESS_APP_ID]['u_account']['u_consumo'][$sess] = $quantity +1;

     
  }
//-----------------------------------------------------------------------------
/**
  *Metodo que permite verificar si se ha cumplido el limite de tiempo que posee el usuario en su cuenta
*/
final public function limitTimeAccount(){
  $this->account = $_SESSION[SESS_APP_ID]['u_account'];
  Helper::load('Strings');

  //determinamos  el tipo de cuenta
  $account = $_SESSION[SESS_APP_ID]['u_account']['tipoAccount'];
  
  if ($account !='FREE') {
        $ac = $this->db->select('iau_timeend','jc_infoacountuser','iau_id_user='.$this->user.'');
        $days = Strings::date_difference(date('d-m-Y',time()),date('d-m-Y',$ac[0][0]));
        


       
        
        if ($days >0 and $days <=5 ) {
           $mensaje ='The time period of your '.strtoupper ($account).' account is about to end,  You have '.$days.' days of use, remember to improve or renew it, otherwise you will stop Automatically to the free plan';
           $_SESSION['info']=[
              'permanent'=>false,
              'timeEnd'=>time()+1800,
              'message'=>$mensaje
           ];
        }

        if ($days <=0) {

           //actualizamos la tabla de informacion de cuenta del usuarios
           $update =['iau_count'=>1,'iau_timeinit'=>time(),'iau_timeend'=>0,'iau_statuspago'=>1];
           $this->db->update('jc_infoacountuser',$update,'iau_id_user='.$this->user.'');

           //Actualizamos la session de la cuenta a Free
                $ac = $this->db->select('
                iau_id_user,ac_name,ac_msj,ac_color,ac_favorites,ac_photos,ac_friendchat,ac_matching,uc_chatmsj,uc_fav,uc_photo,uc_friendchat,uc_match,iau_timeend',
                'jc_infoacountuser 

                LEFT JOIN jc_userconsumoacount ON iau_id_user=uc_id_user 
                LEFT JOIN jc_mbacounts ON ac_id=iau_count',

                'iau_id_user='.$this->user.'');
              
              //var_dump($account);die();
              if (false!=$ac) {
                   $_SESSION[SESS_APP_ID]['u_account'] =[
                     'status'=>'success',
                     'tipoAccount'=>$ac[0]['ac_name'],
                     'accountColor'=>$ac[0]['ac_color'],
                     'accountDateLimit'=>$ac[0]['iau_timeend'],
                     'u_limited'=>[
                          'l_chats'=>$ac[0]['ac_msj'],
                          'l_favor'=>$ac[0]['ac_favorites'],
                          'l_photo'=>$ac[0]['ac_photos'],
                          'l_fchat'=>$ac[0]['ac_friendchat'],
                          'l_match'=>$ac[0]['ac_matching']
                     ],
                     'u_consumo'=>[
                          'c_chats'=>$ac[0]['uc_chatmsj'],
                          'c_favor'=>$ac[0]['uc_fav'],
                          'c_photo'=>$ac[0]['uc_photo'],
                          'c_fchat'=>$ac[0]['uc_friendchat'],
                          'c_match'=>$ac[0]['uc_match']  
                     ]
                   ];
              }
          //-----------------------------------------------------------
        }
  }

}
//------------------------------------------------------------------------------

/**
  * reseteo de tiempo ya sea  para los mensajes del chat o el matching
  *@return booleano true si se puede resetar false si aun no se ha cumplido el tiempo
*/
final public function resetContador(string $campo,string $session,int $time){
Helper::load('strings');
 
  $pasedTime = Strings::date_difference(date('d-m-Y',$time),date('d-m-Y',time()));
  

    if ($pasedTime >0) {
       
       $_SESSION[SESS_APP_ID]['u_account']['u_consumo'][$session]=0;
       $this->db->update('jc_userconsumoacount',[$campo => 0],'uc_id_user='.$this->user.'');
       
    }
      
}
//-----------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
