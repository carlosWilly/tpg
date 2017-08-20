<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

/**
  * retorna informacion para top intereses
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/top/myinteres',function($request, $response){

  $reg = new Favoritos;
  $response->withJson($reg->myFavorites());

  return $response;
});

/**
  * retorna informacion para top intereses
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/top/notifications',function($request, $response){

  $reg = new InfoUsers;
  $response->withJson($reg->infoNoticias());

  return $response;
});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::INFORMACION DE EMPLEO:::::::::::::::::::::::::::::::::::::::::::::::
/**
  * retorna informacion sobre el empleo del usuarios para luego ser editado
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/empleo/info',function($request, $response){

  $reg = new ProfileInfo;
  $response->withJson($reg->empleoInfo());

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::INFORMACION DE FORMACION::::::::::::::::::::::::::::::::::::::::::::
/**
  * retorna informacion sobre la formacion del usuario
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/formacion/info',function($request, $response){

  $reg = new ProfileInfo;
  $response->withJson($reg->fomacionInfo());

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::INFORMACION DE DESCRIPCION DEL USUARIO::::::::::::::::::::::::::::::::::::::::::::
/**
  * retorna informacion sobre la formacion del usuario
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/descripcion/info',function($request, $response){

  $reg = new ProfileInfo;
  $response->withJson($reg->descripcionInfo());

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PARA OPERACIONES SEARCH::::::::::::::::::::::::::::::
/**
  * Retorno  de informacion de las preferecias de busqueda del usuario cuando va a editar esta informacion
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/search/osearchInfo',function($request, $response){

  $reg = new InfoUsers;
  $response->withJson($reg->optionSearch(true));

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PHOTOGRAPY::::::::::::::::::::::::::::::
/**
  * Retorna todas mis fotos para ser usadas ya sea como foto de portada o foto de perfil
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/photos/myPictures',function($request, $response){

  $model = new Photos;
  $response->withJson($model->myPictures());

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS RAPIDOS E INSTANTANEOS::::::::::::::::::::::::::::::
/**
  * Retorna todas mis fotos para ser usadas ya sea como foto de portada o foto de perfil
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/search/cityState',function($request, $response){

  $model = new Search;
  $response->withJson($model->AjaxCityByState($_GET));

  return $response;
});


/**
  * Retorna las prefencias del usuario  que esta en mis favoritos, eso me asegura saber que es lo que esta buscando
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/search/userPreferences',function($request, $response){

  $model = new Search;
  $response->withJson($model->userPreferences($_GET));

  return $response;
});

/**
  * busca usuarios para el controlador search segun las preferencias de busqueda
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/search/optionSearch',function($request, $response){

  $model = new Search;
  $response->withJson($model->optionSearch($_GET));

  return $response;
});

/**
  * ----
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/search/PB',function($request, $response){
  $model = new Search;
  $response->withJson($model->PB($_GET));
  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//::::::::::::::::::CHAT:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
/**
  * Carga lista de contactos

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/loadChat',function($request, $response) {

  $model = new InfoUsers;
  $response->withJson($model->infoChat());
  
  return $response;

});

/**
  * carga los mensajes de los usuarios en el box de pedido

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/chat/content',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->messageLoadBox($_GET));
  
  return $response;

});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::