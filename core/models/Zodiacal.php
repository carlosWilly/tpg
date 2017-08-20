<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

final class Zodiacal extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }
  //------------------------------------------------------------

  final public function signeZodiacal(array $data) : array{
         
      try {
        $z = $this->db->scape($data['data-zodiacal']);
        $d = explode('-',$z);

        $day  =$d[0]; $mes= $d[1]; $year= $d[2]; $zodiacal=NULL;

        if(($day>=21 && $mes==3) || ($day<=20 && $mes==4)){
          $zodiacal='ARIES';
        }
        if(($day>=21 && $mes==4) || ($day<=20 && $mes==5)){
          $zodiacal='TAURUS';
        }
        if(($day>=21 && $mes==5) || ($day<=21 && $mes==6)){
         $zodiacal='GEMINI';
        }
        if(($day>=21 && $mes==6) || ($day<=20 && $mes==7)){
           $zodiacal='CANCER';
        }
        if(($day>=21 && $mes==7) || ($day<=21 && $mes==8)){
          $zodiacal='LEO';
        }
        if(($day>=22 && $mes==8) || ($day<=22 && $mes==9)){
          $zodiacal='VIRGO';        
        }
        if(($day>=23 && $mes==9) || ($day<=22 && $mes==10)){
         $zodiacal='LIBRA';
        }
        if(($day>=23 && $mes==10) || ($day<=22 && $mes==11)){
          $zodiacal='SCORPIO';
        }
        if(($day>=23 && $mes==11) || ($day<=20 && $mes==12)){
          $zodiacal='SAGITARIUS';
        }
        if(($day>=21 && $mes==12) || ($day<=19 && $mes==1)){
          $zodiacal='CAPRICORN';
        }
        if(($day>=20 && $mes==1) || ($day<=18 && $mes==2)){
          $zodiacal='AQUARIUS';
        }
        if(($day>=19 && $mes==2) || ($day<=20 && $mes==3)){
          $zodiacal='PISCES';
        }
        $zo = $this->db->select('sz_id,sz_name','jc_zodiacal','sz_name="'.$zodiacal.'"');
        if (false == $zo) throw new Exception("Error Processing Request", 1);
      
        return ['success'=>1,'data'=>$zo[0][0]];
      } catch (Exception $e) {
        return ['success'=>0,'message'=>$e->getMessage()];
      }
  }
//---------------------------------------------------------------------------------

//------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}

?>
