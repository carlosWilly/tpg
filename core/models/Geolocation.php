<?php

# Seguridad
defined('INDEX_DIR') OR exit('jcode software says .i.');
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

final class Geolocation extends Models implements OCREND {
  

  private $apiGoogle;
  
  public function __construct() {
    parent::__construct();


    $this->apiGoogle = 'AIzaSyDXyWZhBuHgvIDIwvs83HTh8z1YkxOA-J0';
  }
////..............................................................................................................................................
/**
  *Metodo que permite calular longitud latitud  de google partiendo de geocodificaicon nombre
  *@param string place cadena conteniendo el lugar del cual se quiere obtener los datos ejemplo Rioja, San Martin, peru
  *@param date fecha de nacimeinto de la persona o fecha delq ue se quiere obtener el time zone en formato dd-mm-yyy
  *@param apiGoogle codigo que nos proporciona google para poder hacer las consultas a su api
  *@return array asociativo con los datos lat,lng,timezone
*/

final public function localityUser(string $place):array{
  // primero determinamos latitud y longitud
  $val =[];
  $componentForm = [  
      'street_number'=> 'short_name',
      'route'=> 'long_name',
      'political'=>'long_name',
      'locality'=> 'long_name',
      'administrative_area_level_1'=> 'short_name',
      'administrative_area_level_2'=>'long_name',
      'country'=> 'short_name',
      'postal_code'=> 'short_name'
    ];
  $lugar = str_replace(' ', '+', $place);
  $mz = Func::file_get_contents_curl('https://maps.googleapis.com/maps/api/geocode/json?address='.$lugar.'&key='.$this->apiGoogle.'');
  if ($mz['status']=='OK') {
     
         for ($i = 0; $i < count($mz['results'][0]['address_components']); $i++) {
                $addressType = $mz['results'][0]['address_components'][$i]['types'][0];
            if ($componentForm[$addressType]) {
                 $val[$addressType]=$mz['results'][0]['address_components'][$i][$componentForm[$addressType]];
            }
         }
         //insertamos latitud y longitud
         $val['lat']= $mz['results'][0]['geometry']['location']['lat'];
         $val['lng']= $mz['results'][0]['geometry']['location']['lng'];
  }
  return $val;
}
//---------------------------------------------------------------------------------------------------


/**
  *Determina el time zone basado en la latitud y longitus ademas de la fecha en timestamp
  *@param string date fecha en formato dd/mm/YYY sera convertido a unix
  *@param lat float con la latitud que se quiere determinar el time zone
  *@param lng float longitud que se queire determinar el timezone
*/
final public function timeZoneUser(string $date, float $lat, float $lng):array{
  $timestamp = strtotime($date);
  $tz = Func::file_get_contents_curl('https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$lng.'&timestamp='.$timestamp.'&key='.$this->apiGoogle.'&language=en');
  if ($tz['status']!='OK') {
      return [];
  }else{
     return $tz;
  }
}
//------------------------------------------------------------------------------------------------------

final public function validateLocation(string $route):bool{
  $var = explode(',',$route);
  return count($var)<>3 ? false : true;
}


////...............................................................................................................................
  public function __destruct() {
    parent::__destruct();
  }

}

?>
