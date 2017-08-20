<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

final class Search extends Models implements OCREND {

  private $user;

public function __construct() {
    parent::__construct();

    $this->user = Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id']);
}
//------------------------------------------------------------

final public function searchTop(array $data) : array{

      $success=0;
      $message='No matches found';
      $da = [];

        
      if ( $data['data-search']=='') return ['success'=>$success,'message'=>$message,'data'=>$da];

        

        $sstring = $this->db->scape($data['data-search']);

        //paginacion
        $dataTotal=  $this->db->select('COUNT(*)','jc_usuarios','u_nombres','LIKE "%'.$sstring.'%" or u_apellidos LIKE "%'.$sstring.'%"');
        $indicator = isset($data['page']) ? true : false;
        $pager = Func::scrollPagination($indicator,20,$dataTotal[0][0],'limitTopSearch');
        
        //------------------------------
        $search = $this->db->select('u_id,iu_img,CONCAT(u_nombres," ",u_apellidos) as u_nombre,u_fecnac,sz_name,(select count(*) FROM jc_blacklist WHERE bl_user_black=u_id) as black',
          'jc_usuarios 
           LEFT JOIN jc_zodiacal ON u_zodiacal=sz_id 
           LEFT JOIN jc_images_users ON u_id=iu_id_user  and iu_tipo=2',

           'u_nombres LIKE "%'.$sstring.'%" or u_apellidos LIKE "%'.$sstring.'%" HAVING black!=1','LIMIT '.$pager[0].','.$pager[1].'');
      
        
        if (false != $search) {
           foreach ($search as $u) {
              $d[]=[
                'b_id'=>Func::codeNumber($u['u_id']),
                'b_name'=>ucwords($u['u_nombre']),
                'b_image'=>$u['iu_img'],
                'b_zodiacal'=>$u['sz_name'],
                'b_edad'=>Strings::calculate_age(Func::dateSpanish($u['u_fecnac']))
              ];
            }
          $success=1;
          $message='Se han encontrado coincidencias';
          $da = $d;
        }
     
        return ['success'=>$success,'message'=>$message,'data'=>$da];

  }

private function fc(string $da):int{
  Helper::load('strings');
  return  Strings::calculate_age(Func::dateSpanish($da));  
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::Opciones de busqueda del usuario CONTROLADOR searchController:::::::::::::::::::::::::::
final public function optionSearch($data = []):array{
        Helper::load('strings');
        $where ='';
        try {
            //----------------------------------------
            //Nombre
           if(isset($data['s_name']) and !empty($data['s_name'])){
             $where .= ' and CONCAT_WS(" ",u_nombres,u_apellidos) LIKE  "%'.$this->db->scape($data['s_name']).'%" ';
           }

           //signo zodiacal
           if (isset($data['s_szinc']) and !empty($data['s_szinc']) and $data['s_szinc']>=0) {
              $where.=  $data['s_with']>0 ? ' and u_zodiacal='.intval($data['s_szinc']).'' : '';
           }
            //locacion
           if (isset($data['s_location']) and !empty($data['s_location']) and $data['s_location']>=0) {
               $where.=  $data['s_location']>0 ? ' and u_state='.intval($data['s_location']).' ' : '';
           }
          //opciones
           if (isset($data['s_option']) and !empty($data['s_option']) and $data['s_option']>0) {
             $where .=' and pb_option='.intval($data['s_option']).'';
           }
          //preferencias
           if (isset($data['s_with']) and !empty($data['s_with']) and $data['s_with']>0) {
              $where.= $data['s_with']<3 ? ' and u_sexo='.intval($data['s_with']).' ' : ' and u_sexo=1 or u_sexo=2';
           }
           //var_dump($where);die();
            $data = $this->db->select('u_id,
                CONCAT_WS(" ",u_nombres,u_apellidos) AS u_name,
                YEAR(CURDATE())-YEAR(u_fecnac)+IF(DATE_FORMAT(CURDATE(),\'%m-%d\')>DATE_FORMAT(u_fecnac,\'%m-%d\'),0,-1)AS u_edad,
                IFNULL(iu_img,"no-img.jpg") as u_img,
                st_name as u_estado,
                sz_name as u_signo,
                pb_edad_end,
                pb_edad_ini,
                sz_icon as u_zicon',
                'jc_usuarios
                LEFT JOIN jc_pbusqueda on u_id=pb_id_user
                LEFT JOIN jc_states on u_state=st_id
                LEFT JOIN jc_zodiacal on u_zodiacal=sz_id
                LEFT JOIN jc_images_users on u_id = iu_img and iu_tipo=2',
                'u_id!='.$this->user.' '.$where.' HAVING u_edad>=pb_edad_ini and u_edad <= pb_edad_end ');
          return ['success'=>1,'data'=>$data];
        } catch (Exception $e) {
          return ['success'=>0,'message'=>$e->getMessage()];
        }
}

final public function PB($data):array{
      try {
        $data = $this->db->select('*','jc_pbusqueda','pb_id_user='.$this->user.'');
        if(false == $data) throw new Exception("No search preferences", 1);
        return ['success'=>1,'data'=>$data];
      } catch (Exception $e) {
        return ['success'=>0,'message'=>$e->getMessage()];
      }
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::Actualiza las opciones de busqueda del usuario::::::::::::::::::::::::::::::::
final public function updateOptionSearch(array $data):array{

        Helper::load('strings');
   
        try {
           if (!isset($data['jc-option'],$data['jc-preference'],$data['jc-eini'],$data['jc-eend'],$data['jc-zodiacal'],$data['jc-state'],$data['jc-city'])) {
             throw new Exception('Error not exist vars');
             
           }

           if (!Func::all_full($_POST)) {
             throw new Exception('Complete all fields');
             
           }

           if ($data['jc-option'] < 1 || $data['jc-option'] >3) {
            throw new Exception('Option not allowed');
           }

           if ($data['jc-preference'] < 1 || $data['jc-preference'] >3) {
             throw new Exception('Preference not allowed');
           }

           if ( ($data['jc-eini'] < 14 || $data['jc-eini'] > 65) && ($data['jc-eend'] < 14 || $data['jc-eend'] > 65) ) {
              throw new Exception('Age range not allowed');
           }

           if ( $data['jc-zodiacal'] < 0 || $data['jc-zodiacal'] > 12) {
              throw new Exception('Zodiacal not allowed');
           }
           if ( $data['jc-state'] < 0 || $data['jc-state'] > 50) {
              throw new Exception('State not allowed');
           }

           if ( $data['jc-city'] < 0) {
              throw new Exception('City not allowed');
           }

        } catch (Exception $e) {
          return array('success' => 0, 'message' => $e->getMessage());
        }
        
        $e = [
          'pb_option'=>intval($data['jc-option']),
          'pb_preference'=>intval($data['jc-preference']),
          'pb_edad_ini'=>intval($data['jc-eini']),
          'pb_edad_end'=>intval($data['jc-eend']),
          'pb_szodiacal'=>intval($data['jc-zodiacal']),
          'pb_pais'=>1,
          'pb_state'=>intval($data['jc-state']),
          'pb_city'=>intval($data['jc-city'])
        ];
        $this->db->update('jc_pbusqueda',$e,'pb_id_user='.$this->user.'');
        
        //actualizamos la tabla principal de usuarios en option y prefrence
        $this->db->update('jc_usuarios',['u_option'=>intval($data['jc-option']),'u_preference'=>intval($data['jc-preference'])],'u_id='.$this->user.'');

    return ['success'=>1,'message'=>'Updated successfully'];
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//..........................................................
final public function AjaxCityByState(array $state):array{

    $state = $this->db->select('st_iso','jc_states','st_id='.intval($state['token']).'');
    $citys = $this->db->select('ci_id,ci_name','jc_city','ci_state="'.$state[0][0].'"');
    return $citys != false ? ['success'=>1,'data'=>$citys]  : ['success'=>0,'data'=>[]];
}

final public function userPreferences(array $data):array{
  try {
    if (!isset($data['u_token'])) {
      throw new Exception('No vars');
      
    }

    if (empty($data['u_token'])) {
        throw new Exception('empty fields');
        
    }

    if (!Func::decodeNumber($data['u_token'])) {
        throw new Exception('Codification errno');
        
    }

    $token = Func::decodeNumber($data['u_token']);
  } catch (Exception $e) {
    return ['success'=>0,'message'=>$e->getMessage()];
  }
  //procedimientos nativos nativos del metodo
  $sql = $this->db->select('
    u_nombres as us_name,pb_edad_ini as us_eini,pb_edad_end as us_eend,pb_pais as us_pais, 
    IFNULL(iu_img,"no-img.jpg") as us_img,
    (CASE pb_option WHEN  1  THEN \'Looking for a friendship\'
                WHEN  2  THEN \'Looking for a relationship\'
                WHEN  3  THEN \'Looking for a something casual\'
                ELSE \'Undefined\' END) as us_options,
    (CASE pb_preference 
                    WHEN  1  THEN \'Whit a men\'
                    WHEN  2  THEN \'whit a woman\'
                    WHEN  3  THEN \'Wthit a Both\'
                    ELSE \'Undefined\' END) as us_preferences,
    (CASE WHEN pb_szodiacal>0  THEN (SELECT sz_name FROM jc_zodiacal WHERE sz_id=pb_szodiacal) ELSE \'All signs\' END) as us_zodiacal, 
    (CASE WHEN pb_state>0  THEN (SELECT st_name FROM jc_states WHERE st_id=pb_state) ELSE \'All states\' END) as us_estado, 
    (CASE WHEN pb_city>0  THEN (SELECT ci_name FROM jc_city WHERE ci_id=pb_city) ELSE \'All Citys\' END) as us_city',
    'jc_usuarios 

    LEFT JOIN jc_images_users ON u_id=iu_id_user and iu_tipo=2 
    LEFT JOIN jc_pbusqueda on u_id=pb_id_user',

    'u_id='.intval($token).'','LIMIT 1');
  //var_dump($sql);die();
  return ['success'=>1,'data'=>$sql !=false ? $sql : [] ];
}
 
 
//------------------------------------------------------------------------------
  public function __destruct() { 
    parent::__destruct();
  }

}

?>
