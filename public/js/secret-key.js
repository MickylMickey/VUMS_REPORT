// Tailwind card styling
document.querySelectorAll(".card").forEach((card) => {
  card.className = "card px-6 py-4 bg-white rounded-xl shadow hover:bg-blue-100 active:scale-95 transition";
});

// Secret sequence
const secretCode = ["card1", "card1", "card1", "card1", "card1"];
let currentStep = 0;
let resetTimer;

function handleCardClick(cardId) {
  clearTimeout(resetTimer);

  if (cardId === secretCode[currentStep]) {
    currentStep++;

    // optional visual feedback
    console.log("Correct:", cardId);

    if (currentStep === secretCode.length) {
      triggerSecret();
    }

    // reset if user pauses too long
    resetTimer = setTimeout(() => {
      currentStep = 0;
    }, 3000);
  } else {
    currentStep = 0;
    console.log("Wrong sequence");
  }
}

function triggerSecret() {
  openModal();

  setTimeout(() => {
    confetti({
      particleCount: 150,
      spread: 120,
      origin: { y: 0.6 },
    });

    fireConfetti();
  }, 500);
}

function openModal() {
  const overlay = document.getElementById("overlay");
  const modal = document.getElementById("modal");

  overlay.classList.remove("hidden");
  overlay.classList.add("flex");

  requestAnimationFrame(() => {
    modal.classList.remove("translate-y-full", "scale-95", "opacity-0");
    modal.classList.add("translate-y-0", "scale-100", "opacity-100");
  });
}

function closeModal() {
  const overlay = document.getElementById("overlay");
  const modal = document.getElementById("modal");

  modal.classList.add("translate-y-full", "scale-95", "opacity-0");
  modal.classList.remove("translate-y-0", "scale-100", "opacity-100");

  setTimeout(() => {
    overlay.classList.add("hidden");
    overlay.classList.remove("flex");
    currentStep = 0;
  }, 300);
}

// Close when clicking outside
document.getElementById("overlay").addEventListener("click", (e) => {
  if (e.target.id === "overlay") {
    closeModal();
  }
});
function fireConfetti() {
  const duration = 2000;
  const end = Date.now() + duration;

  (function frame() {
    confetti({
      particleCount: 5,
      spread: 100,
      origin: { x: Math.random(), y: Math.random() - 0.2 },
    });

    if (Date.now() < end) {
      requestAnimationFrame(frame);
    }
  })();
}
