<?php

# Seguridad
defined('INDEX_DIR') OR exit('carwill software says :s');

//------------------------------------------------

final class InfoUsers extends Models implements OCREND {

  private $user;
  private $unique;

  public function __construct() {
    parent::__construct();

    $this->user = isset($_SESSION[SESS_APP_ID]) ? Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']) : NULL;
  }
  //------------------------------------------------------------

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::SOLICITA INFORMACION DEL CHAT  PARA MOSTRAR EN LATERAL::::::::::::::::::
  final public function infoChat() : array{

      helper::load('strings');
      //var_dump();die();
      $success = 0;
      $message ='<center><i class="fa fa-comments-o fa-4x" aria-hidden="true"></i><br>No people in chat</center>'; 
      $data=[];

      $chat = $this->db->select('u_id,
                              u_estado,iu_img as u_img,CONCAT_WS(" ",u_nombres,u_apellidos) as u_nombre,
                              ch_code,
                              (CASE (select COUNT(*) FROM jc_message WHERE msj_id_user=u_id and msj_id_destino='.$this->user.' AND msj_see=0) WHEN 0 THEN "" ELSE \'<i class="fa fa-envelope c-crimson"></i>\' END)as u_mysms,
                              (select COUNT(*) FROM jc_blacklist WHERE bl_user_main='.$this->user.' and bl_user_black=u_id) as black',
                              'jc_code_chat 

                              LEFT JOIN jc_usuarios ON ch_userDos=u_id 
                              LEFT JOIN jc_images_users ON u_id=iu_id_user  and iu_tipo=2',
                              'ch_userOne='.$this->user.' HAVING black=0');
      if (false != $chat) {
          
          foreach ($chat as $key=>$c) {
            $chat[$key]['u_id']=Func::codeNumber($c['u_id']);
            $chat[$key]['u_nombre']=Func::cut($c['u_nombre'],14);
          } 
        $success = 1;
        $message ='Conection On';
    }

    return ['success'=>$success,'message'=>$message,'data'=>$chat];
  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::SOLICITA INFORMACION DE SOLICITUDES Y NOTICIAS::::::::::::::::::::::::::
  final public function infoNoticias():array{
      Helper::load('strings');
      try {
      $data = $this->db->select('
        jc_notifications.sl_id as n_id,
        u_id,
        CONCAT_WS(" ",u_nombres,u_apellidos) as n_remite,
        iu_img as u_img,
        FROM_UNIXTIME(sl_fecha,"%m/%d/%Y") as n_fecha,
        (CASE sl_tipo WHEN 0 THEN "Wants to start a conversation with you" WHEN 1 THEN "I accept your conversation request" END) as n_message,
        sl_tipo,
        sl_status',
        'jc_notifications 
        LEFT JOIN jc_usuarios on u_id=sl_id_remite
        LEFT JOIN jc_images_users on u_id=iu_id_user and iu_tipo=2',
       'sl_id_destino='.$this->user.'','ORDER BY sl_id DESC');
       foreach ($data as $key => $val) {
         $data[$key]['u_id'] = Func::codeNumber($val['u_id']);
       }
      //actualizamos estado de las notificaciones a visto
      $this->db->update('jc_notifications',['sl_visto'=>1],'sl_id_destino='.$this->user.'');
        return ['success'=>1,'data'=>$data];
    } catch (Exception $e) {
        return ['success'=>0,'message'=>$e->getMessage()];
    }
  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::SOLICITA INFORMACION DE SOLICITUDES Y NOTICIAS::::::::::::::::::::::::::
  final public function infoSearchOption() : array{
      Helper::load('strings');
      $success = 0;
      $message ='<center><i class="fa fa-comments-o fa-4x" aria-hidden="true"></i><br>No Information</center>'; 
      $data=[];
    
      $solicita = $this->db->select('sl_id,u_id,CONCAT(u_nombres," ",u_apellidos) as u_name,iu_img,sl_fecha,sl_tipo','jc_notifications LEFT JOIN jc_usuarios ON sl_id_remite=u_id LEFT JOIN jc_images_users ON u_id=iu_id_user and iu_tipo=2','sl_id_destino='.$this->user.' and sl_status!=1','ORDER BY sl_id DESC');
      if (false != $solicita) {
      
      foreach ($solicita as $a) {

        switch ($a['sl_tipo']) {
          case 0:
            $msj ='Wants to start a conversation with you';//quiere inciar conversacion contigo;
            $buttons = '<button data="'.Func::codeNumber($a['sl_id']).'" class="jcode-confirmNoti btn btn-primary btn-sm">to accept</button> <button  data="'.Func::codeNumber($a['sl_id']).'" class="jcode-cancelNoti btn btn-default btn-sm">Cancel</button>';
            break;
          
          case 1:
            $msj =' I accept your conversation request';//acepto tu solicitud de conersacion;
            $buttons='';
            break;
          case 2:
            $msj =' Rejected your conversation request';//rechazo tu solicitud de converzacion;
            $buttons='';
            break;
        }
        $data[]=[
             'u_id'=>Func::codeNumber($a['u_id']),
             'u_name'=>$a['u_name'].' '.$msj,
             'u_imagen'=>$a['iu_img'],
             'u_fecha'=>Strings::amigable_time($a['sl_fecha']),
             'u_buttons'=>$buttons
        ];
      }
      $success = 1;
      $message ='Successfull';

      //actualizamos estado de las notificaciones a visto
      $this->db->update('jc_notifications',['sl_visto'=>1],'sl_id_destino='.$this->user.'');
      }

    return ['success'=>$success,'message'=>$message,'data'=>$data];
  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::MUESTRA LAS OCIONES DE BUSQUEDA DEL USUARIO EN LA COLUMNA IZQUIERA DEL TEMPLATE::::::::::::::

final public function optionSearch(bool $type):array{
  $osearch = $this->db->select('*,(SELECT st_iso FROM jc_states WHERE st_id= pb_state) as state_iso','jc_pbusqueda','pb_id_user='.$this->user.'','LIMIT 1');


  if (false != $osearch) {
    if ($type) {
         //determinamos los estados
          $aestate = $this->db->select('st_id,st_iso,st_name','jc_states');
          //determinamos las ciudaddes
          $acity = $this->db->select('ci_id,ci_name','jc_city','ci_state="'.$osearch[0]['state_iso'].'"');
          //selecccionamos los signos
           $signs = $this->db->select('*','jc_zodiacal');

          $dsingle =[ 
            'option'     => $osearch[0]['pb_option'],
            'eini'     => $osearch[0]['pb_edad_ini'],
            'eend'     => $osearch[0]['pb_edad_end'],
            'preference' => $osearch[0]['pb_preference'],
            'signo'      => $osearch[0]['pb_szodiacal'],
            'state'      => $osearch[0]['pb_state'],
            'city'       => $osearch[0]['pb_city']
          ];

        return ['success'=>1,'dsingle'=>$dsingle,'aestate'=>$aestate,'acity'=>$acity,'signs'=>$signs];
    }else{
    //opcion de busqueda general en el sitio
          switch ($osearch[0]['pb_option']) {
            case 1:
              $option = 'looking for a friendship';
              break;
            
            case 2:
              $option = 'looking for a relationship';
              break;

            case 3:
              $option = 'looking for a something casual';
              break;
          }
          //preferencia sexual
          switch ($osearch[0]['pb_preference']) {
            case 1:
              $preference = 'With a man';
              break;
            
            case 2:
              $preference = 'with a woman';
              break;
            case 3:
              $preference = 'with both';
              break;
          }
          //tipo de signo
          if ($osearch[0]['pb_szodiacal']==0) {
            $signo = 'All signs';
          }else{
            $sz = $this->db->select('sz_name','jc_zodiacal','sz_id='.intval($osearch[0]['pb_szodiacal']).'','LIMIT 1');
    
            $signo = 'Only '.ucwords($sz[0][0]);
          }
          
          //busqueda por zona geografica
          //tipo de signo
          if ($osearch[0]['pb_state']==0) {
            $pb_state = 'All States';
          }else{
            $sz = $this->db->select('st_name','jc_states','st_id='.intval($osearch[0]['pb_state']).'','LIMIT 1');
            $pb_state = 'Estate '.ucwords($sz[0][0]);
          }

          //tipo de signo
          if ($osearch[0]['pb_city']==0) {
            $pb_city = 'All Citys';
          }else{
            $sz = $this->db->select('ci_name','jc_city','ci_id='.intval($osearch[0]['pb_city']).'','LIMIT 1');
            $pb_city = 'City '.ucwords($sz[0][0]);
          }
      //----------------------------------------------------------------------
      };
      $redad0 = intval($osearch[0]['pb_edad_ini']);
      $redad1 = intval($osearch[0]['pb_edad_end']);

  
    //----------------------------------------
    return ['opcode'=>$osearch[0]['pb_option'],'option'=>$option,'preference'=>$preference,'redad0'=>$redad0,'redad1'=>$redad1,'signo'=>$signo,'pb_state'=>$pb_state,'pb_city'=>$pb_city];
  }

  return [];
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::MUESTRA INFORMACION ACERCA DEL USARIO POR LOS GENERAL PARA EDITAR::::::::::::::::::::::::::::

final public function infoUser( array $data = NULL):array{

  try {
   if ($data != NULL) {
        if (!Func::decodeNumber($data['token-user'])) {
              throw new Exception("Error of cadification");
        }
     }  
  } catch (Exception $e) {
    return ['success'=>0,'message'=>$e->getMessage()];
  }

  $id_user = $data != NULL ? Func::decodeNumber($data['token-user']) : $this->user; 


  $user = $this->db->select('u_nombres,u_apellidos,u_email,u_sexo,ge_name,DATE_FORMAT(u_fecnac,"%m/%d/%Y") as u_fecnac,CONCAT(optm_hhora,":",optm_hmin) as u_horan','jc_usuarios 
    LEFT JOIN jc_genero ON ge_id=u_sexo LEFT JOIN jc_optmatching ON u_id=optm_id_user',
    'u_id='.$id_user.'','LIMIT 1');

  return ['success'=>1,'data'=>$user];
  
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::MUESTRA INFORMACION SOBRE NOTIFICACION Y MENSAJES TOP MENU USUARIO::::::::::::::::::::::::::::

final public function infoUserTop():array{
  $noti = $this->db->select('count(*)','jc_notifications','sl_id_destino='.$this->user.' and sl_visto=0');
  $msj = $this->db->select('count(*)','jc_message','msj_id_destino='.$this->user.' and msj_see=0');
  return ['noti'=>$noti[0][0],'msj'=>$msj[0][0]];
  
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::




//------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
