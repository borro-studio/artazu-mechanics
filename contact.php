<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://artazumechanics.com');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$nombre  = trim(strip_tags($_POST['nombre']  ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$empresa = trim(strip_tags($_POST['empresa'] ?? ''));
$mensaje = trim(strip_tags($_POST['mensaje'] ?? ''));

if (!$nombre || !$email || !$mensaje) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email no válido']);
    exit;
}

$destinatario = 'info@borrostudio.com';
$asunto       = 'Nuevo mensaje desde la web — Artazu Mechanics';

$cuerpo  = "Has recibido un nuevo mensaje desde el formulario de contacto de artazumechanics.com\n\n";
$cuerpo .= "Nombre:  $nombre\n";
$cuerpo .= "Email:   $email\n";
if ($empresa) $cuerpo .= "Empresa: $empresa\n";
$cuerpo .= "\nMensaje:\n$mensaje\n";

$cabeceras  = "From: Artazu Mechanics <artazu@artazumechanics.com>\r\n";
$cabeceras .= "Reply-To: $nombre <$email>\r\n";
$cabeceras .= "MIME-Version: 1.0\r\n";
$cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";
$cabeceras .= "X-Mailer: PHP/" . phpversion();

if (mail($destinatario, $asunto, $cuerpo, $cabeceras)) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al enviar el correo']);
}
