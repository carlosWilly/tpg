<?php

# Seguridad
defined('INDEX_DIR') OR exit('carwill software says .i.');

//------------------------------------------------

/**
  * dslogueo
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/salir',function($request, $response){

  $reg = new Logout;
  $response->withJson($reg->logoutUser());

  return $response;
});

//------------------------------------------------

/**
  * Registro de un usuario
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/register',function($request, $response){

  $reg = new Register;
  $response->withJson($reg->regMultiple($_POST));

  return $response;
});

$app->post('/register/datacomplete',function($request, $response){

  $reg = new Register;
  $response->withJson($reg->regDataComplete($_POST));

  return $response;
});

$app->post('/register/complete0',function($request, $response){

  $reg = new Register;
  $response->withJson($reg->complete());

  return $response;
});

//------------------------------------------------

/**
  * Inicio de Sesión
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/login',function($request, $response) {

  $login = new Login;
  $response->withJson($login->SignIn($_POST));

  return $response;
});

//------------------------------------------------

/**
	* Recuperación de contraseña perdida
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/lostpass',function($request, $response) {

	$model = new Lostpass;
	$response->withJson($model->RepairPass($_POST));

	return $response;
});
//------------------------------------------------





//------------------------------------------------

/**
  * Container profile grall

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/profile',function($request, $response) {

  $model = new ProfileInfo;
  $response->withJson($model->ProfileGrall($_POST));
  
  return $response;

});

//------------------------------------------------


/**
  * sube photos pertenecientes al perfil y/usuarios

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/photosProfile',function($request, $response) {

  $model = new Photos;
  $response->withJson($model->photosProfile($_POST));
  
  return $response;

});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::PROCEDIMIENTOS PARA LA SECCION EMPLEO DEL PERFIL:::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 
 //------------------------------------------------

/**
  * Edita informacion respecto al empleo del usuario

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/empleo/edit',function($request, $response) {

  $model = new ProfileInfo;
  $response->withJson($model->empleoEdit($_POST));
  
  return $response;

});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::PROCEDIMIENTOS PARA LA SECCION FORMACION PERFILL:::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 
 //------------------------------------------------

/**
  * Edita informacion de la formacion del usuario

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/formacion/edit',function($request, $response) {

  $model = new ProfileInfo;
  $response->withJson($model->formacionEdit($_POST));
  
  return $response;

});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::PROCEDIMIENTOS PARA LA SECCION DESCRIPCION DEL USUARIO:::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 
 //------------------------------------------------

/**
  * Edita informacion de la formacion del usuario

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/descripcion/edit',function($request, $response) {

  $model = new ProfileInfo;
  $response->withJson($model->descripcionEdit($_POST));
  
  return $response;

});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::PROCEDIMIENTOS PARA LA SECCION FAVORITOS:::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 
 //------------------------------------------------

/**
  * Edita informacion de la formacion del usuario

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/favoritos/add',function($request, $response) {

  $model = new Favoritos;
  $response->withJson($model->addFavorites($_POST));
  
  return $response;

});

/**
  * elimina a usuario de  mi lista de favoritos

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/favoritos/delete',function($request, $response) {

  $model = new Favoritos;
  $response->withJson($model->deleteFavorites($_POST));
  
  return $response;

});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::PROCEDIMIENTOS CORRESPONDIENTES AL CHAT:::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 
 //------------------------------------------------

/**
  * ENVIA SOLICITUD DE CHAT

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/chat/solicite',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->solicitudChat($_POST));
  
  return $response;

});

/**
  * confirma solicitud del chat

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/chat/chatConfirm',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->permisionChat($_POST));
  
  return $response;

});

//------------------------------------------------

/**
  * cancela la solicitud de chat

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/chat/chatCancel',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->chatSoliciteCancel($_POST));
  
  return $response;

});


//------------------------------------------------

/**
  * Inserta los mesajes provenientes del chat

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/chat/sendMessage',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->sendMessage($_POST));
  
  return $response;

});

//------------------------------------------------

/**
  * verifica que el hash a donde se esta enviando sea correcto

  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/chat/hash',function($request, $response) {

  $model = new Chat;
  $response->withJson($model->hash($_POST));
  
  return $response;

});

//------------------------------------------------



//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PARA OPERACIONES SEARCH::::::::::::::::::::::::::::::
/**
  * Retorno  de informacion de las preferecias de busqueda del usuario cuando va a editar esta informacion
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/search/updateSearchOption',function($request, $response){

  $model = new Search;
  $response->withJson($model->updateOptionSearch($_POST));

  return $response;
});

/**
  * retorno resultados controlador search cuando se realiza el scroll
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/search/scrollsearch',function($request, $response){

  $model = new Search;
  $response->withJson($model-> optionSearch($_POST));

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::



//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PARA OPERACIONES USUARIOS::::::::::::::::::::::::::::::
/**
  * INSERTA ACTUALIZACION RESPECTO A LOS DATOS DE LOS USUARIOS
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/user/updateProcess',function($request, $response){

  $model = new Users;
  $response->withJson($model->updateInfoUsers($_POST));

  return $response;
});

$app->post('/user/nowubication',function($request, $response){

  $model = new Users;
  $response->withJson($model->nowUbicationUpdate($_POST));

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PARA MANEJOS DE PHOTOS::::::::::::::::::::::::::::::
/**
  * Actualiza photo de portada y perfil al seleccionar una imagen de tu catolo general
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/photo/updateSelecion',function($request, $response){

  $model = new Photos;
  $response->withJson($model->updateSelecion($_POST));

  return $response;
});

/**
  * Elimina Imagenes de usuarios
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/photo/deletePhoto',function($request, $response){

  $model = new Photos;
  $response->withJson($model->deleteImgUsers($_POST));

  return $response;
});
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS PARA PAGOS:::::::::::::::::::::::::::::::::::::::::::
/**
  * Actualiza photo de portada al selecciona runa imagen
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/payment/pay',function($request, $response){

  $model = new Pagos;
  $response->withJson($model->pay($_POST));

  return $response;
});

/**
  * Actualiza photo de portada al selecciona runa imagen
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/payment/removeTransaction',function($request, $response){

  $model = new Pagos;
  $response->withJson($model->removeTransaction($_POST));

  return $response;
});

/**
  * Transacion y demas procesos cuando el usuario se regitra por primera vez fullpack 3 meses
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/payment/fullPack',function($request, $response){

  $model = new Pagos;
  $response->withJson($model->fullPack($_POST));

  return $response;
});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::PROCEDIMIENTOS BLACKLIST:::::::::::::::::::::::::::::::::::::::::::
/**
  * retira al usurio de la blacklist
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/blackList/add',function($request, $response){

  $model = new Blacklist;
  $response->withJson($model->blacklistAdd($_POST));

  return $response;
});

//-------------------------------------------------------------------------
/**
  * retira al usurio de la blacklist
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/blackList/delete',function($request, $response){

  $model = new Blacklist;
  $response->withJson($model->blacklistDelete($_POST));

  return $response;
});

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
