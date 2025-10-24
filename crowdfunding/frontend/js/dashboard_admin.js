document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (!user || user.rol !== "admin") {
    alert("Acceso denegado");
    window.location.href = "../index.html";
    return;
  }

  await loadAdminData();

  document.getElementById("logout").addEventListener("click", () => {
    localStorage.removeItem("user");
    window.location.href = "../index.html";
  });
});

async function loadAdminData() {
  try {
    const res = await fetch("../../backend/api/admin.php?action=getAllData");
    const text = await res.text();
    console.log("Respuesta del servidor:", text);

    const data = JSON.parse(text);
    if (data.status !== "success") throw new Error(data.msg);

    renderCampaigns(data.campaigns);
    renderUsers(data.users);
  } catch (err) {
    console.error("Error al cargar datos del admin:", err);
    document.querySelector("main").innerHTML = `<p>Error: ${err.message}</p>`;
  }
}

function renderCampaigns(campaigns) {
  const tbody = document.querySelector("#campaigns tbody");
  tbody.innerHTML = campaigns.map(c => `
    <tr>
      <td>${c.id}</td>
      <td>${c.titulo}</td>
      <td>${c.creador}</td>
      <td>${c.estado}</td>
      <td>
        <button class="btn approve" onclick="updateCampaign(${c.id}, 'aprobada')">Aprobar</button>
        <button class="btn reject" onclick="updateCampaign(${c.id}, 'rechazada')">Rechazar</button>
        <button class="btn delete" onclick="deleteCampaign(${c.id})">Eliminar</button>
      </td>
    </tr>
  `).join('');
}

function renderUsers(users) {
  const tbody = document.querySelector("#users tbody");
  tbody.innerHTML = users.map(u => `
    <tr>
      <td>${u.id}</td>
      <td>${u.nombre}</td>
      <td>${u.email}</td>
      <td>${u.rol}</td>
      <td><button class="btn delete" onclick="deleteUser(${u.id})">Eliminar</button></td>
    </tr>
  `).join('');
}

async function updateCampaign(id, estado) {
  if (!confirm(`¿Seguro que deseas marcar como ${estado}?`)) return;

  const res = await fetch("../../backend/api/admin.php?action=updateCampaign", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, estado })
  });
  const data = await res.json();
  alert(data.msg);
  loadAdminData();
}

async function deleteCampaign(id) {
  if (!confirm("¿Eliminar esta campaña?")) return;

  const res = await fetch("../../backend/api/admin.php?action=deleteCampaign", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });
  const data = await res.json();
  alert(data.msg);
  loadAdminData();
}

async function deleteUser(id) {
  if (!confirm("¿Eliminar este usuario?")) return;

  const res = await fetch("../../backend/api/admin.php?action=deleteUser", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });
  const data = await res.json();
  alert(data.msg);
  loadAdminData();
}
