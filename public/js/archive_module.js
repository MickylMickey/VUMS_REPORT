document.addEventListener("DOMContentLoaded", function () {
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
      
      const ref = row.getAttribute("data-ref").toLowerCase();
      const reporter = row.getAttribute("data-reporter").toLowerCase();
      const description = (row.getAttribute("data-desc") || "").toLowerCase(); // New
      const severityID = row.getAttribute("data-severity");

      const matchesSearch = searchTerm === "" || ref.includes(searchTerm) || reporter.includes(searchTerm) || description.includes(searchTerm); // Included description in search


      const matchesSeverity = severityVal === "" || severityID === severityVal;

    
      if (matchesSearch && matchesSeverity) {
        row.classList.remove("hidden");
        visibleCount++;
      } else {
        row.classList.add("hidden");
      }
    });

    
    if (noResultsRow) {
      if (visibleCount === 0) {
        noResultsRow.classList.remove("hidden");
      } else {
        noResultsRow.classList.add("hidden");
      }
    }
  }


  if (searchInput) searchInput.addEventListener("input", applyReportFilters);
  if (severityFilter) severityFilter.addEventListener("change", applyReportFilters);
  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      searchInput.value = "";
      severityFilter.value = "";
      applyReportFilters();
    });
  }

  
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


  if (sugSearchInput) sugSearchInput.addEventListener("input", filterSuggestions);
  if (resetSugBtn) {
    resetSugBtn.addEventListener("click", () => {
      sugSearchInput.value = "";
      filterSuggestions();
    });
  }
});


function openViewModal(data) {
    const modal = document.getElementById('viewModal');
    const backdrop = document.getElementById('viewModalBackdrop');
    const container = document.getElementById('viewModalContainer');
    const imgElement = document.getElementById('view_attachment');
    const placeholder = document.getElementById('no_img_placeholder');

  
    document.getElementById('view_category').innerText = data.category;
    document.getElementById('view_module').innerText = data.module;
    document.getElementById('view_desc').innerText = data.description;
    

    const sevBadge = document.getElementById('view_severity');
    sevBadge.innerText = data.severity;
    
    let bgColor, textColor, borderColor;
    const sev = data.severity.toLowerCase();

   
    if (sev === 'critical') {
        bgColor = '#fef2f2'; textColor = '#dc2626'; borderColor = '#fee2e2';
    } else if (sev === 'high') {
        bgColor = '#fff7ed'; textColor = '#ea580c'; borderColor = '#ffedd5';
    } else if (sev === 'medium') {
        bgColor = '#fffbeb'; textColor = '#d97706'; borderColor = '#fef3c7';
    } else {
        bgColor = '#ecfdf5'; textColor = '#059669'; borderColor = '#d1fae5';
    }

    sevBadge.style.backgroundColor = bgColor;
    sevBadge.style.color = textColor;
    sevBadge.style.borderColor = borderColor;

     if (data.image && data.image.toString().trim() !== "") {
        imgElement.src = "uploads/" + data.image; 
        imgElement.style.display = "block";
        placeholder.style.display = "none";
    } else {
        imgElement.src = "";
        imgElement.style.display = "none";
        placeholder.style.display = "block";
    }

  
    modal.style.display = 'flex';
    setTimeout(() => {
        backdrop.style.opacity = '1';
        container.style.opacity = '1';
        container.style.transform = 'scale(1)';
    }, 10);
}

//function para isara ang view details modal meow meow.
function closeViewModal() {
    const modal = document.getElementById('viewModal');
    const backdrop = document.getElementById('viewModalBackdrop');
    const container = document.getElementById('viewModalContainer');

   
    backdrop.style.opacity = '0';
    container.style.opacity = '0';
    container.style.transform = 'scale(0.95)';

  
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}