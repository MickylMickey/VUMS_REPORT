async function startAiTraining() {
  const btn = document.getElementById("train-btn");
  const icon = document.getElementById("train-icon");
  const text = document.getElementById("train-text");

  // 1. Set Loading State
  btn.disabled = true;
  btn.classList.replace("bg-blue-600", "bg-slate-400");
  icon.classList.add("fa-spin");
  text.innerText = "Learning Patterns...";

  try {
    // 2. Call your PHP script
    const response = await fetch("../functions/auto_train.php");
    const result = await response.text();

    // 3. Success State
    icon.classList.remove("fa-spin");
    icon.classList.replace("fa-wand-magic-sparkles", "fa-check");
    btn.classList.replace("bg-slate-400", "bg-green-500");
    text.innerText = "Training Complete!";

    console.log("AI Response:", result);
    alert(result); // Shows how many patterns were learned
  } catch (error) {
    console.error("Training failed:", error);
    alert("Training failed. Check console for details.");

    // Reset button on error
    btn.disabled = false;
    btn.classList.replace("bg-slate-400", "bg-blue-600");
    icon.classList.remove("fa-spin");
    text.innerText = "Try Again";
  } finally {
    // Optional: Reset button back to original state after 3 seconds
    setTimeout(() => {
      btn.disabled = false;
      btn.classList.remove("bg-green-500");
      btn.classList.add("bg-blue-600");
      icon.classList.replace("fa-check", "fa-wand-magic-sparkles");
      text.innerText = "Train AI Now";
    }, 3000);
  }
}
