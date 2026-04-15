async function initDashboard() {
  try {
    const response = await fetch("../functions/data-api.php");

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();

    if (result.status === "success") {
      const data = result.data;

      // --- 1. POPULATE STAT CARDS ---
      if (data.stats) {
        const stats = data.stats;

        animateValue("stat-critical", stats.critical || 0);
        animateValue("stat-active", stats.active || 0);
        animateValue("stat-pending", stats.pending || 0);
        animateValue("stat-resolved", stats.resolved_today || 0);

        animateValue("stat-high", stats.high || 0);
        animateValue("stat-medium", stats.medium || 0);
        animateValue("stat-low", stats.low || 0);

        animateValue("stat-in-progress", stats.in_progress || 0);
      }

      // --- 2. CHARTS ---
      if (data.categories) {
        const catLabels = data.categories.map((item) => item.category);
        const catValues = data.categories.map((item) => item.total);

        renderBarChart("categoryChart", catLabels, catValues, "Reports by Category");
      }

      if (data.trends) {
        const trendLabels = data.trends.map((item) => item.month);
        const trendValues = data.trends.map((item) => item.total);

        renderLineChart("trendChart", trendLabels, trendValues, "Monthly Report Volume");
      }

      // --- 3. MODULES (FIXED LOCATION) ---
      if (result.status === "success") {
        const data = result.data;

        // 1. Animate the Overall Total
        animateValue("overall-total", data.overall);

        // 2. Populate the Module List
        const container = document.getElementById("module-container");
        if (container && data.modules) {
          container.innerHTML = ""; // Clear loading state

          data.modules.forEach((mod) => {
            // Calculate percentage for a progress bar effect
            const percentage = data.overall > 0 ? (mod.total_reports / data.overall) * 100 : 0;

            const row = `
                <div class="group">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-slate-700">${mod.mod_desc}</span>
                        <span class="text-sm font-bold text-slate-900">${mod.total_reports}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-indigo-500 h-full transition-all duration-1000" 
                             style="width: ${percentage}%"></div>
                    </div>
                </div>
            `;
            container.innerHTML += row;
          });
        }
      }
      // Inside initDashboard() -> if (result.status === "success")

      // 1. Render Top Reporters Table
      if (data.reporters) {
        const list = document.getElementById("reporter-list");
        list.innerHTML = data.reporters
          .map(
            (user) => `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
            <td class="py-4 font-bold text-slate-800">${user.username}</td>
            <td class="py-4 text-right">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full font-bold text-xs">
                    ${user.total}
                </span>
            </td>
        </tr>
    `,
          )
          .join("");
      }

      // 2. Render Status Pie Chart
      if (data.statuses) {
        const labels = data.statuses.map((s) => s.label);
        const values = data.statuses.map((s) => s.total);

        const ctx = document.getElementById("statusChart").getContext("2d");

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
                labels: {
                  usePointStyle: true,
                  padding: 20,
                },
              },
            },
            cutout: "70%",
          },
        });
      }
      if (data.critical_reports) {
        const container = document.getElementById("critical-list");

        if (container) {
          container.innerHTML = data.critical_reports
            .map(
              (report) => `
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition">
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    ${report.ref_num}
                </p>
                <p class="text-xs text-slate-500">
                    ${report.date}
                </p>
            </div>

            <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-lg font-bold">
                Critical
            </span>
        </div>
      `,
            )
            .join("");
        }
      }

      console.log("Dashboard fully populated.");
    } else {
      console.error("API returned success:false", result.message);
    }
  } catch (err) {
    console.error("Dashboard Init Failed:", err);
  }
}

/**
 * Helper to animate numbers using CountUp.js
 */
function animateValue(id, value) {
  const element = document.getElementById(id);
  if (!element) return;

  // target, endVal, options
  const demo = new countUp.CountUp(id, value || 0, {
    duration: 2, // seconds
    useEasing: true, // smooth start/stop
    useGrouping: true, // adds commas for thousands
  });

  if (!demo.error) {
    demo.start();
  } else {
    console.error(demo.error);
    element.innerText = value; // Fallback to static text
  }
}

/**
 * Renders a Bar Chart for Categories
 */
function renderBarChart(canvasId, labels, data, title) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return; // Guard clause if element is missing

  const ctx = canvas.getContext("2d");

  // Destroy existing instance to prevent "ghosting" on hover
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
          backgroundColor: "#4F46E5", // Indigo-600
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: { beginAtZero: true, grid: { display: false } },
        x: { grid: { display: false } },
      },
    },
  });
}

/**
 * Renders a Line Chart for Trends
 */
function renderLineChart(canvasId, labels, data, title) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;

  const ctx = canvas.getContext("2d");

  if (window[canvasId] instanceof Chart) {
    window[canvasId].destroy();
  }

  window[canvasId] = new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: title,
          data: data,
          borderColor: "#2563eb", // Blue-600
          backgroundColor: "rgba(37, 99, 235, 0.1)",
          borderWidth: 3,
          tension: 0.4, // Smoother curve
          fill: true,
          pointBackgroundColor: "#2563eb",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: { beginAtZero: true },
        x: { grid: { display: false } },
      },
    },
  });
}

// Auto-initialize when the DOM is ready
document.addEventListener("DOMContentLoaded", initDashboard);

let statusChartInstance = null;
