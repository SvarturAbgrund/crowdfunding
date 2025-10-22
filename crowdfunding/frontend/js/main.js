// main.js → maneja cambios dinámicos del menú en index.html

document.addEventListener("DOMContentLoaded", () => {
  const user = JSON.parse(localStorage.getItem("user"));
  const navMenu = document.querySelector(".nav-menu");

  if (user) {
    const loginItem = navMenu.querySelector('a[href="pages/profile.html"]');
    if (loginItem) {
      loginItem.textContent = "Perfil";
      loginItem.href = "#"; // Cambiar a un enlace vacío para manejar la redirección con JavaScript
      loginItem.onclick = redirectToProfile; // Asignar la función de redirección
    }
  }
});

function redirectToProfile() {
  const user = JSON.parse(localStorage.getItem("user"));
  
  if (user) {
    switch (user.rol) {
      case "emprendedor":
        window.location.href = "pages/dashboard_emprendedor.html"; // Asegúrate de que esta ruta sea correcta
        break;
      case "inversionista":
        window.location.href = "pages/dashboard_inversionista.html"; // Asegúrate de que esta ruta sea correcta
        break;
      case "admin":
      case "moderador":
        window.location.href = "pages/dashboard_admin.html"; // Asegúrate de que esta ruta sea correcta
        break;
      default:
        alert("Rol desconocido. Redirigiendo a inicio.");
        window.location.href = "index.html";
    }
  } else {
    alert("No has iniciado sesión. Redirigiendo a inicio.");
    window.location.href = "index.html";
  }
}