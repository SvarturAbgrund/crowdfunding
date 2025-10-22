document.addEventListener("DOMContentLoaded", () => {
    const user = JSON.parse(localStorage.getItem("user"));
    
    if (!user) {
        alert("Por favor inicia sesión");
        window.location.href = "../index.html";
        return;
    }

    loadUserCampaigns(user.id);
});

async function loadUserCampaigns(userId) {
    try {
        const response = await fetch(`../../backend/api/campaigns.php?action=getUserCampaigns&userId=${userId}`);
        const result = await response.json();

        if (result.status === "success") {
            displayCampaigns(result.data);
            updateStats(result.data);
        } else {
            throw new Error(result.message || "Error al cargar las campañas");
        }
    } catch (error) {
        console.error("Error:", error);
        const campaignGrid = document.querySelector('.campaign-grid');
        if (campaignGrid) {
            campaignGrid.innerHTML = `<p class="error-message">Error al cargar las campañas: ${error.message}</p>`;
        }
    }
}

function displayCampaigns(campaigns) {
    const campaignGrid = document.querySelector('.campaign-grid');
    if (!campaignGrid) return;
    
    if (!campaigns || campaigns.length === 0) {
        campaignGrid.innerHTML = '<p>No tienes campañas creadas aún.</p>';
        return;
    }

    campaignGrid.innerHTML = campaigns.map(campaign => `
        <div class="campaign-card">
            <h3>${campaign.titulo}</h3>
            <p>Meta: $${parseFloat(campaign.meta).toLocaleString()}</p>
            <p>Recaudado: $${parseFloat(campaign.recaudado || 0).toLocaleString()}</p>
            <p>Estado: ${campaign.estado}</p>
            <div class="progress-bar">
                <div class="progress" style="width: ${((campaign.recaudado/campaign.meta) * 100 || 0).toFixed(1)}%"></div>
            </div>
            <div class="actions">
                <a href="edit_campaign.html?id=${campaign.id}" class="btn">Editar</a>
                <button class="btn delete" onclick="deleteCampaign(${campaign.id})">Eliminar</button>
            </div>
        </div>
    `).join('');
}

function updateStats(campaigns) {
    const activeCampaignsElement = document.querySelector('.stats-overview .stat-card:first-child .number');
    const totalRecaudadoElement = document.querySelector('.stats-overview .stat-card:last-child .number');
    
    if (!activeCampaignsElement || !totalRecaudadoElement) return;

    const activeCampaigns = campaigns.filter(c => c.estado === "aprobada").length;
    const totalRecaudado = campaigns.reduce((sum, c) => sum + parseFloat(c.recaudado || 0), 0);
    
    activeCampaignsElement.textContent = activeCampaigns;
    totalRecaudadoElement.textContent = `$${totalRecaudado.toLocaleString()}`;
}