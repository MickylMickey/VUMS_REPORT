const hideToast = (element) => {
  setTimeout(() => {
    element.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    element.style.opacity = "0";
    element.style.transform = "translateY(-10px)";
    setTimeout(() => element.remove(), 500);
  }, 3000);
};

document.addEventListener("DOMContentLoaded", function () {
  const validationBlock = document.getElementById("validationBlock");

  if (validationBlock) {
    hideToast(validationBlock);
  } else {
    // If it's not there yet, watch the body for it to be added
    const observer = new MutationObserver((mutations) => {
      const addedNode = document.getElementById("validationBlock");
      if (addedNode) {
        hideToast(addedNode);
        observer.disconnect(); // Stop watching once found
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
});
