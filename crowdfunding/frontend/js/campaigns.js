document.addEventListener("DOMContentLoaded", () => {
    loadCampaigns();
});

async function loadCampaigns() {
    const container = document.getElementById("campaigns-container");
    
    try {
        // Fixing the fetch path to point to the correct location
        const response = await fetch("../backend/api/campaigns.php?action=list");
        
        // Log the response for debugging
        console.log("Response status:", response.status);
        console.log("Response headers:", response.headers);
        
        // Check if response is ok before parsing
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get the response text first for debugging
        const text = await response.text();
        console.log("Raw response:", text);
        
        // Try to parse the text as JSON
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error(`JSON parse error: ${e.message}\nRaw response: ${text}`);
        }

        if (result.status === "success") {
            displayCampaigns(result.data);
        } else {
            throw new Error(result.message || "Error al cargar las campañas");
        }
    } catch (error) {
        console.error("Error details:", error);
        if (container) {
            container.innerHTML = `
                <p class="error-message">
                    Error al cargar las campañas: ${error.message}
                    <br>
                    <small>Por favor, revisa la consola para más detalles.</small>
                </p>`;
        }
    }
}

function displayCampaigns(campaigns) {
    const container = document.getElementById('campaigns-container');
    
    if (!container) {
        console.error('No se encontró el contenedor de campañas');
        return;
    }
    
    if (!campaigns || campaigns.length === 0) {
        container.innerHTML = '<p>No hay campañas disponibles en este momento.</p>';
        return;
    }

    container.innerHTML = campaigns.map(campaign => {
        // Validar y establecer valores predeterminados para evitar errores
        const titulo = campaign.titulo || 'Sin título';
        const descripcion = campaign.descripcion || 'Sin descripción';
        const meta = parseFloat(campaign.meta || 0);
        const recaudado = parseFloat(campaign.recaudado || 0);
        const progress = Math.min(100, (recaudado / meta) * 100);
        
        // Formatear la fecha
        let formattedDate = 'Fecha no disponible';
        try {
            formattedDate = new Date(campaign.fecha_creacion).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (error) {
            console.error('Error al formatear la fecha:', error);
        }
        
        return `
            <div class="campaign-card">
                <div class="campaign-image">
                    <img src="${campaign.imagen || 'assets/images/default-campaign.jpg'}" 
                         alt="${titulo}"
                         onerror="this.src='assets/images/default-campaign.jpg'">
                </div>
                <div class="campaign-content">
                    <h3>${titulo}</h3>
                    <p class="campaign-creator">Por: ${campaign.creador || 'Anónimo'}</p>
                    <p class="campaign-description">${descripcion.substring(0, 100)}...</p>
                    <div class="campaign-stats">
                        <div class="progress-bar">
                            <div class="progress" style="width: ${progress.toFixed(1)}%"></div>
                        </div>
                        <p class="campaign-numbers">
                            <span>$${recaudado.toLocaleString('es-ES')}</span> de 
                            <span>$${meta.toLocaleString('es-ES')}</span>
                        </p>
                        <p class="campaign-date">Creado el ${formattedDate}</p>
                    </div>
                    <a href="pages/campaign_details.html?id=${campaign.id}" class="btn">Ver más</a>
                </div>
            </div>
        `;
    }).join('');

    // Agregar paginación si hay más de 6 campañas
    handlePagination(campaigns);
}

function handlePagination(campaigns) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    if (campaigns.length > 6) {
        const pageCount = Math.ceil(campaigns.length / 6);
        pagination.style.display = 'flex';
        pagination.innerHTML = createPaginationControls(pageCount);
    } else {
        pagination.style.display = 'none';
    }
}

function createPaginationControls(pageCount) {
    let controls = '';
    for (let i = 1; i <= pageCount; i++) {
        controls += `<a href="#" class="page-number ${i === 1 ? 'active' : ''}">${i}</a>`;
    }
    return controls;
}

function showCampaignDetails(campaignId) {
    if (!campaignId) {
        console.error('ID de campaña no proporcionado');
        return;
    }
    window.location.href = `pages/campaign_details.html?id=${campaignId}`;
}