document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("createCampaignForm");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const user = JSON.parse(localStorage.getItem("user"));
    if (!user || user.rol !== "emprendedor") {
      alert("Solo los emprendedores pueden crear campañas.");
      return;
    }

    const data = {
      titulo: document.getElementById("titulo").value,
      categoria: document.getElementById("categoria").value,
      descripcion: document.getElementById("descripcion").value,
      meta: document.getElementById("meta").value,
      user_id: user.id
    };

    try {
      const res = await fetch("../../backend/api/campaigns.php?action=create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const text = await res.text();
      console.log("Respuesta del servidor:", text);

      const result = JSON.parse(text);
      if (result.status === "success") {
        alert("Campaña creada exitosamente. Espera aprobación del administrador.");
        window.location.href = "dashboard_emprendedor.html";
      } else {
        alert(result.message || "Error al crear campaña");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Error al conectar con el servidor");
    }
  });
});
