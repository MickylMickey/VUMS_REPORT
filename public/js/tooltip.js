const tooltip = document.getElementById("tooltip");
let tooltipTimeout; // Store the timer here

document.addEventListener("mouseover", (e) => {
  const target = e.target.closest("[data-tooltip]");
  if (!target) return;

  // 1. Clear any existing timer to prevent overlaps
  clearTimeout(tooltipTimeout);

  // 2. Start the 5-second delay
  tooltipTimeout = setTimeout(() => {
    target._isHovered = true;

    if (!target.dataset.originalTooltip) {
      target.dataset.originalTooltip = target.dataset.tooltip;
    }

    tooltip.textContent = target.dataset.tooltip;

    // Position logic (Optimized for Tailwind 'fixed' class)
    const rect = target.getBoundingClientRect();
    tooltip.style.top = rect.bottom + 8 + "px"; // 8px gap
    tooltip.style.left = rect.left + rect.width / 2 + "px";
    tooltip.style.transform = "translateX(-50%)";
    tooltip.style.opacity = "1";
  }, 3000); // <--- 3000ms = 3 seconds
});

document.addEventListener("mouseout", (e) => {
  const target = e.target.closest("[data-tooltip]");
  if (!target) return;

  // 3. Cancel the timer if the user leaves early
  clearTimeout(tooltipTimeout);

  target._isHovered = false;
  tooltip.style.opacity = "0";
});

// Click logic remains the same...
document.addEventListener("click", (e) => {
  const target = e.target.closest("[data-tooltip][data-tooltip-click]");
  if (!target) return;

  const original = target.dataset.originalTooltip;
  const clickText = target.dataset.tooltipClick;

  target.dataset.tooltip = target.dataset.tooltip === original ? clickText : original;

  if (target._isHovered) {
    tooltip.textContent = target.dataset.tooltip;
  }
});
