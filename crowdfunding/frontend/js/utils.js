// utils.js

function getUser() {
  return JSON.parse(localStorage.getItem("user"));
}

function logout() {
  localStorage.removeItem("user");
  window.location.href = "../index.html";
}
