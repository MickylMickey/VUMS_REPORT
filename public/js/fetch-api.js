async function loadDashboardData() {
  try {
    const response = await fetch("../functions/data-api.php");

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    console.log("VUMS Data Received:", data);

    updateCategoryChart(data.categories);
    updateTrendChart(data.trends);
  } catch (error) {
    console.error("Could not fetch dashboard data:", error);
  }
}

// Call the function when the page loads
document.addEventListener("DOMContentLoaded", loadDashboardData);
