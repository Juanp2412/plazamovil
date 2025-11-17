// ==========================================================
// 1. FUNCIONES GLOBALES Y DE UTILIDAD
// ==========================================================

// Función global para desplazar la vista al último mensaje
function scrollToBottom() {
    const chatbox = document.getElementById("chatbot-body");
    // Esto fuerza la vista al final del contenedor, mostrando el último mensaje.
    chatbox.scrollTop = chatbox.scrollHeight;
}

// Mostrar mensajes del usuario en el chat
function appendUserMessage(message) {
    const chatbox = document.getElementById("chatbot-body");
    const msgDiv = document.createElement("div");

    msgDiv.classList.add("message", "user-message");
    // Usamos innerHTML por si usas emojis o formato simple.
    msgDiv.innerHTML = message; 
    
    chatbox.appendChild(msgDiv);
    
    // Asegura que el chat baje al nuevo mensaje
    scrollToBottom();
}

// Mostrar mensajes del bot en el chat
function appendBotMessage(message) {
    const chatbox = document.getElementById("chatbot-body");
    const msgDiv = document.createElement("div");

    msgDiv.classList.add("message", "bot-message");
    msgDiv.innerHTML = message;
    
    chatbox.appendChild(msgDiv);
    
    // Asegura que el chat baje al nuevo mensaje
    scrollToBottom();
}

// ==========================================================
// 2. FUNCIÓN PRINCIPAL DE ENVÍO DE MENSAJES (FETCH)
// ==========================================================

// Función para enviar mensaje y comunicarse con el servidor
function sendMessage() {
    const input = document.getElementById("chatbot-input");
    const userMessage = input.value.trim();

    if (!userMessage) return; // No enviar si el mensaje está vacío

    appendUserMessage(userMessage);
    input.value = "";

    // 🛑 CORRECCIÓN FINAL: Usamos la ruta absoluta desde la raíz del servidor.
    // Esto garantiza que se busque en la carpeta 'PLAZA-M-VIL-3.1/controller/'.
    fetch("/PLAZA-M-VIL-3.1/controller/chatbot.php", { 
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mensaje: userMessage }),
    })
        .then((response) => {
            if (!response.ok) {
                // Captura el error 404 o 500 y lo muestra
                throw new Error("Error HTTP: " + response.status);
            }
            // Verifica si la respuesta es JSON antes de intentar analizarla
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                throw new Error("Respuesta no válida del servidor. No es JSON.");
            }
        })
        .then((data) => {
            // Usa 'data.respuesta' o un mensaje por defecto si no viene nada
            let botReply = data.respuesta || "🤖 Lo siento, no pude obtener una respuesta.";
            appendBotMessage(botReply);
        })
        .catch((error) => {
            console.error("Error en chatbot:", error);
            // Muestra el error específico al usuario
            appendBotMessage("❌ Error al conectar con el servidor: " + error.message);
        });
}

// ==========================================================
// 3. EVENT LISTENERS (MANEJO DE EVENTOS)
// ==========================================================

// Asegurarse de que el elemento existe antes de añadir el listener
const chatInput = document.getElementById("chatbot-input");
if (chatInput) {
    // Enviar mensaje con la tecla Enter
    chatInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            sendMessage();
            e.preventDefault(); // Previene la acción por defecto (como saltos de línea)
        }
    });
}


const sendButton = document.getElementById("chatbot-send-btn");
if (sendButton) {
    // Enviar mensaje con el botón de envío
    sendButton.addEventListener("click", sendMessage);
}

const closeButton = document.getElementById("chatbot-close-btn");
if (closeButton) {
    // Cerrar chatbot con el botón ✖ 
    closeButton.addEventListener("click", () => {
        const chatContainer = document.getElementById("chatbot-container");
        if (chatContainer) {
             chatContainer.style.display = "none";
        }
    });
}

// Ocultar el contenedor al inicio (se asume que existe un botón para abrirlo)
const chatContainer = document.getElementById("chatbot-container");
if (chatContainer) {
    chatContainer.style.display = "none"; 
}

// Asegura que el chat se desplace al final al cargar (para ver el mensaje de bienvenida)
document.addEventListener('DOMContentLoaded', scrollToBottom);