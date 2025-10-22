document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  const container = document.getElementById("dashboard-content");
  if (!user) { alert("Inicia sesión"); window.location.href = "profile.html"; return; }

  try {
    let url;
    if (user.rol === 'emprendedor') {
      url = `../backend/api/dashboard.php?action=emprendedor&user_id=${user.id}`;
    } else if (user.rol === 'inversionista') {
      url = `../backend/api/dashboard.php?action=inversionista&user_id=${user.id}`;
    } else {
      url = `../backend/api/dashboard.php?action=admin`;
    }

    const res = await fetch(url);
    const text = await res.text();
    console.log("Response from server:", text);
    
    // Check if response is empty
    if (!text.trim()) {
      container.innerHTML = `<p>Error: La respuesta del servidor está vacía</p>`;
      return;
    }
    
    // Try parsing JSON with error handling
    let result;
    try {
      result = JSON.parse(text);
    } catch (parseError) {
      console.error("JSON Parse Error:", parseError);
      container.innerHTML = `<p>Error: La respuesta del servidor no es JSON válido</p>
                           <p>Respuesta recibida: ${text}</p>`;
      return;
    }

    if (result.status !== 'success') {
      container.innerHTML = `<p>Error: ${result.msg}</p>`;
      return;
    }

    renderDashboard(user.rol, result, container);
  } catch (err) {
    console.error("Connection error:", err);
    container.innerHTML = `<p>Error de conexión: ${err.message}</p>`;
  }
});

function renderDashboard(rol, result, container) {
  if (rol === 'emprendedor') {
    const rows = result.data || [];
    if (!rows.length) {
      container.innerHTML = "<p>No tienes campañas aún.</p>";
    } else {
      container.innerHTML = `<h2>Tus campañas</h2>` + rows.map(c => `
        <div class="card">
          <h3>${c.titulo}</h3>
          <p>${c.descripcion}</p>
          <p>Meta: $${c.meta} — Recaudado: $${c.recaudado}</p>
          <p>Estado: ${c.estado}</p>
          <a href="../index.html" onclick="event.stopPropagation()">Ver en público</a>
        </div>
      `).join('');
    }
  }

  if (rol === 'inversionista') {
    const rows = result.data || [];
    if (!rows.length) {
      container.innerHTML = "<p>No has donado aún.</p>";
    } else {
      container.innerHTML = `<h2>Tus donaciones</h2>` + rows.map(d => `
        <div class="card">
          <h3>${d.campaña}</h3>
          <p>Monto: $${d.monto}</p>
          <p>Fecha: ${d.fecha}</p>
        </div>
      `).join('');
    }
  }

  if (rol === 'admin' || rol === 'moderador') {
    const campaigns = result.campaigns || [];
    const users = result.users || [];
    container.innerHTML = `<h2>Campañas</h2>` + campaigns.map(c => `
      <div class="card">
        <h3>${c.titulo}</h3>
        <p>Autor: ${c.creador} (${c.email})</p>
        <p>Meta: $${c.meta} — Recaudado: $${c.recaudado}</p>
        <p>Estado: ${c.estado}</p>
        <button onclick="updateItem(${c.id}, 'campaigns', {estado:'aprobada'})">Aprobar</button>
        <button onclick="updateItem(${c.id}, 'campaigns', {estado:'rechazada'})">Rechazar</button>
        <button onclick="deleteItem(${c.id}, 'campaigns')">Eliminar</button>
      </div>
    `).join('') + `<h2>Usuarios</h2>` + users.map(u => `
      <div class="card">
        <p>${u.nombre} — ${u.email} — ${u.rol}</p>
        <button onclick="deleteItem(${u.id}, 'users')">Eliminar usuario</button>
      </div>
    `).join('');
  }
}

async function updateItem(id, table, fields) {
  try {
    const payload = { table, id, fields };
    const res = await fetch("../backend/api/dashboard.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    
    const text = await res.text();
    console.log("Update response:", text);
    
    const data = JSON.parse(text);
    alert(data.msg);
    location.reload();
  } catch (err) {
    console.error("Update error:", err);
    alert("Error al actualizar: " + err.message);
  }
}

async function deleteItem(id, table) {
  if (!confirm("Confirmar eliminación")) return;
  
  try {
    const res = await fetch("../backend/api/dashboard.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ table, id })
    });
    
    const text = await res.text();
    console.log("Delete response:", text);
    
    const data = JSON.parse(text);
    alert(data.msg);
    location.reload();
  } catch (err) {
    console.error("Delete error:", err);
    alert("Error al eliminar: " + err.message);
  }
}
