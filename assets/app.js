// app.js - funciones JS mínimas
console.log('Crowdfunding app loaded');
// TODO: polling para dashboard en tiempo real

// Polling para dashboard: actualiza cada 5s si existe #dashboard
function fetchDashboard(){
	const dash = document.getElementById('dashboard');
	if(!dash) return;
	fetch('/crowdfunding1/campaigns/status.php',{credentials:'same-origin'})
		.then(r=>r.json())
		.then(data=>{
			if(!data.ok) return;
			data.campaigns.forEach(c=>{
				const el = document.querySelector('.campaign[data-id="'+c.id+'"]');
				if(!el) return;
				const pledged = el.querySelector('.pledged');
				const backers = el.querySelector('.backers');
				const status = el.querySelector('.status');
				if(pledged) pledged.textContent = parseFloat(c.pledged_amount).toFixed(2);
				if(backers) backers.textContent = c.backers;
				if(status) status.textContent = c.status;
			});
		}).catch(()=>{});
}
setInterval(fetchDashboard, 5000);
document.addEventListener('DOMContentLoaded', fetchDashboard);
