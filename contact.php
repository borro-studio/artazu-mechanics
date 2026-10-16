<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://artazumechanics.com');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Campo trampa: invisible para personas, los bots lo rellenan. Respondemos "ok" sin enviar nada.
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true]);
    exit;
}

// Límite de envíos: máximo 5 mensajes por IP cada 10 minutos
$limiteArchivo = sys_get_temp_dir() . '/artazu_contact_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
$ahora   = time();
$envios  = is_file($limiteArchivo) ? (json_decode(file_get_contents($limiteArchivo), true) ?: []) : [];
$envios  = array_filter($envios, fn($t) => $t > $ahora - 600);
if (count($envios) >= 5) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Demasiados envíos, inténtalo más tarde']);
    exit;
}

// Campos de una sola línea: quitamos saltos de línea para evitar inyección de cabeceras de correo
$unaLinea = fn($v) => trim(str_replace(["\r", "\n", "\0"], ' ', strip_tags($v)));

$nombre  = mb_substr($unaLinea($_POST['nombre']  ?? ''), 0, 100);
$email   = mb_substr($unaLinea($_POST['email']   ?? ''), 0, 254);
$empresa = mb_substr($unaLinea($_POST['empresa'] ?? ''), 0, 150);
$mensaje = mb_substr(trim(strip_tags($_POST['mensaje'] ?? '')), 0, 5000);

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

$destinatario = 'comercial@artazumechanics.com';
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
    $envios[] = $ahora;
    @file_put_contents($limiteArchivo, json_encode(array_values($envios)), LOCK_EX);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al enviar el correo']);
}
