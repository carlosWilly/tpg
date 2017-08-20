<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class setupController extends Controllers {

  public function __construct() {
    parent::__construct(true);

    //(new Pagos)->userPagado();
    //calculo de deatalles d e cuenta
    $db = new Conexion();
    $ac = $db->select('iau_timeinit,iau_timeend','jc_infoacountuser','iau_id_user='.intval(Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id'])).'','LIMIT 1');
    if ($ac != false) {
        Helper::load('strings');
        $days = Strings::date_difference(date('d-m-Y',time()),date('d-m-Y',$ac[0][1]));
      
      //calculo de variables a mostrar
      $button ='<a href="plans" class="btn btn-default btn-lg btn-block b-green-a">EXPAND YOU ACCOUNT</a>';
      $remainingDays = $days;
      $expiration = date('m/d/Y',$ac[0][1]);

      if ($_SESSION[SESS_APP_ID]['u_account']['tipoAccount'] == 'FULLPACK') {
          $button='';
      }elseif ($_SESSION[SESS_APP_ID]['u_account']['tipoAccount'] == 'FREE') {
          $expiration='Unlimited';
          $remainingDays='Unlimited';
      }
     //--------------------------------------------------------------------------------------------
        $data = array(
            'fecha_ini'=>date('m/d/Y',$ac[0][0]),
            'fecha_end'=>$expiration,
            'fecha_act'=>$remainingDays,
            'button'=>$button,
            'acil'=> $_SESSION[SESS_APP_ID]['u_account']['tipoAccount']== 'FULLPACK' || $_SESSION[SESS_APP_ID]['u_account']['tipoAccount']=='PRO II' ? true : false
        );

    }
    //------------------------------------------------------
    $iu = new InfoUsers;
    echo $this->template->render('app/setup',[
         'user'=>$iu->infoUser(),
         'data'=>$data
    	]);
  }
}

?>
