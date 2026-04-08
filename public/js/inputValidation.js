function initFormValidation(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  // Async availability check for inputs with data-check-url
  form.querySelectorAll("[data-check-url]").forEach((input) => {
    input.addEventListener("blur", async () => {
      const errorEl = input.closest("div").querySelector(".error-message");
      const value = input.value.trim();
      const url = input.dataset.checkUrl;

      // Messages
      const duplicateMessage = input.dataset.checkMessage || "Already exists.";
      const notFoundMessage = input.dataset.checkExistsMessage || "";

      if (value === "") {
        if (errorEl) {
          errorEl.textContent = "";
          errorEl.classList.add("hidden");
        }
        input.classList.remove("border-red-500");
        delete input.dataset.invalid;
        return;
      }

      try {
        const response = await fetch(url + encodeURIComponent(value));
        const data = await response.json();

        if (data.exists) {
          // Duplicate
          if (errorEl) {
            errorEl.textContent = duplicateMessage;
            errorEl.classList.remove("hidden");
          }
          input.classList.add("border-red-500");
          input.dataset.invalid = "true";
        } else if (!data.exists && notFoundMessage) {
          // Not found
          if (errorEl) {
            errorEl.textContent = notFoundMessage;
            errorEl.classList.remove("hidden");
          }
          input.classList.add("border-red-500");
          input.dataset.invalid = "true";
        } else {
          // Valid
          if (errorEl) {
            errorEl.textContent = "";
            errorEl.classList.add("hidden");
          }
          input.classList.remove("border-red-500");
          delete input.dataset.invalid;
        }
      } catch (error) {
        console.error("Error checking availability:", error);
      }
    });
  });

  const ageInputs = form.querySelectorAll("[data-age-validation]");

  ageInputs.forEach((input) => {
    input.addEventListener("input", () => validateAge(input));
    input.addEventListener("change", () => validateAge(input));
  });

  function validateAge(input) {
    const realAge = parseFloat(input.dataset.ageReal); // Age from PHP
    const recordedAge = parseFloat(input.value);
    const errorEl = input.closest("div").querySelector(".error-message");
    const saveBtn = form.querySelector("[type='submit']");

    // Reset if empty
    if (isNaN(recordedAge) || recordedAge === "") {
      clearAgeError();
      return;
    }

    // ❌ Recorded Age cannot be GREATER than real age
    if (recordedAge > realAge) {
      showAgeError(`Recorded age cannot be greater than actual age (${realAge} months).`);
      return;
    }

    // 👍 Valid
    clearAgeError();

    function showAgeError(msg) {
      if (errorEl) {
        errorEl.textContent = msg;
        errorEl.classList.remove("hidden");
      }
      input.classList.add("border-red-500");
      input.dataset.invalid = "true";
      if (saveBtn) saveBtn.disabled = true;
    }

    function clearAgeError() {
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.add("hidden");
      }
      input.classList.remove("border-red-500");
      delete input.dataset.invalid;
      if (saveBtn) saveBtn.disabled = false;
    }
  }

  // Clear errors while typing or selecting
  form.querySelectorAll("[data-required='true']").forEach((input) => {
    input.addEventListener("input", () => clearError(input));
    input.addEventListener("change", () => clearError(input));
  });

  function clearError(input) {
    const errorEl = input.closest("div").querySelector(".error-message");
    if (input.type === "checkbox" || input.type === "radio") {
      if (input.checked) {
        if (errorEl) {
          errorEl.textContent = "";
          errorEl.classList.add("hidden");
        }
        input.classList.remove("border-red-500");
        delete input.dataset.invalid;
      }
    } else if (input.value.trim() !== "" && input.value !== null) {
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.add("hidden");
      }
      input.classList.remove("border-red-500");
      delete input.dataset.invalid;
    }
  }

  // Final validation on submit
  form.addEventListener("submit", function (e) {
    let valid = true;

    form.querySelectorAll("[data-required='true']").forEach((input) => {
      const errorEl = input.closest("div").querySelector(".error-message");
      let errorMsg = input.dataset.error || "This field is required.";

      let isEmpty = false;
      if (input.type === "checkbox" || input.type === "radio") {
        isEmpty = !input.checked;
      } else if (input.tagName.toLowerCase() === "select") {
        isEmpty = input.value === "" || input.value === null;
      } else {
        isEmpty = input.value.trim() === "";
      }

      if (isEmpty) {
        valid = false;
        if (errorEl) {
          errorEl.textContent = errorMsg;
          errorEl.classList.remove("hidden");
        }
        input.classList.add("border-red-500");
        return; // skip other checks
      }

      // ✅ Custom rule checks
      if (input.dataset.minlength && input.value.length < parseInt(input.dataset.minlength)) {
        valid = false;
        if (errorEl) {
          errorEl.textContent = input.dataset.errorMinlength || `Must be at least ${input.dataset.minlength} characters.`;
          errorEl.classList.remove("hidden");
        }
        input.classList.add("border-red-500");
        return;
      }

      if (input.dataset.maxlength && input.value.length > parseInt(input.dataset.maxlength)) {
        valid = false;
        if (errorEl) {
          errorEl.textContent = input.dataset.errorMaxlength || `Must be no more than ${input.dataset.maxlength} characters.`;
          errorEl.classList.remove("hidden");
        }
        input.classList.add("border-red-500");
        return;
      }

      if (input.dataset.pattern) {
        const regex = new RegExp(input.dataset.pattern);
        if (!regex.test(input.value)) {
          valid = false;
          if (errorEl) {
            errorEl.textContent = input.dataset.errorPattern || "Invalid format.";
            errorEl.classList.remove("hidden");
          }
          input.classList.add("border-red-500");
          return;
        }
      }

      if (input.dataset.match) {
        const other = form.querySelector(`[name='${input.dataset.match}']`);
        if (other && input.value !== other.value) {
          valid = false;
          if (errorEl) {
            errorEl.textContent = input.dataset.errorMatch || "Values do not match.";
            errorEl.classList.remove("hidden");
          }
          input.classList.add("border-red-500");
          return;
        }
      }

      // If passes all checks → clear error
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.add("hidden");
      }
      input.classList.remove("border-red-500");
    });

    if (!valid) e.preventDefault(); // Stop form from submitting
  });
}
