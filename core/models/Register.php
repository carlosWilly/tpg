<?php
# Seguridad

defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

final class Register extends Models implements OCREND {
  
  

  public function __construct() {
    parent::__construct();
  }

  final public function regDataComplete(array $data):array{
    $me = intval(Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']));
   try {
     if (!isset($data['u_nacimiento'],$data['u_hora'],$data['u_oldlocation'],$data['u_nowlocation'],$data['u_gender'],$data['u_option'],$data['u_preference'])) {
       throw new Exception("parameters not available");
       
     }

     if (Func::e($data['u_nacimiento'],$data['u_hora'],$data['u_oldlocation'],$data['u_nowlocation'],$data['u_gender'],$data['u_option'],$data['u_preference'])) {
       throw new Exception("Complete all fields");
       
     }
     //validamos el formato de la fecha y rangos de edades permitidas
     $date= explode('/',$data['u_nacimiento']);
     $hora= explode(':',$data['u_hora']);

     if (count($date)<=0 || count($date)>3) {
       throw new Exception("Date not allowed");
     }
     #validamos rangos de dias
     if ($date[1]<=0 || $date[1] >31) {
       throw new Exception("Day not allowed");
     }

     if ($date[0]<=0 || $date[0] >12) {
       throw new Exception("Month not allowed");
     }

     if ((date('Y',time()) - intval($date[2]))>=90) {
        throw new Exception("Age limit allowed: 90 years");//limite de edad permitido:90 años
     }

     if ((date('Y',time()) - intval($date[2]))<=13) {
        throw new Exception("Minimum age: 13 years");//Edad mínima: 13 años
     }
     #validamos el formato y limite de la hora
     if (count($date)<=0 || count($date)>3) {
       throw new Exception("time not allowed");
     }
     
     if ($hora[0]<=0 || $hora[0] >24) {
       throw new Exception("Hour not allowed");
     }

     if ($hora[1]<0 || $hora[1] >60) {
       throw new Exception("Minuts not allowed");
     }


     //--end validation formato fecha
     #validamos sexo option y preferecia
     if($data['u_gender'] <=0 || $data['u_gender'] >2){
             throw new Exception('<b>Error:</b> Gender not allowed');
      }elseif($data['u_option'] <=0 || $data['u_option'] >3){
         throw new Exception('<b>Error:</b> Options not allowed');
      }elseif($data['u_preference'] <=0 || $data['u_preference'] >3){
         throw new Exception('<b>Error:</b> Preference not allowed');
      }

     $geo = new Geolocation;
     #validamos la ubicacion geografica para el lugar de nacimiento
      $ly_1 = $geo->localityUser($this->db->scape($data['u_oldlocation']));

      if (!isset($ly_1['country'],$ly_1['administrative_area_level_1'],$ly_1['locality'])) {
       throw new Exception('Verifica tu lugar donde vives actualmente, no es el formato permitido');
      }

      $location_1 = $this->db->select('pa_id,st_iso,ci_id','jc_pais LEFT JOIN jc_states ON st_iso="'.$ly_1['administrative_area_level_1'].'" LEFT JOIN jc_city ON ci_name="'.$ly_1['locality'].'"','pa_iso="'.$ly_1['country'].'"','LIMIT 1');
      if ($location_1) {
         if (!$location_1[0][1]) {
             throw new Exception('We are sorry ...State not allowed');
           }  
      }else{
         throw new Exception('We are sorry ... is not yet available for this country, we are working to be available soon all over the planet');
      }
      //--------------------------------------------------------------------------------------------


      #validamos la ubicacion geografica para el lugar donde vive la persona
      $ly_2 = $geo->localityUser($this->db->scape($data['u_nowlocation']));

      if (!isset($ly_2['country'],$ly_2['administrative_area_level_1'],$ly_2['locality'])) {
       throw new Exception('Verifica tu lugar donde vives actualmente, no es el formato permitido');
      }

      $location_2 = $this->db->select('pa_id,st_id,ci_id','jc_pais LEFT JOIN jc_states ON st_iso="'.$ly_2['administrative_area_level_1'].'" LEFT JOIN jc_city ON ci_name="'.$ly_2['locality'].'"','pa_iso="'.$ly_2['country'].'"','LIMIT 1');
      if ($location_2) {
         if (!$location_2[0][1]) {
             throw new Exception('We are sorry ...State not allowed');
           }  
      }else{
         throw new Exception('We are sorry ... is not yet available for this country, we are working to be available soon all over the planet');
      }
     
   } catch (Exception $e) {
     return ['success'=>0,'message'=>$e->getMessage()];
   }
   #procedimientos comunes
     if ($location_2[0][2] == NULL) {
        $c=['ci_state'=>$ly_2['administrative_area_level_1'],'ci_name'=>$ly_2['locality']];
        $this->db->insert('jc_city',$c);
        $u_city = $this->db->lastInsertId();
     }else{
        $u_city = $location_2[0][2];
     }



        //----------------------------------------------------------
        $fec_nac = $date[1].'-'.$date[0].'-'.$date[2];
        //var_dump($fec_nac);die();
        //declaracion de variables para su actualizacion en la bd
    
        $e = array(
          'u_sexo'     => intval($data['u_gender']),
          'u_fecnac'   => $date[2].'-'.$date[0].'-'.$date[1],
          'u_pais'     => intval($location_2[0][0]),
          'u_state'    => intval($location_2[0][1]),
          'u_city'     => intval($u_city),
          'u_time_log' => time(),
          'u_estado' => 1,
          'u_option' => intval($data['u_option']),
          'u_preference' => intval($data['u_preference']),
          'u_zodiacal' => (new Zodiacal)->signeZodiacal(['data-zodiacal'=>$fec_nac])['data'],
        );
        $this->db->update('jc_usuarios',$e,'u_id='.$me.'');
        #Obtebnemos latitud longitud y time zone para la tabla de matching


        /* $itz = $geo->timeZoneUser($date[1].'-'.$date[1].'-'.$date[2],$ly_1['lat'],$ly_1['lng']);
       
         $tz = (new AstroApi)->call('/timezone/',['country_code'=>$itz['timeZoneId'],'isDst'=>true]);
         $tz = json_decode($tz,true);*/
         #preparamos array para insertar en la table jc_optmatching
         $optm = [
           'optm_id_user'=>$me,
           'optm_lnacimiento'=>$this->db->scape($data['u_oldlocation']),
           'optm_fday'   =>$date[1],
           'optm_fmes'   =>$date[0],
           'optm_fyear'  =>$date[2],
           'optm_hhora'  =>intval(trim($hora[0])),
           'optm_hmin'   =>intval(trim($hora[1])),
           'optm_sexo'   =>$data['u_gender'],
           'optm_lat'    =>$ly_1['lat'],
           'optm_lng'    =>$ly_1['lng'],
           'optm_timezone'=>0//$tz['timezone']
         ];
         $this->db->insert(' jc_optmatching',$optm);
         //--------------------------------------------------------------------------------------------------------
    

         //insersion de datos en tablas jc_formacion jc_empleo
       $empleo =[//table jc_empleo
         'emp_id_user'=>$me,  
         'emp_empresa'=>'',
         'emp_cargo'=>'',
         'emp_ciudad'=>'',
         'emp_descripcion'=>''
       ];
       $this->db->insert('jc_empleo',$empleo);

       $formacion =[//table jc_formacion
         'fm_id_user'=>$me,
         'fm_escuela'=>'',
         'fm_periodo_ini'=>0,
         'fm_periodo_end'=>0,
         'fm_terminado'=>0,
         'fm_carrera'=>'',
       ];
       $this->db->insert('jc_formacion',$formacion);

       $pbusqueda =[//table jc_pbusqueda
         'pb_id_user'=>$me,
         'pb_option'=>$data['u_option'],
         'pb_preference'=>$data['u_preference'],
         'pb_edad_ini'=>Strings::calculate_age($fec_nac)-6,
         'pb_edad_end'=>Strings::calculate_age($fec_nac)+10,
         'pb_szodiacal'=>0,//(new Zodiacal)->signeZodiacal(['data-zodiacal'=>$this->fec_nac])['data'],
         'pb_pais'=>intval($location_2[0][0]),
         'pb_state'=>0,//intval($this->location[0][1]),
         'pb_city'=>0,//$this->u_city
       ];
       $this->db->insert('jc_pbusqueda',$pbusqueda);
       //---------------------------------------------------------------
         
        //insertamos los datos preliminares de cuenta del usuario
        $ac = new Account();
        $ac->accountGenerateTransaction($me);
        $ac->infoAccountUser($me,5, time(),1, true);
         #generamos la ssssion general del aplicativo con datos incluidos de la cuenta
               if ($_SESSION[SESS_APP_ID]['u_data_profile']=='INCOMPLETE') {

               //averiguamos datos de la cuenta personal del usuario si este aun no la tiene lo redireccionamos a los planes
                $account = $this->db->select('iau_id_user,ac_name,ac_msj,ac_color,ac_favorites,ac_photos,ac_friendchat,ac_matching,uc_chatmsj,uc_fav,uc_photo,uc_friendchat,uc_match,iau_timeend','jc_infoacountuser LEFT JOIN jc_userconsumoacount ON iau_id_user=uc_id_user LEFT JOIN jc_mbacounts ON ac_id=iau_count','iau_id_user='.$me.'');
                if (false!=$account) {//adicionamos a la sesion app_id el siguiente array de la cuenta
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
                }
              $_SESSION[SESS_APP_ID]['u_data_profile']='COMPLETE';
              $_SESSION[SESS_APP_ID]['u_welcome']=0;
            }else{
              $_SESSION[SESS_APP_ID]['u_data_profile']='INCOMPLETE';
            }
         #-----------------------------------------------------------------------------------------
         
         
         return array('success' => 1, 'message' => 'registration completed');
 
  }

  //-------------------------------------------------------------

  /**
    * Metodo que permite el registro de usuarios mediante la opcion de login de las redes sociales
  */
 final public function regMultiple(array $data){
   //ddd($data);
   Helper::load('strings');
   try {
     
   
   if (!isset($data['u_email'],$data['u_name'],$data['u_lastname'])) {
     throw new Exception('We can not process your request');// no se puede procesar tu solicitud
     
   }

   if (Func::e($data['u_email'],$data['u_name'],$data['u_lastname'])) {
     throw new Exception('Please complete all fields(s)');//por favor, completa todo los campos
   }

   if (!isset($_SESSION['social'])) {
       if (!isset($data['u_pass'])) {
         throw new Exception('We can not process your request(2)');//no existe contraseña
       }

       if (empty($data['u_pass'])) {
        throw new Exception('Please complete all fields(2)');//por favor, completa todo los campos
       }
   }else{
       if ($_SESSION['social']) {
         if (!isset($data['u_slId'],$data['u_socialTipe'])) {
           throw new Exception('We can not process your request(3)');// no se puede procesar tu solicitud
         }

         if (Func::e($data['u_slId'],$data['u_socialTipe'])) {
           throw new Exception('Please complete all fields(3)');//por favor, completa todo los campos
         }
       }
   }

   if (!Strings::is_email($data['u_email'])) {
    throw new Exception('Please, write a valid email');//por favor, escriba un email válido
   }

   } catch (Exception $e) {
     return ['success'=>0,'message'=>$e->getMessage()];
   }
   //----------------------------------------------------------------------------------------------------------------------


   #consultamos si el email ya se encutra registrado // de lo contrario hacemos el respectivo registro
   $sql = $this->db->select('u_id,u_estado, iu_img as u_img','jc_usuarios lEFT JOIN jc_images_users ON iu_id_user=u_id and iu_tipo=2','u_email="'.$this->db->scape($data['u_email']).'"');
   if (false!=$sql) {
      #verificamos si es un usuario que viene de redes sociales
      if (isset($data['u_slId'])) {
        
        #verificamos si este usuario tiene registro de redes sociales 
        $sl = $this->db->select('ls_id_user,ls_tipo_social','jc_login_socials','ls_id_user='.$sql[0]['u_id'].'','LIMIT 1');
        if ($sl!=false) {

           for ($i=0; $i <count($sl) ; $i++) { 

              if ($sl[$i]['ls_tipo_social']==$data['u_socialTipe']) {//comprobamos que el id de usuario que nos da la api sea iguala  la registrada en la bd
                 if($sl[$i]['ls_id_social']==$data['u_slId']) { throw new Exception("Failed error code Id"); exit; }
              } 
           }
           
        }
        #proceso de logueo
        #generamos la session global para darle acceso al usuario, 

         $session =[
          'u_id'=>Func::codeNumber($sql[0]['u_id']),
          'u_name'=>$this->db->scape($data['u_name']),
          'u_email'=>$data['u_email'],
          'u_img'=>$sql[0]['u_img']
         ];
         
        #verificamos estado del perfil
        $sql[0]['u_estado']==5 ?  $session['u_data_profile']='INCOMPLETE' :  $session['u_data_profile']='COMPLETE';;
        #levantamos sesion de la cuenta
        if ($sql[0]['u_estado']!=5) {
      
       //averiguamos datos de la cuenta personal del usuario si este aun no la tiene lo redireccionamos a los planes
        $account = $this->db->select('iau_id_user,ac_name,ac_msj,ac_color,ac_favorites,ac_photos,ac_friendchat,ac_matching,uc_chatmsj,uc_fav,uc_photo,uc_friendchat,uc_match,iau_timeend','jc_infoacountuser LEFT JOIN jc_userconsumoacount ON iau_id_user=uc_id_user LEFT JOIN jc_mbacounts ON ac_id=iau_count','iau_id_user='.$sql[0]['u_id'].'');
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
        }#session de la cuenta
        (new Sessions)->generate_session($session);
        Func::redir();exit;#redireccionamos y logueamos
      }else{
         return ['success'=>0,'message'=>'The account you are trying to register is not available.'];
      }

   }else{
     #si no existe el email registrado en la bd recurrimos a insertar a este usuario en nuestra tabla jc_usuarios y jc_login_social
     #preparamos matriz a insertar
     $u=[
       'u_email'=>$data['u_email'],
       'u_pass'=>isset($data['u_slId']) ? '' : Strings::hash($data['u_pass']),
       'u_nombres'=>$this->db->scape($data['u_name']),
       'u_apellidos'=>$this->db->scape($data['u_lastname']),
       'u_descripcion'=>'',
       'u_sexo'=>0,
       'u_fecnac'=>'',
       'u_pais'=>0,
       'u_state'=>0,
       'u_city'=>0,
       'u_time_log'=>time(),
       'u_estado'=>5, //estado cuando el perfil del usuario no esta completo
       'u_option'=>0,
       'u_preference'=>0,
       'u_zodiacal'=>0,
       'u_keypass'=>'',
       'u_keypass_tmp'=>'',
       'u_session'=>DB_SESSION ? (time() + SESSION_TIME) : 0
     ];
     
     $this->db->insert('jc_usuarios',$u);
     $id_user = $this->db->lastInsertId();

     #preparamos matriz para insertar en  la tabla jc_login_socials
     if (isset($data['u_slId'],$data['u_socialTipe'])) {
     
       $ls = [
        'ls_id_user'=>$id_user,
        'ls_id_social'=>$data['u_slId'],
        'ls_tipo_social'=>$data['u_socialTipe']
       ];
       $this->db->insert('jc_login_socials',$ls);
     }

     #insertamos la imagen asi venga de redes sociales o de un registro natural
     if (isset($data['u_slId'],$data['u_socialTipe'])) {
         copy(implode($data['u_picture']), 'views/assets/images/users/'.$data['u_slId'].'.jpg');
         #guardamos en la bd
         //preguntamos si ya tiene imagen de perfil
           $this->db->insert('jc_images_users',['iu_id_user'=>$id_user,'iu_img'=>$data['u_slId'].'.jpg','iu_tipo'=>2]);
           $image = $data['u_slId'].'.jpg';
     }else{ 
         $image= 'no-img.jpg';
     }
     #generamos la session global para darle acceso al usuario, 

         $_SESSION[SESS_APP_ID] =[
          'u_id'=>Func::codeNumber($id_user),
          'u_name'=>$this->db->scape($data['u_name']),
          'u_email'=>$data['u_email'],
          'u_img'=>$image
         ];
      #generamos la session status u_account con el estatus pendiente, esto obliga al usuario a validar su cuenta
      # de 3 primeros meses gratis y asi seguir los procesos comunes, le ortorgamos el valor PENDENT
         $_SESSION[SESS_APP_ID]['u_data_profile']='INCOMPLETE';

         
        return ['success'=>1,'message'=>'Registration completed'];
   }

 }

 final public function complete():array{
  unset($_SESSION[SESS_APP_ID]['u_welcome']);
  return ['success'=>1];
 }
  //----------------------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
