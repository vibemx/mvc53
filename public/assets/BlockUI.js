// Función para mostrar el bloqueador
function BlockUI(message = "Cargando...") {
    // Crear el elemento contenedor del bloqueador
    const blockUI = document.createElement("div");
    blockUI.id = "block-ui-overlay";
    blockUI.style.position = "fixed";
    blockUI.style.top = "0";
    blockUI.style.left = "0";
    blockUI.style.width = "100%";
    blockUI.style.height = "100%";
    blockUI.style.backgroundColor = "rgba(0, 0, 0, 0.5)"; // Fondo semi-transparente
    blockUI.style.zIndex = "9999"; // Asegura que esté por encima de otros elementos

    // Crear el mensaje dentro del bloqueador
    const messageBox = document.createElement("div");
    messageBox.style.position = "absolute";
    messageBox.style.top = "50%";
    messageBox.style.left = "50%";
    messageBox.style.transform = "translate(-50%, -50%)";
    messageBox.style.padding = "20px";
    messageBox.style.backgroundColor = "#ffffff";
    messageBox.style.boxShadow = "0px 0px 10px rgba(0, 0, 0, 0.5)";
    messageBox.style.borderRadius = "8px";
    messageBox.style.fontSize = "16px";
    messageBox.style.color = "#333";
    messageBox.textContent = message;

    // Agregar el mensaje al contenedor
    blockUI.appendChild(messageBox);

    // Agregar el bloqueador al body
    document.body.appendChild(blockUI);
}

// Función para ocultar el bloqueador
function unblockUI() {
    const blockUI = document.getElementById("block-ui-overlay");
    if (blockUI) {
        blockUI.remove();
    }
}

