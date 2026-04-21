// 1. Grab elements once
const descriptionArea = document.getElementById("suggestion_desc");
const fileInput = document.getElementById("suggestion_img_input");
const previewContainer = document.getElementById("paste-preview-container");
const previewImage = document.getElementById("paste-preview");
const clearPreviewBtn = document.getElementById("clear-preview-btn");

// 2. Global Clear Function (So the button can see it)
function clearPastedImage() {
  fileInput.value = "";
  previewImage.src = "";
  previewImage.classList.add("hidden"); // Sync with HTML hidden state
  previewContainer.classList.add("hidden");
  clearPreviewBtn.classList.add("hidden");
}

// 3. Paste Event Listener
descriptionArea.addEventListener("paste", function (e) {
  const items = (e.clipboardData || e.originalEvent.clipboardData).items;

  for (let item of items) {
    if (item.type.indexOf("image") !== -1) {
      const blob = item.getAsFile();

      // Sync with File Input
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(blob);
      fileInput.files = dataTransfer.files;

      // Preview Logic
      const reader = new FileReader();
      reader.onload = function (event) {
        previewImage.src = event.target.result;
        previewImage.classList.remove("hidden");
        previewContainer.classList.remove("hidden");
        clearPreviewBtn.classList.remove("hidden");
      };
      reader.readAsDataURL(blob);
    }
  }
});
