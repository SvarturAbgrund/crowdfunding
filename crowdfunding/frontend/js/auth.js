document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const showRegister = document.getElementById("showRegister");
  const showLogin = document.getElementById("showLogin");

  showRegister.addEventListener("click", (e) => {
    e.preventDefault();
    loginForm.classList.add("active");
    registerForm.classList.add("active");
  });

  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    loginForm.classList.remove("active");
    registerForm.classList.remove("active");
  });
});

 const menuToggle = document.getElementById('menu-toggle');
  const navSlide = document.getElementById('nav-slide');

  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    navSlide.classList.toggle('active');
  });

  // Cerrar menú al hacer clic en un enlace (en móvil)
  document.querySelectorAll('.nav-slide a').forEach(link => {
    link.addEventListener('click', () => {
      menuToggle.classList.remove('active');
      navSlide.classList.remove('active');
    });
  });