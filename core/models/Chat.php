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
 * Description: Clase que permite actuar a las principales acciones correspondientes al modulo de chat
 */

//------------------------------------------------

final class Chat extends Models implements OCREND {

  private $user;

  public function __construct() {
    parent::__construct();

    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }
//------------------------------------------------------------



/**
  * Permite ingresar una solicitud de chat de el usuario en accion hacia otro
  *@param array id codificado del usario a quien se le envia la solicitud
  *@return array con los mensajes resultantes del proceso
*/

final public function solicitudChat(array $data):array{
  
  try {
    
    $cuenta  = $_SESSION[SESS_APP_ID]['u_account']['tipoAccount'];

     #verificamos  el vencimiento de la cuenta
     if ($cuenta != 'FREE') {
        $days = Strings::date_difference(date('d-m-Y',time()),date('d-m-Y',intval($_SESSION[SESS_APP_ID]['u_account']['accountDateLimit'])));
        if ($days <=0) {
          throw new Exception("The time period of your account is over, please renew your plan or improve it.");//El período de tiempo de su cuenta ha terminado, renueve su plan o mejórelo.

        }
     }
     #-----------------------------------------------------------------------------
     $consumo = $_SESSION[SESS_APP_ID]['u_account']['u_consumo']['c_fchat'];
     $limit   = $_SESSION[SESS_APP_ID]['u_account']['u_limited']['l_fchat'];


     if ($cuenta!='PRO II' and $cuenta!='FULLPACK' and $consumo >=$limit) {
       throw new Exception("You have reached your account limit to add people to chat");//Ha alcanzado el límite de su cuenta para añadir personas al chat.


     }
     #-----------------------------------------------------------------------------
    if (!Func::decodeNumber($data['token-user'])) {
       throw new Exception("Error codification");
    }
    
    $id_user = Func::decodeNumber($data['token-user']);

    #------------------------------------------------------------------------------
    #averiguamos si se encuentra en la lista negra
    $blackList = $this->db->select('count(*)','jc_blacklist','bl_user_main='.$id_user.' and bl_user_black='.$this->user.'','LIMIT 1');
    if ($blackList[0][0] > 0) {
          throw new Exception('Sorry, you can not contact this user, you may have blocked');
    }
    #----------------------------------------------------------------
    
    #averigumos si ya tenemos una conversacion inciada con el usuario en peticion
    $call = $this->db->select('count(*)','jc_code_chat','ch_userOne='.$this->user.' and ch_userDos='.$id_user.'','LIMIT 1');
    if ($call[0][0] > 0) {
          throw new Exception('Sorry, You already have a conversation started');
    }
    #---------------------------------------------------------------------------------
    
    #verificamos si ya  tiene solicitudes y su estado
    $prev = $this->db->select('sl_status','jc_notifications','sl_id_remite='.$this->user.' and sl_id_destino='.$id_user.' and sl_tipo=0','LIMIT 1');
    if ($prev != false) {
       switch ($prev[0][0]) {
         case 0:
           throw new Exception('You have a request pending, please wait');
           break;
       }
    }

  #----------------------------------------------------------------
  } catch (Exception $e) {
    return array('success' => 0, 'message' => $e->getMessage());
  }


  $a = [
      'sl_id_remite'=>$this->user,
      'sl_id_destino'=>$id_user,
      'sl_fecha'=>time(),
      'sl_tipo'=>0,
      'sl_status'=>0,
      'sl_visto'=>0
  ];
  #insertamos la solicitud en la bd
  $this->db->insert('jc_notifications',$a);
  $count = $this->db->select('count(*)','jc_notifications','sl_id_destino='.$id_user.' and sl_visto=0');
  return ['success'=>1,'message'=>'Your request has been sent','men'=>'so'.Func::codeNumber($id_user),'count'=>$count[0][0],'action'=>'solicite'];

}
//---------------------------------------------------------------------------------------------------------------------------------------------------


/**
  *Metodo que permite cancelar una solicitud de chat proveniente de otro usuario
 */
final public function chatSoliciteCancel(array $data):array{

  
  try {
     if (!isset($data['token'])) {
        throw new Exception("No vars");
        
     }

     if (!Func::decodeNumber($data['token'])) {
       throw new Exception("Codification error");
       
     }

     $sl_token = intval(Func::decodeNumber($data['token']));
     //verificamos si la solicitud a cancelar existe ademas de pertenecer a este usuario
     $sl = $this->db->select('sl_id,sl_id_remite,sl_status','jc_notifications','sl_id='.$sl_token.' and sl_id_destino='.$this->user.'','LIMIT 1');

     if (false == $sl) {
        throw new Exception("requestnot allowed");
     }else{

      if ($sl[0][2]==1) {
         throw new Exception("Sorry, this request has already been confirmed");
         
      }elseif ($sl[0][2]==2) {
        throw new Exception("Sorry, this request has already been canceled.");
      }
     }
  } catch (Exception $e) {
    return ['success'=>0,'message'=>$e->getMessage()];
  }
  #bloque de procemientos comunes
  //actualizamos la solititud previa a respondia para evitar los botones
  $this->db->update('jc_notifications',['sl_status'=>2],'sl_id='.$sl_token.' and sl_id_destino='.$this->user.'');
  $slt=[
    'sl_id_remite'=>$this->user,
    'sl_id_destino'=>intval($sl[0]['sl_id_remite']),
    'sl_fecha'=>time(),
    'sl_tipo'=>2,
    'sl_status'=>0,
    'sl_visto'=>0
  ];
  $this->db->insert('jc_notifications',$slt);
  return ['success'=>1,'message'=>'You just canceled this conversation request','data'=>Func::codeNumber($sl[0]['sl_id_remite'])];
}
//------------------------------------------------------------------------------



/**
  *Metodo que habilita el chat para los usuarios solicitantes, al mismo  tiempo que general el codigo de conversacion
  *@param array $data con el id  de la solicitud a procesar
  *@return array que contiene mensajes de la operacion, id codificado del usuario a quien se le manda el resultado de la solicitud
  *por el soket, numero de mensajes sin ver, y nombre del proceso para identificar la accion del socket
*/

final public function permisionChat(array $data):array{
  //var_dump($data);die();
  $ac = new Account;//lamamos a la clase account para verificacion de limites de cuenta
  try {
    if(!isset($data['n_token'],$data['n_mode']) || Func::e($data['n_token'],$data['n_mode'])) throw new Exception("Error Processing Request", 1);
    
    $id_noti= intval($data['n_token']);
    $n_mode = intval($data['n_mode']);

    #comparamos el id de solicitud en la tabla de solicitudes para validar y verificar que es veridica
    $noti = $this->db->select('*','jc_notifications','sl_id='.$id_noti.' and sl_id_destino='.$this->user.'','LIMIT 1');
    if (false == $noti) throw new Exception('Sorry, Can not process');
          
    
    
    #verificacion de consumo de la cuenta
     if ($ac->veryLimisAccounts('amigosChat')) {
       throw new Exception("You have reached your account limit to add friends to chat, Improve your account in the configuration section");
     }
    

    #------------------------------------------------------------------------------
    #averiguamos si se encuentra en la lista negra
    
    if ((new Blacklist)->blacklistDetect($noti[0][1])) {
          throw new Exception('Sorry, you can not contact this user, you may have blocked');
    }
    #----------------------------------------------------------------
    
    #averigumos si ya tenemos una conversacion inciada con el usuario en peticion
    $call = $this->db->select('count(*)','jc_code_chat','ch_userOne='.$this->user.' and ch_userDos='.$noti[0][1].'','LIMIT 1');
    if ($call[0][0] > 0) {
          throw new Exception('Sorry, You already have a conversation started');
    }
    #---------------------------------------------------------------------------------
    
  
  $code = uniqid('cw');
  $time = time();
  
  $a = [
      'ch_userOne'=>$this->user,
      'ch_userDos'=>$noti[0][1],
      'ch_code'=>$code,
      'ch_fecha'=>$time
  ];

  $b = [
      'ch_userOne'=>$noti[0][1],
      'ch_userDos'=>$this->user,
      'ch_code'=>$code,
      'ch_fecha'=>$time
  ];
  #insertamos la solicitud en la bd
  $this->db->insert('jc_code_chat',$a);
  $this->db->insert('jc_code_chat',$b);
  #actualizamos estado de la solicitud
  $this->db->update('jc_notifications',['sl_status'=>1],'sl_id='.$id_noti.'');
  #insertamos un tipo de notificacion que equiva a la respuesta de solicitud de chat
  #actualizamos el consumo de los usuarios
  $ac->updateStatusAccount('amigosChat');
  //$ac->updateStatusAccount('amigosChat',$id_noti);
  #----------------------------------------------------------------
  $nv = [
    'sl_id_remite'=>$this->user,
    'sl_id_destino'=>$noti[0][1],
    'sl_fecha'=>time(),
    'sl_tipo'=>1,// tu solicitud ha sido aceptada
    'sl_status'=>0,//0 indica aceptada // 1 cancelada
    'sl_visto'=>0,
  ];

  $this->db->insert('jc_notifications',$nv);
  #contamos solicitudes a retornar
 

  #retornamos array
  return ['success'=>1,'message'=>'Your request has been sent','toa'=>Func::codeNumber($noti[0][1])];

  } catch (Exception $e) {
    return array('success' =>0,'message'=>$e->getMessage());
  }

}
//-------------------------------------------------------------------------------------------------------------------------------------------------







/**
  *Metodo que inserta los mensajes enviado por los usuarios desde el chat al mismo tiempo que devuelve valores para ser enviados por el socket
  *@param  array con el id del usuario a quien se envia el mensaje y el codigo de conversacion
  *@return  artay 
       * 0=> id codificada del usario a quien se manda el mensaje, 
       * 1=> id codificada de quien manda el mensaje
       * 2=> mensaje que se ha enviado para ser redistribuido por el socket
*/
final public function sendMessage(array $data):array{
 //var_dump($data);die();
  
  $ac = new Account;
  Helper::load('strings');
  try {

    if (!isset($data['token'],$data['string'])) {
         throw new Exception('<b>Error:</b> no procedure');
    }

    if (!Func::all_full($data)) {
         throw new Exception('<b>Error:</b> complete al fields');
    }

    if (!Func::decodeNumber($data['token'])) {
         throw new Exception('<b>Error:</b> codification errors');
    }

    $token = Func::decodeNumber($data['token']);
    $time = time();

    
    #verificacion de consumo de la cuenta
     if ($ac->veryLimisAccounts('mensajes')) {
               //reseteamos contador si es que es la situacion
               $ac->resetContador('uc_chatmsj','c_chats',$_SESSION['chatTime']);
               throw new Exception("You have reached the limit of your account to chat, Improve your account in the configuration section, or wait 24 hours, to start over");
     }
    
    
    $_SESSION['chatTime'] = $time;
    #averiguamos si se encuntra en la lista negra para evitar conversaciones
    if ((new Blacklist)->blacklistDetect($token)) {
          throw new Exception('Sorry, you can not contact this user, you may have blocked');
    }
    
   
    $bst = $this->db->select('ch_code,iu_img','jc_code_chat LEFT JOIN jc_images_users ON iu_id_user='.$this->user.' and iu_tipo=2','ch_userOne='.$this->user.' and ch_userDos='.$token.'');
    if(false == $bst) throw new Exception("send messaje fail", 1);
    
    #verificamos hash
   /* if (!Strings::chash($tokenbst,$bst[0][2])) {
        throw new Exception('<b>Error:</b> codification error(2)');
    }*/
  #-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $e=[
           'msj_id_user'=>$this->user,
           'msj_id_destino'=>$token,
           'msj_date'=>$time,
           'msj_message'=>strip_tags($this->db->scape($data['string'])),
           'msj_see'=>0,
           'msj_ch_code'=>$bst[0][0]
        ];

        $this->db->insert('jc_message',$e);
        //actualizamos la session de contador de mensajes en el chat
        $ac->updateStatusAccount('mensajes');
        //gurdamos el tiempo del ultimo mensaje en una session para resetearla si es necesario
        $_SESSION['chatTime'] = $time;
    return ['success' =>1,'img'=>$bst[0][1]];
  } catch (Exception $e) {
    return ['success'=>0,'message'=>$e->getMessage()];

  }



}
//--------------------------------------------------------------------------------------------------------------------------------





//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::CARGA  MENSAJES  AL MOMENTO DE ABRIR EL BOX DE CHAT:::::::::::::::::::
/**
  *
  *@param array que contiene el id codificado del usuario y el id de conversaion
  *@return array con los mesajes del proceso 
  *data array que cotiene los mensajes de usario con las que inicie la conversacion
  *nombre del usario de quien estoy pidiendo la conversacion
  *id de del usario propio osea mio pero codificado
*/
final public function messageLoadBox(array $data):array{

             Helper::load('strings');           
              try {
                  if (!isset($data['token']) || empty($data['token'])) throw new Exception("Error Processing Request", 1);  

                  if (!Func::decodeNumber($data['token'])) {
                       throw new Exception('<b>Error:</b> codification error');
                  }
                  $token = Func::decodeNumber($data['token']);
                  $data = $this->db->select('
                      msj_id,
                      msj_id_user,
                      msj_id_destino,
                      IFNULL((SELECT iu_img from jc_images_users where iu_id_user=msj_id_user and iu_tipo=2 LIMIT 1),"no-img.jpg") as msj_img,
                      FROM_UNIXTIME(msj_date,"%m/%d/%Y") as msj_date,
                      msj_message 
                    ','jc_message',

                    'msj_id_user='.$this->user.' and  msj_id_destino='.intval($token).' or msj_id_user='.intval($token).' and  msj_id_destino='.$this->user.'');
                  
                   #actualizamos los mensajes a vistos
                   $this->db->update('jc_message',['msj_see'=>1],'msj_id_destino='.$this->user.' and msj_id_user='.intval($token).'');
                  if($data == false) throw new Exception("No messages", 1);
                  
                  foreach (array_column($data, 'msj_id_user') as $key=>$v) {
                      $data[$key]['msj_id_user']=Func::codeNumber($v);
                  }

    return ['success'=>1,'data'=>$data];
  } catch (Exception $e) {
    return ['success' => 0, 'message' => $e->getMessage(),'process'=>0,'data'=>[] ];
  }
}


//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//----------------------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
