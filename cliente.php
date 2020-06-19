<?php
/* cliente para manejar errores */

$ch = curl_init($argv[1]);
curl_setopt(
	$ch,
	CURLOPT_RETURNTRANSFER,
	true
);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo $httpCode;

switch ($httpCode) {
	case 200:
		echo 'Todo bien';
		break;

	case 400:
		echo 'Request incorrecto';
		break;

	case 404:
		echo 'Recurso no encontrado';
		break;

	case 500:
		echo 'El servidor falló';
		break;

	default:

		break;
}
