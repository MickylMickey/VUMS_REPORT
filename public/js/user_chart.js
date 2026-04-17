async function initUserDashboard() {
  try {
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

      // --- 2. STAT CARDS ---
      if (data.stats) {
        const stats = data.stats;
        animateValue("stat-critical", stats.critical);
        animateValue("stat-active", stats.active);
        animateValue("stat-pending", stats.pending);
        animateValue("stat-resolved", stats.resolved_today);

        // Optional cards: check existence to avoid null errors
        if (document.getElementById("stat-high")) animateValue("stat-high", stats.high);
        if (document.getElementById("stat-medium")) animateValue("stat-medium", stats.medium);
        if (document.getElementById("stat-low")) animateValue("stat-low", stats.low);
        if (document.getElementById("stat-in-progress")) animateValue("stat-in-progress", stats.in_progress);
      }

      // --- 3. HR-SPECIFIC DATA (TOTAL USERS) ---
      // This only runs if the 'totalUsers' element is present in the HTML
      const totalUsersEl = document.getElementById("totalUsers");
      if (totalUsersEl && data.total_users !== undefined) {
        totalUsersEl.textContent = data.total_users;
      }

      // --- 4. HR-SPECIFIC DATA (REPORTERS LIST) ---
      const reporterList = document.getElementById("reporter-list");
      if (reporterList && data.reporters) {
        reporterList.innerHTML = data.reporters
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

      // --- 5. STATUS PIE CHART ---
      if (data.statuses) {
        renderStatusPieChart(data.statuses);
      }

      // --- 6. CRITICAL REPORTS LIST ---
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

      console.log("Dashboard fully populated.");
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
 * Helper: Animate numbers
 */
function animateValue(id, value) {
  const element = document.getElementById(id);
  if (!element) return;

  // Safety check for countUp library
  if (typeof countUp !== "undefined") {
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
  } else {
    element.innerText = value || 0;
  }
}

document.addEventListener("DOMContentLoaded", initUserDashboard);
