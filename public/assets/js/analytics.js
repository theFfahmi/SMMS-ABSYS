const baseUrl = window.location.origin + '/';
let analyticsChart = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchData();
});

function fetchData() {
    fetch(`${baseUrl}analytics/getData`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        animateValue('stat-reach', 0, data.metrics.reach, 1500);
        animateValue('stat-impressions', 0, data.metrics.impressions, 1500);
        animateValue('stat-engagement', 0, data.metrics.engagement, 1500);
        document.getElementById('stat-engagement-rate').innerText = data.metrics.engagement_rate + '%';
        
        animateValue('br-likes', 0, data.metrics.likes, 1000);
        animateValue('br-comments', 0, data.metrics.comments, 1000);
        animateValue('br-shares', 0, data.metrics.shares, 1000);
        animateValue('br-saves', 0, data.metrics.saves, 1000);
        animateValue('br-clicks', 0, data.metrics.clicks, 1000);
        animateValue('br-followers', 0, data.metrics.followers, 1000, '+');

        renderPlatformStats(data.performanceList);
        renderTopContent(data.performanceList);
        renderChart(data.chartData);
    })
    .catch(err => console.error('Fetch error:', err));
}

function animateValue(id, start, end, duration, prefix = '') {
    if (start === end) {
        document.getElementById(id).innerHTML = prefix + formatNumber(end);
        return;
    }
    let range = end - start;
    let current = start;
    let increment = end > start ? Math.ceil(range / (duration / 16)) : -1;
    let stepTime = Math.abs(Math.floor(duration / range));
    if (stepTime < 16) stepTime = 16;
    
    let obj = document.getElementById(id);
    let timer = setInterval(function() {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        obj.innerHTML = prefix + formatNumber(current);
    }, stepTime);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderPlatformStats(list) {
    let platforms = {};
    list.forEach(p => {
        if (!platforms[p.platform_name]) {
            platforms[p.platform_name] = { reach: 0, engagement: 0, icon: p.platform_icon, color: p.platform_color };
        }
        platforms[p.platform_name].reach += parseInt(p.reach);
        platforms[p.platform_name].engagement += parseInt(p.total_engagement);
    });
    
    const container = document.getElementById('platform-stats-list');
    if (!container) return;
    container.innerHTML = '';
    
    Object.keys(platforms).sort((a,b) => platforms[b].engagement - platforms[a].engagement).forEach(name => {
        let p = platforms[name];
        let pClass = name.toLowerCase();
        container.innerHTML += `
            <div class="platform-stat-item">
                <div class="platform-icon ${pClass}"><i class="bi ${p.icon}"></i></div>
                <div class="platform-info">
                    <div class="platform-name">${name}</div>
                    <div class="platform-metrics">
                        <div class="platform-metric">Reach: <strong>${formatNumber(p.reach)}</strong></div>
                        <div class="platform-metric">Engage: <strong>${formatNumber(p.engagement)}</strong></div>
                    </div>
                </div>
            </div>
        `;
    });
}

function renderTopContent(list) {
    const container = document.getElementById('top-content-list');
    if (!container) return;
    container.innerHTML = '';
    
    if (list.length === 0) {
        container.innerHTML = '<div class="analytics-empty"><i class="bi bi-bar-chart"></i><h4>Belum ada data analytics</h4></div>';
        return;
    }
    
    let sorted = [...list].sort((a,b) => b.total_engagement - a.total_engagement).slice(0, 7);
    
    sorted.forEach((p, index) => {
        let rankClass = index === 0 ? 'rank-1' : (index === 1 ? 'rank-2' : (index === 2 ? 'rank-3' : ''));
        container.innerHTML += `
            <div class="top-content-item">
                <div class="top-content-rank ${rankClass}">${index + 1}</div>
                <div class="top-content-info">
                    <div class="top-content-title">${p.content_title || 'Konten #' + p.content_id}</div>
                    <div class="top-content-meta">${p.platform_name} &bull; ${new Date(p.recorded_at).toLocaleDateString('id-ID')}</div>
                </div>
                <div class="top-content-stats">
                    <div class="top-content-engagement">${formatNumber(p.total_engagement)} <span style="font-size:10px;color:var(--cp-muted)">Engagements</span></div>
                    <div class="top-content-label">${p.calc_engagement_rate}% ER &bull; ${formatNumber(p.reach)} Reach</div>
                </div>
            </div>
        `;
    });
}

function renderChart(chartData) {
    const ctx = document.getElementById('analyticsChart');
    if (!ctx) return;

    if (analyticsChart) {
        analyticsChart.destroy();
    }
    
    // Create gradient for Reach
    const gradientReach = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientReach.addColorStop(0, 'rgba(45, 108, 223, 0.4)');
    gradientReach.addColorStop(1, 'rgba(45, 108, 223, 0)');

    // Create gradient for Engagement
    const gradientEngage = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientEngage.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
    gradientEngage.addColorStop(1, 'rgba(139, 92, 246, 0)');

    analyticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Total Reach',
                    data: chartData.reach,
                    borderColor: '#2d6cdf',
                    backgroundColor: gradientReach,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2d6cdf',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Total Engagement',
                    data: chartData.engagement,
                    borderColor: '#8b5cf6',
                    backgroundColor: gradientEngage,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#8b5cf6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { family: "'Inter', sans-serif", weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleFont: { family: "'Inter', sans-serif", size: 13 },
                    bodyFont: { family: "'Inter', sans-serif", size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
}
