document.addEventListener("DOMContentLoaded", () => {
  loadCampaigns();
});

async function loadCampaigns() {
  const container = document.querySelector(".grid-layout");
  if (!container) return;

  try {
    const res = await fetch("backend/api/campaigns.php?action=list");
    const text = await res.text();
    console.log("Respuesta del servidor:", text);

    const result = JSON.parse(text);

    if (result.status !== "success") {
      container.innerHTML = `<p>Error: ${result.msg}</p>`;
      return;
    }

    if (result.data.length === 0) {
      container.innerHTML = `<p>No hay campañas disponibles por ahora.</p>`;
      return;
    }

    // Mostrar campañas activas (aprobadas)
    container.innerHTML = result.data.map(c => `
      <div class="campaign small" onclick="showCampaignDetails(${c.id})">
        <div class="image-hover">
          <img src="assets/img/default.jpg" alt="${c.titulo}">
          <div class="hover-description">${c.descripcion}</div>
        </div>
        <h4>${c.titulo}</h4>
        <p>Recaudado: $${c.recaudado} / $${c.meta}</p>
        <div class="progress"><div style="width:${(c.recaudado / c.meta) * 100}%"></div></div>
      </div>
    `).join('');

  } catch (err) {
    console.error("Error al cargar campañas:", err);
    container.innerHTML = `<p>Error de conexión al cargar campañas.</p>`;
  }
}

function showCampaignDetails(id) {
  // Redirige a la página de detalles (o abre modal más adelante)
  window.location.href = `pages/campaign_details.html?id=${id}`;
}
