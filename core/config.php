<?php

# Tipado estricto para PHP 7
declare(strict_types=1);

//------------------------------------------------

# Seguridad
defined('INDEX_DIR') OR exit('carwill software says .i.');

//------------------------------------------------

# Timezone DOC http://php.net/manual/es/timezones.php
date_default_timezone_set('America/New_York');

//------------------------------------------------

/**
  * Configuración de la conexión con la base de datos.
  * @param host 'hosting local/remoto'
  * @param user 'usuario de la base de datos'
  * @param pass 'password del usuario de la base de datos'
  * @param name 'nombre de la base de datos'
  * @param port 'puerto de la base de datos (no necesario en la mayoría de motores)'
  * @param protocol 'protocolo de conexión (no necesario en la mayoría de motores)'
  * @param motor 'motor de conexión por defecto'
  * MOTORES VALORES:
  *        mysql
  *        sqlite
  *        oracle
  *        postgresql
  *        cubrid
  *        firebird
  *        odbc
*/
define('DATABASE', array(
  'host' => 'localhost',
  'user' => 'root',//wybwsajt_carwill
  'pass' => '',//625ab8824c8df9393683b312a5c52afc9c0a90bb02e33a3d
  'name' => 'tpg',//wybwsajt_agento
  'port' => 1521,
  'protocol' => 'TCP',
  'motor' => 'mysql'
));

//------------------------------------------------

/**
  * Define la carpeta en la cual se encuentra instalado el framework.
  * @example "/" si para acceder al framework colocamos http://url.com en la URL, ó http://localhost
  * @example "/Ocrend-Framework/" si para acceder al framework colocamos http://url.com/Ocrend-Framework, ó http://localhost/Ocrend-Framework/
*/
define('__ROOT__', '/tpg/');//jcode

//------------------------------------------------

# Constantes fundamentales
define('URL', 'http://localhost/tpg/');//http://104.131.173.5/jcode/
define('APP', 'PlayGround');

//------------------------------------------------

# Constantes vedi crishi astro //https://www.vedicrishiastro.com/
define('IDUSER', 600203);
define('KEYAPI', '683d23634e1376e7cccfaf6b9e16d84d');

//------------------------------------------------
# Control de sesiones
define('DB_SESSION', true);
define('SESSION_TIME', 18000); # Tiempo de vida para las sesiones 5 horas = 18000 segundos.
define('SESS_APP_ID', 'app_id');
session_start([
  'use_strict_mode' => true,
  'use_cookies' => true,
  'cookie_lifetime' => SESSION_TIME,
  'cookie_httponly' => true, # Evita el acceso a la cookie mediante lenguajes de script (cómo javascript)
  'hash_function' => 5 # sha256, para obtener una lista completa print_r(hash_algos());
]);

//------------------------------------------------

# Constantes de PHPMailer
define('PHPMAILER_HOST', 'mail.carwillstudios.com');
define('PHPMAILER_USER', 'carloswilly@carwillstudios.com');
define('PHPMAILER_PASS', 'c3rl0s_w1lly');
define('PHPMAILER_PORT', 465);

//------------------------------------------------

# PayPal SDK
define('PAYPAL_MODE','sandbox'); # sandbox ó live
define('PAYPAL_CLIENT_ID','ATc-98ftOLdtFrWWHkqvTKmNbIvfHD0qbFg1Ws2xAm_u5IoX_s0GDzIIQu23NfUDyncdhFGGr6hMU10j');
define('PAYPAL_CLIENT_SECRET','EKK02fTGvO4uldzwyUsb4iMe4_20Fl7aLNFgzCccSYEFtluZDAmGkiWkkzcXsty1Tz3-azdCnqT1tnkU');

//------------------------------------------------

# Facebook Inicio de session
define('APP_ID', '1383800061684828');
define('APP_SECRET', '326bfb1197d09d6648a2b7a8a77a1f05');
define('DEFAULT_GRAPH_VERSION','v2.8');
//------------------------------------------------
# Facebook Inicio de session
define('CLIENT_ID', '78apd42eogip0k');
define('APP_SECRETL', 'pa79Z7qSPkiWxI44');
//------------------------------------------------
# Activación del Firewall
define('FIREWALL', true);

//------------------------------------------------

# Activación del DEBUG, solo para desarrollo
define('DEBUG', false);

//------------------------------------------------

# Versión actual del framework
define('VERSION', '1.1.2');

?>
