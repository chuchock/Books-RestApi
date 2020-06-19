<?php

echo "entra al servidor";
/* MÉTODOS DE AUTENTICACION */

//--------------------------------------------------------
// autenticación via HTTP
/*
	$user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
	$pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

	if($user !== 'jesus' || $pwd !== '123456'){
		echo "el usuario o password son incorrectos";
		die;
	}
	*/
//---------------------------------------------------------
// autenticación via HMAC
/*if(
		!array_key_exists('HTTP_X_HASH', $_SERVER) ||
		!array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
		!array_key_exists('HTTP_X_UID', $_SERVER)
	){
		echo "faltan parametros";
		die;
	}

	list($hash, $uid, $timestamp) = [
		$_SERVER['HTTP_X_HASH'],
		$_SERVER['HTTP_X_UID'],
		$_SERVER['HTTP_X_TIMESTAMP']
	];

	$secret = 'sh!! No se lo cuentes a nadie';
	$newHash = sha1($uid.$timestamp.$secret);

	if($newHash !== $hash){
		echo "los hash no coinciden";
		die;
	}
	*/
//------------------------------------------------------
// autenticación vía acces token
if (!array_key_exists('HTTP_X_TOKEN', $_SERVER)) {
	echo "faltan parametros";
	die;
}

// servidor de autenticación
$url = 'http://localhost:82/platzi/Rest_api/php_api/auth_server.php';

$ch = curl_init($url);
curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	[
		"X-Token: {$_SERVER['HTTP_X_TOKEN']}"
	]
);

curl_setopt(
	$ch,
	CURLOPT_RETURNTRANSFER,
	true
);

$ret = curl_exec($ch);
if ($ret !== 'true') {
	echo "los tokens no coinciden";
	die;
}
echo "te has logueado";

//----------------------------------------------------------------------------



// Definimos los recursos disponibles
$allowedResourceTypes = [
	'books',
	'authors',
	'genres',
];

// Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];
echo $resourceType;

if (!in_array($resourceType, $allowedResourceTypes)) {
	echo "el recurso no es válido";
	http_response_code(400);
	die;
}

// Defino los recursos
$books = [
	1 => [
		'titulo' => 'The lord of the rings',
		'id_autor' => 1,
		'id_genero' => 1,
	],
	2 => [
		'titulo' => 'How to build REST API with php',
		'id_autor' => 2,
		'id_genero' => 2,
	],
	3 => [
		'titulo' => 'Best programming practices',
		'id_autor' => 3,
		'id_genero' => 3,
	],
];

header('Content-Type: application/json');

// Levantamos el Id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';

// Generamos la respuesta asumiendo que el pedido es correcto
echo ($resourceId);
switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
	case 'GET':
		if (empty($resourceId)) {
			echo json_encode($books);
		} else {
			if (array_key_exists($resourceId, $books)) {
				echo json_encode($books[$resourceId]);
			} else {
				http_response_code(404);
				echo "El item no existe";
			}
		}
		break;

	case 'POST':
		$json = file_get_contents('php://input');

		$books[] = json_decode($json, true);

		//echo array_keys($books)[count($books) - 1];
		echo json_encode($books);
		break;

	case 'PUT':
		// validamos que el recurso usado exista
		if (!empty($resourceId) && array_key_exists($resourceId, $books)) {
			//obtenemos los datos que manda el cliente
			$json = file_get_contents('php://input');

			$books[$resourceId] = json_decode($json, true);

			//regresamos la colección modificada en formato json
			echo json_encode($books);
		}
		break;

	case 'DELETE':
		// validamos que el recurso usado exista
		if (!empty($resourceId) && array_key_exists($resourceId, $books)) {
			unset($books[$resourceId]);
		}

		echo json_encode($books);
		break;
}


	/*
	GET optener recursos, tabnto colecciones como recursos puntulaes
	POST crear recursos en tu servivod
	PUT remplazo de recursos existentes por uno nuevo
	Delete borra el recurso
	*/


	// Inicio el servidor en la terminal 1
	// php -S localhost:8000 server.php

	// Terminal 2 ejecutar 
	// curl http://localhost:8000 -v
	// curl http://localhost:8000/\?resource_type\=books
	// curl http://localhost:8000/\?resource_type\=books | jq
