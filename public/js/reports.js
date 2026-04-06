document.querySelectorAll(".edit-report-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    document.getElementById("edit_report_id").value = this.dataset.id;
    document.getElementById("edit_cat_id").value = this.dataset.cat;
    document.getElementById("edit_mod_id").value = this.dataset.mod;
    document.getElementById("edit_sev_id").value = this.dataset.sev;
    document.getElementById("edit_desc").value = this.dataset.desc;
    document.getElementById("editModal").classList.remove("hidden");
  });
});

function closeModal() {
  document.getElementById("editModal").classList.add("hidden");
}
