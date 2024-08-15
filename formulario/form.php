<?php
header('Content-Type: application/json');

$nombres = $_POST["nombres"];
$apellidos = $_POST["apellidos"];
$correo = $_POST["correo"];
$telefono = $_POST["telefono"];
$mensaje = $_POST["mensaje"];
$adjunto = $_FILES["adjunto"];

$destinatario = "diegopolo14@gmail.com";
$asunto = "Asesoria personalizada - ECONOREPUESTOS S.A.S";

// Crear un identificador único para el correo
$uid = md5(uniqid(time()));

// Cuerpo del mensaje en HTML
$cuerpo = "
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombres:</strong> $nombres</p>
    <p><strong>Apellidos:</strong> $apellidos</p>
    <p><strong>Correo:</strong> $correo</p>
    <p><strong>Número de teléfono:</strong> $telefono</p>
    <p><strong>Mensaje:</strong> $mensaje</p>
";

$headers = "From: $correo\r\n";
$headers .= "Reply-To: $correo\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$uid\"\r\n\r\n";

$mensaje = "--$uid\r\n";
$mensaje .= "Content-Type: text/html; charset=UTF-8\r\n";
$mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$mensaje .= "$cuerpo\r\n\r\n";

if (isset($adjunto) && $adjunto['error'] == UPLOAD_ERR_OK) {
    $filePath = $adjunto['tmp_name'];
    $fileName = $adjunto['name'];
    $fileType = $adjunto['type'];
    $fileSize = $adjunto['size'];

    $handle = fopen($filePath, "r");
    $content = fread($handle, $fileSize);
    fclose($handle);
    $content = chunk_split(base64_encode($content));

    $mensaje .= "--$uid\r\n";
    $mensaje .= "Content-Type: $fileType; name=\"$fileName\"\r\n";
    $mensaje .= "Content-Transfer-Encoding: base64\r\n";
    $mensaje .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n\r\n";
    $mensaje .= "$content\r\n\r\n";
    $mensaje .= "--$uid--";
}

// Enviar el correo y devolver la respuesta en JSON
$response = array();

if (mail($destinatario, $asunto, $mensaje, $headers)) {
    $response['status'] = 'success';
} else {
    $response['status'] = 'error';
}

echo json_encode($response);
?>