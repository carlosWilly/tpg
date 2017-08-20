<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');

//------------------------------------------------

class reciboController extends Controllers {
 
  public function __construct() {
    parent::__construct(true);
    //-----------------------------------------------------
   
   Helper::load('strings');
   //---------------------------------
   $free = false;
   if (isset($_GET['account'],$_GET['tokenAccount'])) {

      if ($_GET['account'] != 'free' || empty($_GET['tokenAccount'])) {
             Func::redir();
      }else{
         $free = true;
      }
   }
   //----------------------------------
   
   $status = new Pagos();

   $t = $status->revisar();
   if ($t['success'] || $free) {

   	$result = $status->userPagado(isset($_GET['tokenAccount']) ? $_GET['tokenAccount'] : $t['id'], $t['hash'],isset($_GET['tokenAccount']) ? true : null);
    
    $success = $result['success'];
    $message = $result['message'];
   
   }else{
   	 $success =0;
   	 $message = 'We are sorry, an error occurred in the transaction, please try again.';
   }
     
     echo $this->template->render('app/recibo',[
     	
     	'success'=>$success,
     	'message'=>$message,
     	'data'=>$result['data']

     	]);
  
    //-----------------------------------------------------
  }

}

?>
