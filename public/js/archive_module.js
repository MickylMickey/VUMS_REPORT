document.addEventListener("DOMContentLoaded", function () {
  // === 1. SYSTEM REPORTS FILTERING ===
  const searchInput = document.getElementById("searchInput");
  const severityFilter = document.getElementById("severityFilter");
  const resetBtn = document.getElementById("resetBtn");
  const reportRows = document.querySelectorAll(".report-row");
  const noResultsRow = document.getElementById("noResultsRow");

  function applyReportFilters() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const severityVal = severityFilter.value;
    let visibleCount = 0;

    reportRows.forEach((row) => {
      // 1. Get all data attributes (including the new data-desc)
      const ref = row.getAttribute("data-ref").toLowerCase();
      const reporter = row.getAttribute("data-reporter").toLowerCase();
      const description = (row.getAttribute("data-desc") || "").toLowerCase(); // New
      const severityID = row.getAttribute("data-severity");

      // 2. Search Logic (Ref OR Reporter OR Description)
      const matchesSearch = searchTerm === "" || ref.includes(searchTerm) || reporter.includes(searchTerm) || description.includes(searchTerm); // Included description in search

      // 3. Filter Logic
      const matchesSeverity = severityVal === "" || severityID === severityVal;

      // 4. Final Visibility Check
      if (matchesSearch && matchesSeverity) {
        row.classList.remove("hidden");
        visibleCount++;
      } else {
        row.classList.add("hidden");
      }
    });

    // 5. Handle "No Results" display for the Table
    if (noResultsRow) {
      if (visibleCount === 0) {
        noResultsRow.classList.remove("hidden");
      } else {
        noResultsRow.classList.add("hidden");
      }
    }
  }

  // Report Listeners
  if (searchInput) searchInput.addEventListener("input", applyReportFilters);
  if (severityFilter) severityFilter.addEventListener("change", applyReportFilters);
  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      searchInput.value = "";
      severityFilter.value = "";
      applyReportFilters();
    });
  }

  // === 2. SUGGESTIONS FILTERING ===
  const sugSearchInput = document.getElementById("sugSearchInput");
  const sugCards = document.querySelectorAll(".suggestion-card");
  const noSugResults = document.getElementById("noSugResults");
  const resetSugBtn = document.getElementById("resetSugBtn");

  function filterSuggestions() {
    const query = sugSearchInput.value.toLowerCase().trim();
    let hasResults = false;

    sugCards.forEach((card) => {
      const user = card.getAttribute("data-user").toLowerCase();
      const text = card.getAttribute("data-text").toLowerCase();

      if (user.includes(query) || text.includes(query)) {
        card.classList.remove("hidden");
        hasResults = true;
      } else {
        card.classList.add("hidden");
      }
    });

    if (noSugResults) {
      hasResults ? noSugResults.classList.add("hidden") : noSugResults.classList.remove("hidden");
    }
  }

  // Suggestion Listeners
  if (sugSearchInput) sugSearchInput.addEventListener("input", filterSuggestions);
  if (resetSugBtn) {
    resetSugBtn.addEventListener("click", () => {
      sugSearchInput.value = "";
      filterSuggestions();
    });
  }
});
