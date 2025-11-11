document.getElementById("contactForm").addEventListener("submit", function(e) {
  e.preventDefault(); // Evita el envío por defecto

  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const subject = document.getElementById("subject").value.trim();
  const message = document.getElementById("message").value.trim();
  const statusMessage = document.getElementById("statusMessage");

  // Validación básica
  if (!name || !email || !subject || !message) {
    statusMessage.textContent = "❌ Por favor, completa todos los campos.";
    statusMessage.className = "status error";
    return;
  }

  // Validar formato del correo electrónico
  const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!email.match(emailPattern)) {
    statusMessage.textContent = "⚠️ Ingresa un correo válido.";
    statusMessage.className = "status error";
    return;
  }

  // Simular envío (puedes conectar a PHP o Firebase luego)
  statusMessage.textContent = "✅ ¡Mensaje enviado correctamente!";
  statusMessage.className = "status success";

  // Limpiar formulario
  document.getElementById("contactForm").reset();

  // Desaparecer mensaje después de unos segundos
  setTimeout(() => {
    statusMessage.textContent = "";
  }, 4000);
});
