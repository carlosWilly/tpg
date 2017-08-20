<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class plansController extends Controllers {
  
  public function __construct() {
    parent::__construct(true);
    //hacemos la peticion a las bd
    $db = new Conexion;
    $transaction = $db->select('tr_id,tr_time,tr_status,tr_url,ac_name,ac_precio',' jc_transacciones LEFT JOIN jc_mbacounts ON tr_tipo_account=ac_id','tr_id_user='.intval(Func::decodeNumber($_SESSION[SESS_APP_ID]['u_id'])).' and tr_status=0 and ac_name!="FREE"');
    //-------------------------------------------------------------------------------------------------------------------------------------------------
    if ($transaction != false) {
            $data = ['type'=>0,'array' => $transaction];
    }else{
        
        $account = isset($_SESSION[SESS_APP_ID]['u_account']['tipoAccount']) ?  $_SESSION[SESS_APP_ID]['u_account']['tipoAccount'] : 'none';
        $plans = $db->select('*','jc_mbacounts','ac_name != "'.$account.'" and  ac_name!="FULLPACK" ');
        
        if (false != $plans) {
            $data=['type'=>1,'array'=>$plans];
        }else{
          Func::redir();
        }
    }
    //------------------------------------------------------------------------------------------------------------------------------------------------
    $iu = new InfoUsers;
    //conprobamos en que plans e encuentra la persona para poder mostrar el catalogo de lo contrario
    // lo redireccionamos al controlador setup
    $fullpack = $_SESSION[SESS_APP_ID]['u_account']['status']== 'PENDENT' ? true : false;

    if (isset($_SESSION[SESS_APP_ID]['u_account']['tipoAccount']) and $_SESSION[SESS_APP_ID]['u_account']['tipoAccount']=='FULLPACK') {
      Func::redir('setup');
    }
    $vista = $fullpack ? 'app/fullpack' : 'app/plans';
    echo $this->template->render($vista,
     [
        'token'=>$this->route->getMethod(),
        'plans'=>$data,
     ]
    );
  }

}

?>
