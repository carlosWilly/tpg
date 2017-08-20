<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software dice :)');

//------------------------------------------------

final class ProfileInfo extends Models implements OCREND {

  private $user;

  public function __construct() {
    parent::__construct();

    if (!isset($_SESSION[SESS_APP_ID])) exit;

    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
  }
  //------------------------------------------------------------

  final public function ProfileGrall(array $data = NULL) : array{
     Helper::load('strings');
     try {
       if (!Func::decodeNumber($data['data-profile'])) throw new Exception("Error Processing Request", 1);
       

        $token_user = Func::decodeNumber($data['data-profile']);
        $data = $this->db->select('
                  u_id as p_id,
                  IFNULL(iu_img,"reg.jpg") as p_image,
                  IFNULL((SELECT iu_img FROM jc_images_users WHERE iu_id_user=u_id and iu_tipo=2),"no-img.jpg") as p_uimg,
                  CONCAT_WS(" ",u_nombres,u_apellidos) as p_name,
                  sz_icon as p_zicon,
                  sz_name p_zname,
                  ge_name p_sexo,
                  DATE_FORMAT(u_fecnac,"%m/%d/%Y") as p_fnac,
                  DATE_FORMAT(u_fecnac,"%d/%m/%Y" )as p_datesingle,
                  pa_name as p_pais,
                  st_name as p_state,
                  ci_name as p_city,
                  emp_empresa,
                  emp_cargo,
                  emp_ciudad,
                  emp_descripcion,
                  fm_carrera,
                  fm_escuela,
                  fm_periodo_ini,
                  fm_periodo_end,
                  fm_terminado,
                  u_descripcion as p_descrip',
             'jc_usuarios 
                  LEFT JOIN jc_images_users ON u_id=iu_id_user and iu_tipo=1 
                  LEFT JOIN jc_genero ON u_sexo=ge_id 
                  LEFT JOIN jc_pais ON u_pais=pa_id 
                  LEFT JOIN jc_states ON u_state=st_id 
                  LEFT JOIN jc_city ON u_city=ci_id 
                  LEFT JOIN jc_zodiacal ON u_zodiacal=sz_id 
                  LEFT JOIN jc_empleo ON u_id=emp_id_user 
                  LEFT JOIN jc_formacion ON u_id=fm_id_user',
              'u_id='.$token_user.'','LIMIT 1');

            if($data == false) throw new Exception("No data to display", 1);
            //var_dump(Func::codeNumber($data[0]['p_id']));die();
            $data[0]['p_id']=Func::codeNumber($data[0]['p_id']);
            $data[0]['p_age']=Strings::calculate_age($data[0]['p_datesingle']);
          //ddd($data[0]['p_image']);
         return ['success'=>1,'data'=>$data];
      } catch (Exception $e) {
         return ['success'=>0,'message'=>$e->getMessage()]; 
      }


  }
//---------------------------------------------------------------------------------

//------------------------------------------------------------

  final public function profilePhotos(array $data,bool $tipo) : array{
      
   if (!Func::decodeNumber($data['data-require'])) {
      $success=0;
      $message='Error of codifications';
      $data=[];
   }else{

      $type = $tipo == true ? 'LIMIT 8': NULL;
      $token_user = Func::decodeNumber($data['data-require']);
      $p = $this->db->select('iu_id,iu_img','jc_images_users','iu_id_user='.$token_user.'  and iu_tipo=0','ORDER BY iu_id DESC '.$type.'');
      if (false != $p ) {
        
        $data = $p;
        $success=1;
        $message='success';

      }else{

        $success=0;
        $message='No photos yet posted';
        $data=[];
      }
        
   }
    return ['success'=>$success,'message'=>$message,'data'=>$data];

  }
//---------------------------------------------------------------------------------

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::Procedimientos para el manejo de la parte de empleo de los usuarios:::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  final public function empleoInfo() : array{

      $success=0;
      $message='not successfull';
      $data=[];

      $p = $this->db->select('emp_empresa,emp_cargo,emp_ciudad,emp_descripcion','jc_empleo','emp_id_user='.$this->user.'');
      if (false != $p ) {
        
        $data = $p;
        $success=1;
        $message='success';
      }  

      return ['success'=>$success,'message'=>$message,'data'=>$data];

  }

  //funcion que edita los parametros de empleo
  final public function empleoEdit(array $data) {

      if (!Func::all_full($data)) {
          $success = 0;
          $message = 'Please complete all fields';
      }else{

        $e = array( 
          'emp_empresa'=>$this->db->scape($data['jc-emp-empresa']),
          'emp_cargo'=>$this->db->scape($data['jc-emp-cargo']),
          'emp_ciudad'=>$this->db->scape($data['jc-emp-ciudad']),
          'emp_descripcion'=>$this->db->scape($data['jc-emp-descrip'])
        );

        $this->db->update('jc_empleo',$e,'emp_id_user='.$this->user.'');
        $success =1;
        $message ='Upload Successful';
      }

      return ['success'=>$success,'message'=>$message];

  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::Procedimientos para el manejro de informacion respecto a la formacion del usuario:::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  final public function fomacionInfo() : array{

      $success=0;
      $message='not successfull';
      $data=[];

      $p = $this->db->select('fm_escuela,fm_periodo_ini,fm_periodo_end,fm_terminado,fm_carrera','jc_formacion','fm_id_user='.$this->user.'');
      
      if (false != $p ) {
        
        $data = $p;
        $success=1;
        $message='success';
      }  

      return ['success'=>$success,'message'=>$message,'data'=>$data];

  }

  //funcion que edita los parametros de formacion
  final public function formacionEdit(array $data) {
      
      if (!Func::all_full($data)) {
          $success = 0;
          $message = 'Please complete all fields';
      }elseif(strlen($data['jc-fm-since'])<>4 || strlen($data['jc-fm-until'])<>4){
          $success = 0;
          $message = 'Study periods not allowed';
      }elseif(intval($data['jc-fm-cl']) != 1 && intval($data['jc-fm-cl']) != 2){
         $success = 0;
         $message = 'State of studies not allowed';
      }else{

        $e = array( 
          'fm_escuela'=>$this->db->scape($data['jc-fm-ename']),
          'fm_periodo_ini'=>intval($this->db->scape($data['jc-fm-since'])),
          'fm_periodo_end'=>intval($this->db->scape($data['jc-fm-until'])),
          'fm_terminado'=>intval($this->db->scape($data['jc-fm-cl'])),
          'fm_carrera'=>$this->db->scape($data['jc-fm-carrera'])
        );

        $this->db->update('jc_formacion',$e,'fm_id_user='.$this->user.'');
        $success =1;
        $message ='Upload Successful';
      }

      return ['success'=>$success,'message'=>$message];

  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::Procedimientos para el manejo de la descricion del usuario::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  final public function descripcionInfo() : array{

      $success=0;
      $message='not successfull';
      $data=[];

      $p = $this->db->select('u_descripcion','jc_usuarios','u_id='.$this->user.'');
      
      if (false != $p ) {
        
        $data = $p;
        $success=1;
        $message='success';
      }  

      return ['success'=>$success,'message'=>$message,'data'=>$data];

  }

  //funcion que edita los parametros de formacion
  final public function descripcionEdit(array $data) {

      if (!Func::all_full($data)) {
          $success = 0;
          $message = 'Please complete all fields';
      }else{

        $e = array( 
          'u_descripcion'=>$this->db->scape($data['jc-u-descrip']),
          
        );

        $this->db->update('jc_usuarios',$e,'u_id='.$this->user.'');
        $success =1;
        $message ='Upload Successful';
      }

      return ['success'=>$success,'message'=>$message];

  }
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
