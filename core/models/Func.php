<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Func {

  /**
    * Calcula el porcentaje de una cantidad
    *
    * @param int $por: El porcentaje a evaluar, por ejemplo 1, 20, 30 % sin el "%", sólamente el número
    * @param int $n: El número al cual se le quiere sacar el porcentaje
    *
    * @return int con el porcentaje correspondiente
  */
  final public static function percent(int $por, int $n) : int {
    return $n * ($por / 100);
  }

  //------------------------------------------------

  /**
    * Da unidades de peso a un integer según sea su tamaño asumida en bytes
    *
    * @param int $size: Un entero que representa el tamaño a convertir
    *
    * @return string del tamaño $size convertido a la unidad más adecuada
  */
  final public static function convert(int $size) : string {
      $unit = array('bytes','kb','mb','gb','tb','pb');
      return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  //------------------------------------------------

  /**
    * Redirecciona a una URL
    *
    * @param string $url: Sitio a donde redireccionará
    *
    * @return void
  */
  final public static function redir(string $url = URL) {
    header('location: ' . $url);
  }

  //------------------------------------------------

  /**
    * Retorna la URL de un gravatar, según el email
    *
    * @param string  $email: El email del usuario a extraer el gravatar
    * @param int $size: El tamaño del gravatar
    * @return string con la URl
  */
   final public static function get_gravatar(string $email, int $size = 32) : string  {
       return 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . (int) abs($size);
   }

   //------------------------------------------------

   /**
     * Alias de Empty, más completo
     *
     * @param midex $var: Variable a analizar
     *
     * @return true si está vacío, false si no, un espacio en blanco cuenta como vacío
   */
   final static function emp($var) : bool {
     return empty(trim(str_replace(' ','',$var)));
   }

   //------------------------------------------------

   /**
     * Aanaliza que TODOS los elementos de un arreglo estén llenos, útil para analizar por ejemplo que todos los elementos de un formulario esté llenos
     * pasando como parámetro $_POST
     *
     * @param array $array, arreglo a analizar
     *
     * @return true si están todos llenos, false si al menos uno está vacío
   */
   final static function all_full(array $array) : bool {
     foreach($array as $e) {
       if(self::emp($e) and $e != '0') {
         return false;
       }
     }
     return true;
   }

   //------------------------------------------------

   /**
     * Alias de Empty() pero soporta más de un parámetro
     *
     * @param infinitos parámetros
     *
     * @return true si al menos uno está vacío, false si todos están llenos
   */
    final public static function e() : bool  {
      for ($i = 0, $nargs = func_num_args(); $i < $nargs; $i++) {
        if(self::emp(func_get_arg($i)) and func_get_arg($i) != '0') {
          return true;
        }
      }
      return false;
    }

    //------------------------------------------------

       //------------------------------------------------

   /**
     * Invierte una fecha de formato Y/m/d a d/m/Y;
     *
     * @param string formato Y/m/d
     *
     * @return String con fecha en formato  d/m/Y
   */
    final public static function dateSpanish(string $date) : string  {

      $d = explode('-',$date);

      $newDate = $d[2].'-'.$d[1].'-'.$d[0];

      return $newDate;
    }

    //------------------------------------------------

    /**
      * Alias de date() pero devuele días y meses en español
      *
      * @param string $format: Formato de salida (igual que en date())
      * @param int $time: Tiempo, por defecto es time() (igual que en date())
      *
      * @return string con la fecha en formato humano (y en español)
    */
     final public static function fecha(string $format, int $time = 0) : string  {
       $date = date($format,$time == 0 ? time() : $time);
       $cambios = array(
         'Monday'=> 'Lunes',
         'Tuesday'=> 'Martes',
         'Wednesday'=> 'Miércoles',
         'Thursday'=> 'Jueves',
         'Friday'=> 'Viernes',
         'Saturday'=> 'Sábado',
         'Sunday'=> 'Domingo',
         'January'=> 'Enero',
         'February'=> 'Febrero',
         'March'=> 'Marzo',
         'April'=> 'Abril',
         'May'=> 'Mayo',
         'June'=> 'Junio',
         'July'=> 'Julio',
         'August'=> 'Agosto',
         'September'=> 'Septiembre',
         'October'=> 'Octubre',
         'November'=> 'Noviembre',
         'December'=> 'Diciembre',
         'Mon'=> 'Lun',
         'Tue'=> 'Mar',
         'Wed'=> 'Mie',
         'Thu'=> 'Jue',
         'Fri'=> 'Vie',
         'Sat'=> 'Sab',
         'Sun'=> 'Dom',
         'Jan'=> 'Ene',
         'Aug'=> 'Ago',
         'Apr'=> 'Abr',
         'Dec'=> 'Dic'
       );
       return str_replace(array_keys($cambios),array_values($cambios),$date);
     }

//---------------------------------------------------------

  /**
      * Codifica un numero  entero por lo general ids
      *
      * @param Int: numero entero a codificar
      *
      * @return string de 43 caracteres codificados
    */
  final public static  function codeNumber(int $int){
    if (empty($int) || !is_numeric($int)) {
      return false;
    }else{
      $num=[1,2,3,4,5,6,7,8,9,0];
      $matriz=['cz','pz','hz','lz','oz','az','tz','nz','sz','wz'];
      $wordEncriptation='carwill';
      $code=sha1($wordEncriptation.intval($int));
      $result=str_replace($num, $matriz, $int);
      return $code.'-'.$result;
    }
  }
  //--------------------------------------------------------------------------


  /**
      * decodifica un numero codificado con codeNumber();
      *
      * @param String:cadena a codificar
      *
      * @return Int decodificado
    */
  public final static function decodeNumber(string $string){
      if (empty($string) || strpos($string, '-')==false) {
            return false;
      }else{
        $num=[1,2,3,4,5,6,7,8,9,0];
        $matriz=['cz','pz','hz','lz','oz','az','tz','nz','sz','wz'];
        $wordEncriptation='carwill';
        $var=explode('-', $string);
        if (!str_replace($matriz, $num, $var[1])) {
          return false;
        }else{
          $decode=str_replace($matriz, $num, $var[1]);
          $encode=sha1($wordEncriptation.$decode);
          if ($var[0]===$encode) {
            return intval($decode);
          }else{
            return false;
              }
            }
      }
    }
//-------------------------------------------------------------------------------

/**
      * Recorta un string a un numero de caracteres establecidas por el usuario
      *
      * @param String: Cadena a recortar
      * @param Int: Numero entero para recortar la cadena, por defecto es 50;
      * @return string recortado
    */
    public final static function cut(string $string, $length=NULL){
        //Si no se especifica la longitud por defecto es 50
        if ($length == NULL)
            $length = 50;
        //Primero eliminamos las etiquetas html y luego cortamos el string
        $stringDisplay = substr(strip_tags($string), 0, $length);
        //Si el texto es mayor que la longitud se agrega puntos suspensivos
        if (strlen(strip_tags($string)) > $length)
            $stringDisplay .= ' ...';
        return $stringDisplay;
    }

//--------------------------------------------------------------------------------
/**
      * DEVUELVE PAGINADO SEGUN SCROLL BOTTOM O TOP 
      *
      * @param Int: page que viene al hacer scroll
      * @return pagina de inicio y limite por pagina
    */
    public final static function scrollPagination(bool $page,int $pagesBy,int $dataTotal,string $sessionName):array{ 
          //control de carga de mensajes del chat de manera ascendente
          $sql = true;
          $pagesBy = $pagesBy;
          if ($page == true) {

              if ($_SESSION[$sessionName] < $pagesBy) {
                    $inicio = 0;
                    $pagesBy = $_SESSION[$sessionName];
                    $_SESSION[$sessionName] = $_SESSION[$sessionName] - $pagesBy;
              }else{
                    //-----------------------------------------------------------
                    $control = $_SESSION[$sessionName] - $pagesBy;
                    $inicio = $control;  
                    $_SESSION[$sessionName] = $control;
                    //---------------------------------------------------------
                    if ($control < 0) {
                     $sql = false;
                    }
              }
              
          }else{

              if (isset($_SESSION[$sessionName])) unset($_SESSION[$sessionName]);

              if ($dataTotal > $pagesBy) {
                  $inicio = $dataTotal - $pagesBy;    
                  
              }else{
                  $inicio = 0;
              }

              $_SESSION[$sessionName] = $inicio;
          }
                  //
          return [$inicio,$pagesBy,$sql];
    }
//-------------------------------------------------------------------------------

/**
  *Metodo que permite hacer llamadas a las api rest externas
*/
final public function file_get_contents_curl($url) {
  if (strpos($url,'https://') !== FALSE) {
    $fc = curl_init();
    curl_setopt($fc, CURLOPT_URL,$url);
    curl_setopt($fc, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($fc, CURLOPT_HEADER,0);
    curl_setopt($fc, CURLOPT_VERBOSE,0);
    curl_setopt($fc, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($fc, CURLOPT_TIMEOUT,30);
    $res = curl_exec($fc);
    curl_close($fc);
  }else {
    $res = file_get_contents($url);
  };
         
  return json_decode($res,true);
}

//--------------------------------------------------------------------------------
}

?>
