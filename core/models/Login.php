<?php

# Seguridad
defined('INDEX_DIR') OR exit('Jcode software says .i.');

//------------------------------------------------

final class Login extends Models implements OCREND {

  private $email;
  private $session = null;
  private $u;

  public function __construct() {
    parent::__construct();
  }


  # Inicio de sesión
  final public function SignIn(array $data) : array {
    
      try {
        Helper::load('strings');


        if (!isset($data['u-email'],$data['u-pass'])) {//verificamos que existan las variables de logueo
          throw new Exception('<b>we are sorry</b> No variables');
        }

        if (!Func::all_full($_POST)) {//Nos aseguramos de que no esten vacias
          throw new Exception('<b>we are sorry</b> Complete all fields');
        }

        if (!Strings::is_email($data['u-email'])) {//verificamos que el email tenga el formato correcto
          throw new Exception('<b>we are sorry</b> Email not allowed');
        }

        $email = $this->db->scape($data['u-email']);
        $u = $this->db->select('u_id,u_pass,CONCAT(u_nombres," ",u_apellidos) as nombre,u_email,IFNULL(iu_img,"no-img.jpg") as iu_img,u_estado','jc_usuarios LEFT JOIN jc_images_users ON iu_id_user=u_id and iu_tipo=2',"u_email='$email' and u_estado!=2",'LIMIT 1');

        if(false == $u or !Strings::chash($u[0]['u_pass'],$data['u-pass'])) {//verificamos que la clave ingresada sea correcta
          throw new Exception('<b>We are sorry</b> Incorrect credentials');
        }

        if(DB_SESSION) {
            $this->session = new Sessions;//arrancamos la session obviamos si el usuario tiene mas de uan session iniciada
            /*if($this->session->session_in_use($this->u[0][0])) {
            throw new Exception('<b>Error:</b> Already have a session');
          }*/
        }

      } catch (Exception $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
      #procedimientos comunes
    
    if(DB_SESSION) {
      $session=[//armamos la matriz que contendrá nuestra session
        'u_id'=>Func::codeNumber($u[0]['u_id']),
        'u_name'=>$u[0]['nombre'],
        'u_email'=>$u[0]['u_email'],
        'u_img'=>$u[0]['iu_img'] == NULL ? '': $u[0]['iu_img']
      ];



      #si la informacion de perfil aun esta incompleta no permitimos la lectura de la cuenta
      if ($u[0]['u_estado']!=5) {
      
       //averiguamos datos de la cuenta personal del usuario si este aun no la tiene lo redireccionamos a los planes
        $account = $this->db->select('iau_id_user,ac_name,ac_msj,ac_color,ac_favorites,ac_photos,ac_friendchat,ac_matching,uc_chatmsj,uc_fav,uc_photo,uc_friendchat,uc_match,iau_timeend','jc_infoacountuser LEFT JOIN jc_userconsumoacount ON iau_id_user=uc_id_user LEFT JOIN jc_mbacounts ON ac_id=iau_count','iau_id_user='.$u[0][0].'');
        if (false!=$account) {//adicionamos a la sesion app_id el siguiente array de la cuenta
           $session['u_account'] =[
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
        }
      $session['u_data_profile']='COMPLETE';
    }else{
      $session['u_data_profile']='INCOMPLETE';
    }
      $this->session->generate_session($session);//generamos la session total
    } else {
      $_SESSION[SESS_APP_ID] = $this->u[0][0];
    }


    # Seguridad para evitar el logueo progresivo
    $_SESSION['pe_time'] = time() + 5;

    //verificamos vigencia del tiempo de la cuenta siempre y cuando no sean free o fullpack
    if ($_SESSION[SESS_APP_ID]['u_data_profile']=='COMPLETE') {
        (new Account)->limitTimeAccount();
    }
 

       
    
   if ($u[0]['u_estado']!=5) {

    //Traemos la hora del ultimo mensaje e chat que se envio si tiene, de lo contrario le damos entero 0;
    $ct = $this->db->select('msj_date','jc_message','msj_id_user='.Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']).'','ORDER BY msj_id DESC LIMIT 1');
    $_SESSION['chatTime']= $ct != false ? $ct[0][0] : 0;

    //reseteamos el contador del chat validando las 24 horas si es que llegó al limite de su couta
    $ac = new Account;
    if ($ac->veryLimisAccounts('mensajes')) {
         $ac->resetContador('uc_chatmsj','c_chats',$_SESSION['chatTime']);
    }
    //-------------------------------------

    #actualizamos el estado de online cada ves que inicia session y devolvemos el token donde actualziar ese estado
    $this->db->update('jc_usuarios',['u_estado'=>1],'u_id='.Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']).'');
    //--------------------------------------------------------------------  
  
    }

    
    return ['success' => 1, 'message' => '<b>Connected</b>, we are redirecting you.','token'=>$_SESSION[SESS_APP_ID]['u_id']];
  



  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
