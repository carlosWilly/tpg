<?php

# Seguridad
defined('INDEX_DIR') OR exit('jcode software says .i.');

//------------------------------------------------

final class Users extends Models implements OCREND {
  protected $user;
  
  

  public function __construct() {
    parent::__construct();
    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }
 //----------------------------------------
  final public function updateInfoUsers(array $data):array{

    //var_dump($data);die();
    Helper::load('strings');


    try {
      if (!Func::all_full($_POST)) {
         throw new Exception("Complete all fields");
         
      }
      //VALIDACION DE NOMBRES Y APELLIDOS
      if (isset($data['u_name'],$data['u_lastname']) ) {
          $e =['u_nombres'=>$this->db->scape($data['u_name']),'u_apellidos'=>$this->db->scape($data['u_lastname'])];
          $_SESSION[SESS_APP_ID]['u_name'] = $this->db->scape($data['u_name']);
      }
      //VALIDACION DE EMAIL
      if (isset($data['u_email'])) {
          if (!Strings::is_email($data['u_email'])) {
             throw new Exception("Email  not allowed");
          }
          //verficamos si el email exite
          $on = $this->db->select('count(*)','jc_usuarios','u_email="'.$data['u_email'].'"');
          if ($on[0][0] >=1) {
           throw new Exception("Email is already registered");
          }

          $e =['u_email'=>$this->db->scape($data['u_email'])];

      }
      //VALIDACION DE COONTRASEÑA
      //VALIDACION DE SEXO
      if (isset($data['u_npass'],$data['u_cpass'])) {
          if ($data['u_npass'] != $data['u_cpass']) {
             throw new Exception("The new passwords do not match");
          }

          /*$pass = $this->db->select('u_pass','jc_usuarios','u_id='.$this->user.'');
          if (!Strings::chash($pass[0][0],$data['u_apass'])) {
           throw new Exception("Your current password is not correct");
          }*/
          if (strlen($data['u_npass']) < 6) {
            throw new Exception("The password must be at least 6 characters");
          }
          $e =['u_pass'=>Strings::hash($data['u_npass'])];
         
      }
      //VALIDACION DE SEXO
      if (isset($data['u_sexo'])) {
          if ($data['u_sexo'] < 1 || $data['u_sexo'] >2) {
             throw new Exception("Sexo  not allowed");
          }
          $e =['u_sexo'=>intval($data['u_sexo'])];
      }
      //VALIDADCION DE DIA MES ANIO HORA
      if (isset($data['u_day'],$data['u_mes'],$data['u_year'],$data['u_hora'])) {
          if ($data['u_day'] < 1 || $data['u_day'] >31) {
             throw new Exception("Day  not allowed");
          }

          if ($data['u_mes'] < 1 || $data['u_mes'] >12) {
             throw new Exception("Month  not allowed");
          }

          if ($data['u_year'] < 1965 || $data['u_year'] >date('Y',time())) {
             throw new Exception("Year  not allowed");
          }

          $age = Strings::calculate_age(intval($data['u_day']).'-'.intval($data['u_mes']).'-'.intval($data['u_year']));
          if ($age < 14 || $age >65) {
            throw new Exception("Only of 14 a 65 years"); 
          }
          //validadcion hora
          $u_hora = explode(':',$data['u_hora'],2);
          if(count($u_hora)!=2){
             throw new Exception("Check the format of the times"); 
          }

          $h=intval($u_hora[0]);$m=intval($u_hora[1]);

         
          if ($h < 0 || $h > 23) {
            throw new Exception('<b>Error:</b> There is a problem with the time check that it has the format of 24 hours ejmplo 08:25');
          }elseif($m < 0 || $m > 60){
            throw new Exception('<b>Error:</b> There is a problem with the time check that it has the format of 24 hours ejmplo 08:25');
          }
          //conformacion de array de actualizacion
          $e =['u_fecnac'=>intval($data['u_year']).'-'.intval($data['u_mes']).'-'.intval($data['u_day'])];
          //actualizamos el sogno zodiacal
          $fecha = intval($data['u_day']).'-'.intval($data['u_mes']).'-'.intval($data['u_year']);
          //calculo de signo
         
          $signo = (new Zodiacal)->signeZodiacal(['data-zodiacal'=>$fecha]);
       
          $this->db->update('jc_usuarios',['u_zodiacal'=>$signo['data']],'u_id='.$this->user.'');

      }
    } catch (Exception $e) {
      return ['success'=>0,'message'=>$e->getMessage()];
    }
    //------------------------------------------------------------------
    $this->db->update('jc_usuarios',$e,'u_id='.$this->user.'');
    //OJOS QUE HAY QUE ACTUALIZAR CIERTAS VARIABLES PROVENIENTES DE LA API ASTROLOGIA YA QUE EN BASE A ESO SE  HACE EL MATCHING
    //actualizamo datos en las opciones de matching en la api
    if(isset($e['u_fecnac'])){
      $om=[
          'optm_fday'=>$data['u_day'],
          'optm_fmes'=>$data['u_mes'],
          'optm_fyear'=>$data['u_year'],
          'optm_hhora'=>$u_hora[0],
          'optm_hmin'=>$u_hora[1],
      ];
      $this->db->update('jc_optmatching',$om,'optm_id_user='.$this->user.'');
    }
    return ['success'=>1,'message'=>'Updated successfully'];

  }
  //----------------------------------------------------------------
  final public function nowUbicationUpdate(array $data):array{
    try {
      if (!isset($data['nowUbication'])) {
        throw new Exception("faile");
        
      }

      if (empty($data['nowUbication'])) {
         throw new Exception("faile(2)");
         
      }
      //----------------------------------------------------------------------
        $geo = new Geolocation;

        if (!$geo->validateLocation($data['nowUbication'])) {
          throw new Exception('The location format is incorrect. Your location must have the following format: City, State, Country(1).');//El formato de ubicación es incorrecto. Su ubicación debe tener el siguiente formato: Ciudad, Estado, País.
        }


       #validamos la ubicacion geografica para el lugar de nacimiento
        $ly_1 = $geo->localityUser($this->db->scape($data['nowUbication']));

        if (!isset($ly_1['country'],$ly_1['administrative_area_level_1'],$ly_1['locality'])) {
         throw new Exception('The location format is incorrect. Your location must have the following format: City, State, Country(2).');//El formato de ubicación es incorrecto. Su ubicación debe tener el siguiente formato: Ciudad, Estado, País.
        }

        $location_1 = $this->db->select('pa_id,st_iso,ci_id,st_id','jc_pais LEFT JOIN jc_states ON st_iso="'.$ly_1['administrative_area_level_1'].'" LEFT JOIN jc_city ON ci_name="'.$ly_1['locality'].'"','pa_iso="'.$ly_1['country'].'"','LIMIT 1');
        //var_dump($location_1[0][3]);die();
        if ($location_1) {
           if (!$location_1[0][1]) {
               throw new Exception('We are sorry ...State not allowed');
             }  
        }else{
           throw new Exception('We are sorry ... is not yet available for this country, we are working to be available soon all over the planet');//Lo sentimos ... todavía no está disponible para este país, estamos trabajando para estar disponibles pronto en todo el planeta

        }
        
         #procedimientos comunes
         if ($location_1[0][2] == NULL) {
            $c=['ci_state'=>$ly_1['administrative_area_level_1'],'ci_name'=>$ly_1['locality']];
            $this->db->insert('jc_city',$c);
            $u_city = $this->db->lastInsertId();
         }else{
            $u_city = $location_1[0][2];
         }
       


      //-----------------------------------------------------------------------
    } catch (Exception $e) {
      return['success'=>0,'message'=>$e->getMessage()];
    }
    #resutn
    #matriz para actualizar ubicacion actual del usuario
        $a =[
          'u_pais'=>$location_1[0][0],
          'u_state'=>$location_1[0][3],
          'u_city'=>$u_city
        ];
        $this->db->update('jc_usuarios',$a,'u_id='.$this->user.'');
    return ['success'=>1,'message'=>'Your current location has been updated correctly.'];//Su ubicación actual se ha actualizado correctamente.
  }
 
  //---------------------------------------


  public function __destruct() {
    parent::__destruct();
  }

}

?>
