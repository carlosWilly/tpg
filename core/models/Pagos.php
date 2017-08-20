<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Pagos extends Models implements OCREND {
    private $user;
	public function __construct() {
		parent::__construct();
		$this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
	}


//revisar el pago
final public function revisar(){
  Helper::load('paypal');
  return Paypal::check_pay();
}



//actualizacion  del usuario a pagado
final public function userPagado(string $id, string $hash=null, int $free = null):array{
   Helper::load('strings');
   $success =0;$message='no cloud process';$data=[];
   $transaccion = $this->db->select('tr_status,tr_hash,tr_tipo_account','jc_transacciones','tr_id_user='.$this->user.' AND tr_id_tr="'.$id.'"','LIMIT 1');

  
   if ($transaccion != false) {
        if ($transaccion[0]['tr_status']==1) {

           $message ='This transaction is not available';//esta transaccion no  esta disponible
       
        }else{
            $vt = Strings::chash($transaccion[0]['tr_hash'],$id);


            if ($vt) {
              $time = time();
              $timeEnd = $free != null ? 0 : $time+2592000;


              $this->db->update('jc_transacciones',['tr_time'=>$time,'tr_status'=>1,'tr_url'=>''],'tr_id_user='.$this->user.' AND tr_id_tr="'.$id.'"');
              //-----------------------------------------------------------------------------------------
                    //insertamos datos de la cuenta del usuario en la table jc_infoacountuser
                       
                       //AVERIGUAMOS SI ESTE USUARIO TIENE UNA CUENTA PREVIA SI LA TIENE LA ACTUALIZAMOS SI NO LA TIENE INSERTAMOS
        
                       $acount = $this->db->select('COUNT(*)','jc_infoacountuser','iau_id_user='.$this->user.'');

                        
                       if ($acount[0][0] >0) {

                          $iau = ['iau_count'=>$transaccion[0]['tr_tipo_account'],'iau_timeinit'=>$time,'iau_timeend'=>$timeEnd,'iau_statuspago'=>1];
                          $this->db->update('jc_infoacountuser',$iau,'iau_id_user='.$this->user.'');

                       }else{

                          $iau = ['iau_id_user'=>$this->user,'iau_count'=>$transaccion[0]['tr_tipo_account'],'iau_timeinit'=>$time,'iau_timeend'=>$timeEnd,'iau_statuspago'=>1];
                          $this->db->insert('jc_infoacountuser',$iau);
                       }


                       $consumo = $this->db->select('COUNT(*)','jc_userconsumoacount','uc_id_user='.$this->user.'');
                       if ($consumo>0) {

                         $uc = ['uc_chatmsj'=>0,'uc_fav'=>0,'uc_photo'=>0,'uc_friendchat'=>0,'uc_match'=>0];
                         $this->db->update('jc_userconsumoacount',$uc,'uc_id_user='.$this->user.'');

                       }else{

                         $uc = ['uc_id_user'=>$this->user,'uc_chatmsj'=>0,'uc_fav'=>0,'uc_photo'=>0,'uc_friendchat'=>0,'uc_match'=>0];
                         $this->db->insert('jc_userconsumoacount',$uc);
                       }
                       
                       //averiguamos datos de la cuenta
                       $cc = $this->db->select('*','jc_infoacountuser LEFT JOIN jc_mbacounts ON iau_count=ac_id ','iau_id_user='.$this->user.'','limit 1'); 
                        
                       
                        //INICIAMOS LA SESSION DEL USUARIO DE ACUERDO A SU PLAN O SIMPLEMENTE LA ACTUALIZAMOS
                        $_SESSION[SESS_APP_ID]['u_account'] =[
                                 'status'=>'success',
                                 'tipoAccount'=>strtoupper($cc[0]['ac_name']),
                                 'accountColor'=>strtoupper($cc[0]['ac_color']),
                                 'accountDateLimit'=>$timeEnd,
                                 'u_limited'=>[
                                      'l_chats'=>$cc[0]['ac_msj'],
                                      'l_favor'=>$cc[0]['ac_favorites'],
                                      'l_photo'=>$cc[0]['ac_photos'],
                                      'l_fchat'=>$cc[0]['ac_friendchat'],
                                      'l_match'=>$cc[0]['ac_matching']
                                 ],
                                 'u_consumo'=>[
                                      'c_chats'=>0,
                                      'c_favor'=>0,
                                      'c_photo'=>0,
                                      'c_fchat'=>0,
                                      'c_match'=>0  
                                 ]
                        ] ;
              $success = 1;
              $message ='Congratulations, you have purchased the free plan, we hope your experience on our site is the most complete, we are always serving, attentively playground.';
              
              //ARRAY PARA MOSTRAR EL PLAN ADQUIRIDO
              $data=[
               'account'=>$cc[0]['ac_name'],
               'favoritos'=>$cc[0]['ac_favorites'],
               'mensajes'=>$cc[0]['ac_msj'],
               'amigosChat'=>$cc[0]['ac_friendchat'],
               'matching'=>$cc[0]['ac_matching'],
               'fotos'=>$cc[0]['ac_photos']
              ];
              //-----------------------------------------------------------------------------------------
            }
            
        }
   }

   return ['success'=>1,'message'=>$message,'data'=>$data];
}
//-----------------------------------------------------------------------------------------
final public function pay(array $data):array{
	Helper::load('paypal');Helper::load('strings');
    try {
    	if (!isset($data['token']) || empty($data['token'])) {//verificamos que nos este llegando el id del plan
    		 throw new Exception("Sorry we can not continue with your payment.");
    		 
    	}
    	if (!Func::decodeNumber($data['token'])) {//decodificamos el id del plan
    		throw new Exception("Sorry we can not continue with your payment(2).");
    	}

    	$token_plan = intval(Func::decodeNumber($data['token']));//id del plan que esta viniendo por url
    	//verificamos la existencia del plan
    	$plan = $this->db->select('ac_id,ac_precio,ac_name','jc_mbacounts','ac_id='.intval($token_plan).'','LIMIT 1');
    	if (false == $plan) {
    		throw new Exception("Sorry, this plan is no longer available.");
    	}

    } catch (Exception $e) {
    	return ['success'=>0,'message'=>$e->getMessage()];
    }
    //------------------------------------------------------------------------------------------------------------
    

   //configuracion de mensaje y redireccion de  procesos de paypal
		$config = [
	          'url'=>'recibo',
	          'descripcion'=>'Payment of membership in playground'
		];

    //configuracion de item de pago de acuerdo a alos planes
	    $item = [
                [ 
      	        'nombre'=>'Membership for the plan '.strtoupper($plan[0]['ac_name']),
      	        'cantidad'=>1,
      	        'precio'=>$plan[0]['ac_precio'],
      	        'envio'=>0,
      	        'tax'=>0
              ]
	    ];
    //Inicializacion de metodo de pago de paypal

   if ($plan[0]['ac_id'] !=1 || $plan[0]['ac_id'] !=5 ) $pago =   Paypal::pay($config,$item);
    
   /* (new Account)->accountGenerateTransaction(
             $this->user, 
             $plan[0]['ac_id'] ==1 || $plan[0]['ac_id'] ==5 ? null  : $pago['id'],
             $plan[0]['ac_id'] ==1 || $plan[0]['ac_id'] ==5 ?  null : $pago['hash'],
             $plan[0]['ac_id'], int $status = null, string $url= null);*/
    // guardasmos los datos en la bd
    $idFree = 'CWR-'.uniqid(); 
    $bd = array(
        'tr_id_user'=>$this->user,
        'tr_id_tr'=>$plan[0]['ac_id'] ==1 ? $idFree : $pago['id'],
        'tr_hash' =>$plan[0]['ac_id'] ==1 ?  Strings::hash($idFree) : $pago['hash'],
        'tr_time'=>time(),
        'tr_tipo_account'=>$plan[0]['ac_id'],
        'tr_status'=>0,
        'tr_url'=>$plan[0]['ac_id'] ==1 ? '' : $pago['url']
      );
    $this->db->insert('jc_transacciones',$bd);
    
    $url = $plan[0]['ac_id'] ==1 ? 'recibo?account=free&tokenAccount='.$idFree :  $pago['url'];

   return ['success'=>1,'url'=>$url];
}

//--quitar transacion si no se desea pagar
final public function removeTransaction(array $data):array{
  try {
    if (!isset($data['tokenTr'])) {
      throw new Exception('Could not Process(0)');
      
    }

    if (!Func::decodeNumber($data['tokenTr'])) {
      throw new Exception('Could not Proces (1)'); 
    }

    $token = Func::decodeNumber($data['tokenTr']);
  } catch (Exception $e) {
    return ['success'=>0,'message'=>$e->getMessage()];
  }

  $this->db->delete('jc_transacciones','tr_id='.intval($token).' and tr_id_user='.$this->user.' and tr_status=0');
   return ['success'=>1,'message'=>'This transaction has been successfully removed'];
}

//proceso para trasaccion de usuario de tipo fullPack
final public function fullPack():array{

try {
  //verificamos que el usuario no tenga otras cuentas y se ala primera ves que se registra para aplicar full pack
    $tr = $this->db->select('count(*)','jc_transacciones','tr_id_user='.$this->user.'');
    if ($tr[0][0]>0) {
      throw new Exception('This package is not available for you (1)');
      
    }

    $iau = $this->db->select('count(*)','jc_infoacountuser','iau_id_user='.$this->user.'');
    if ($iau[0][0]>0) {
       throw new Exception('This package is not available for you (2)');
    }
} catch (Exception $e) {
  return ['success'=>1,'message'=>$e->getMessage()];
}
//insertamos datos en la tabla jc_transaciones para full_pack
$fp = new Account;
$trsp = $fp->accountGenerateTransaction($this->user);
//var_dump($trsp);die();
//insertamosa datos en la tabla matriz de informaciond e la cuenta del usuario
$fp->infoAccountUser($this->user,5,$trsp,1,true);
//generamos la session account para el control de la cuenta
$account = $this->db->select('
        iau_id_user,ac_name,ac_msj,ac_color,ac_favorites,ac_photos,ac_friendchat,ac_matching,uc_chatmsj,uc_fav,uc_photo,uc_friendchat,uc_match,iau_timeend',
        'jc_infoacountuser 

        LEFT JOIN jc_userconsumoacount ON iau_id_user=uc_id_user 
        LEFT JOIN jc_mbacounts ON ac_id=iau_count',

        'iau_id_user='.$this->user.'');

           $_SESSION[SESS_APP_ID]['u_account'] =[
             'status'=>'success',
             'tipoAccount'=>$account[0]['ac_name'],
             'accountColor'=>$account[0]['ac_color'],
             'accountDateLimit'=>$account[0]['iau_timeend'],
             'u_limited'=>[
                  'l_chats'=>$account[0]['ac_msj'],
                  'l_favor'=>$account[0]['ac_favorites'],
                  'l_photo'=>$account[0]['ac_photos'],
                  'l_fchat'=>$account[0]['ac_friendchat'],
                  'l_match'=>$account[0]['ac_matching']
             ],
             'u_consumo'=>[
                  'c_chats'=>$account[0]['uc_chatmsj'],
                  'c_favor'=>$account[0]['uc_fav'],
                  'c_photo'=>$account[0]['uc_photo'],
                  'c_fchat'=>$account[0]['uc_friendchat'],
                  'c_match'=>$account[0]['uc_match']  
             ]
           ];

return ['success'=>1,'message'=>'You are already part of our family, now enjoy 100% of our services'];
}
//-----------------------------------------------------------------------------------------
	public function __destruct() {
		parent::__destruct();
	}
}

?>
