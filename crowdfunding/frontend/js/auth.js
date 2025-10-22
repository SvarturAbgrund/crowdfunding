document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");

  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const data = {
        nombre: document.getElementById("nombre").value,
        email: document.getElementById("email").value,
        password: document.getElementById("password").value,
        rol: document.getElementById("rol").value,
      };

      try {
        const res = await fetch("../../backend/api/auth.php?action=register", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });

        const text = await res.text();
        console.log("Respuesta cruda del servidor (register):", text);

        let result;
        try {
          result = JSON.parse(text);
        } catch (err) {
          console.error("JSON parse error (register):", err);
          alert("Respuesta inválida del servidor al registrar.");
          return;
        }

        if (result.status === "success") {
          alert("Registro exitoso. Ahora puedes iniciar sesión.");
          // Redirigir a la página de login o perfil según prefieras
          window.location.href = "login.html";
          return;
        } else {
          alert(result.msg || "Error al registrar");
          return;
        }
      } catch (error) {
        console.error("Error en fetch (register):", error);
        alert("Error al registrar. Intenta nuevamente.");
        return;
      }
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      await loginUser();
    });
  }
});

async function loginUser() {
  const email = document.getElementById("email_login").value;
  const password = document.getElementById("password_login").value;

  if (!email || !password) {
    alert("Por favor completa email y contraseña.");
    return;
  }

  try {
    const res = await fetch("../../backend/api/auth.php?action=login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });

    const text = await res.text();
    console.log("Respuesta del servidor (login):", text);

    let result;
    try {
      result = JSON.parse(text);
    } catch (err) {
      console.error("JSON parse error (login):", err);
      alert("Respuesta inválida del servidor al iniciar sesión.");
      return;
    }

    if (result.status === "success") {
      localStorage.setItem("user", JSON.stringify(result.user));

      // Redirigir según rol
      if (result.user.rol === "emprendedor") {
        window.location.href = "dashboard_emprendedor.html";
        return;
      }
      if (result.user.rol === "inversionista") {
        window.location.href = "dashboard_inversionista.html";
        return;
      }
      if (result.user.rol === "admin" || result.user.rol === "moderador") {
        window.location.href = "dashboard_admin.html";
        return;
      }

      // Fallback si no hay rol conocido
      alert("Inicio de sesión exitoso");
      window.location.href = "../index.html";
      return;
    } else {
      alert(result.msg || "Error al iniciar sesión");
      return;
    }
  } catch (error) {
    console.error("Error (fetch login):", error);
    alert("Error al iniciar sesión. Verifica tu conexión e intenta nuevamente.");
    return;
  }
}