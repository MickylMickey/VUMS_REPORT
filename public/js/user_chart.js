async function initUserDashboard() {
  try {
    // 1. Fetch from the user-specific endpoint
    const response = await fetch("../functions/user-data-api.php");

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();

    if (result.status === "success") {
      const data = result.data;

      // --- 1. OVERALL TOTAL ---
      if (data.overall !== undefined) {
        animateValue("overall-total", data.overall);
      }

      // --- 2. STAT CARDS (USER-SCOPED) ---
      if (data.stats) {
        const stats = data.stats;
        animateValue("stat-critical", stats.critical);
        animateValue("stat-active", stats.active);
        animateValue("stat-pending", stats.pending);
        animateValue("stat-resolved", stats.resolved_today);

        // Optional: High/Medium/Low cards if they exist in your User UI
        if (document.getElementById("stat-high")) animateValue("stat-high", stats.high);
        if (document.getElementById("stat-medium")) animateValue("stat-medium", stats.medium);
        if (document.getElementById("stat-low")) animateValue("stat-low", stats.low);
        if (document.getElementById("stat-in-progress")) animateValue("stat-in-progress", stats.in_progress);
      }

      // --- 3. CHARTS ---

      // --- 4. STATUS PIE CHART (USER-SCOPED) ---
      if (data.statuses) {
        renderStatusPieChart(data.statuses);
      }

      // --- 5. MY CRITICAL REPORTS LIST ---
      if (data.critical_reports) {
        const container = document.getElementById("critical-list");
        if (container) {
          container.innerHTML =
            data.critical_reports.length > 0
              ? data.critical_reports
                  .map(
                    (report) => `
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">${report.ref_num}</p>
                        <p class="text-xs text-slate-500">${report.date}</p>
                    </div>
                    <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-lg font-bold">Critical</span>
                </div>
              `,
                  )
                  .join("")
              : `<p class="text-sm text-slate-400 text-center py-4">No pending critical reports.</p>`;
        }
      }

      console.log("User Dashboard fully populated.");
    } else {
      console.error("API Error:", result.message);
    }
  } catch (err) {
    console.error("Dashboard Init Failed:", err);
  }
}

/**
 * Renders the Status Doughnut Chart
 */
let statusChartInstance = null;
function renderStatusPieChart(statusData) {
  const canvas = document.getElementById("statusChart");
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  const labels = statusData.map((s) => s.label);
  const values = statusData.map((s) => s.total);

  if (statusChartInstance) {
    statusChartInstance.destroy();
  }

  statusChartInstance = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [
        {
          data: values,
          backgroundColor: ["#4F46E5", "#10B981", "#F59E0B", "#EF4444"],
          borderWidth: 0,
          hoverOffset: 4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: { usePointStyle: true, padding: 20 },
        },
      },
      cutout: "70%",
    },
  });
}

/**
 * Reusable Helper: Animate numbers using CountUp.js
 */
function animateValue(id, value) {
  const element = document.getElementById(id);
  if (!element) return;

  const demo = new countUp.CountUp(id, value || 0, {
    duration: 2,
    useEasing: true,
    useGrouping: true,
  });

  if (!demo.error) {
    demo.start();
  } else {
    element.innerText = value || 0;
  }
}

/**
 * Reusable Helper: Bar Chart
 */
function renderBarChart(canvasId, labels, data, title) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  if (window[canvasId] instanceof Chart) {
    window[canvasId].destroy();
  }

  window[canvasId] = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: title,
          data: data,
          backgroundColor: "#4F46E5",
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, grid: { display: false } },
        x: { grid: { display: false } },
      },
    },
  });
}

// Start
document.addEventListener("DOMContentLoaded", initUserDashboard);
