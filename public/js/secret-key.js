// The secret sequence of element IDs
const secretCode = ["card1", "card1", "card1", "card1", "card1"];
let currentStep = 0;

function handleCardClick(cardId) {
  // Check if the clicked card matches the next step in our secret code
  if (cardId === secretCode[currentStep]) {
    currentStep++;

    // If the user completed the whole sequence
    if (currentStep === secretCode.length) {
      window.location.href = "/public/secret-key.php"; // Redirect to the secret page
    }
  } else {
    // Wrong card! Reset the progress
    currentStep = 0;
    console.log("Sequence broken. Start over!");
  }
}
