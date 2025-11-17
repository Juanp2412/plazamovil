<?php
// Esto es esencial: Le dice al navegador que la respuesta será JSON.
header('Content-Type: application/json');

// 1. Obtener y decodificar el mensaje enviado desde JavaScript.
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$respuesta_bot = "";

// 2. Verificar si se recibió el mensaje
if (isset($data['mensaje']) && !empty($data['mensaje'])) {
    
    // Convertir a minúsculas y limpiar espacios
    $mensaje_usuario = strtolower(trim($data['mensaje']));

    // 3. Lógica del Chatbot (Tus reglas)
    
    // --- Condición 1: SALUDO SECRETO (Para 'puñeta') ---
    // Tiene prioridad para que nadie más lo vea.
    if (strpos($mensaje_usuario, 'puñeta') !== false) {
        $respuesta_bot = "👋 ¡Dímelo, papi! Si se puede puñeta.";

    // --- Condición 2: SALUDO BÁSICO (Para 'hola' o 'iniciar') ---
    // Saludo normal de la empresa.
    } else if (strpos($mensaje_usuario, 'hola') !== false || strpos($mensaje_usuario, 'iniciar') !== false) {
        $respuesta_bot = "👋 ¡Bienvenido a Plaza Móvil! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?";

    // --- Condición 3: PRODUCTOS ---
    } else if (strpos($mensaje_usuario, 'producto') !== false || strpos($mensaje_usuario, 'catalogo') !== false) {
        $respuesta_bot = "🛍️ ¡Claro! Puedes ver nuestro catálogo de productos en el siguiente enlace: [Aquí va el enlace a tu catálogo].";
        
    // --- Condición 4: HORARIO ---
    } else if (strpos($mensaje_usuario, 'horario') !== false) {
        $respuesta_bot = "⏱️ Nuestro horario de atención es:<br>Lunes a viernes de 8:00 AM a 6:00 PM.";

    // --- Condición 5: CONTACTO/TELÉFONO ---
    } else if (strpos($mensaje_usuario, 'contacto') !== false || strpos($mensaje_usuario, 'telefono') !== false || strpos($mensaje_usuario, 'whatsapp') !== false) {
        $respuesta_bot = "📞 Nuestro teléfono/WhatsApp es: **3003105511**.";

    // --- Condición 6: Mensaje por defecto ---
    } else {
        $respuesta_bot = "Disculpa, no entendí tu pregunta. Puedes intentar preguntar por 'horario', 'productos' o simplemente decir 'hola'.";
    }
    
} else {
    // Si no se recibió la clave 'mensaje', es una petición inválida
    http_response_code(400); // Bad Request
    $respuesta_bot = "❌ Error: No se recibió un mensaje válido.";
}

// 4. Crear el array de respuesta
$response_array = array(
    'respuesta' => $respuesta_bot // Esta es la clave que tu JavaScript espera: data.respuesta
);

// 5. Codificar el array a formato JSON y enviarlo.
echo json_encode($response_array);

// NO HAY CÓDIGO NI ESPACIOS DESPUÉS DE ESTA LÍNEA
?>