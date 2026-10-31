document.addEventListener("DOMContentLoaded", function () {
  initThemeToggle();
  initNotificationBanner();
  initSlider();
  initFaq();
  initModal();
  initHamburgerMenu();
});

// Restore the saved theme, then change it when the button is clicked.
function initThemeToggle() {
  const themeButtons = document.querySelectorAll("[data-theme-toggle]");
  const savedTheme = localStorage.getItem("studenthub-theme");

  function updateThemeControls(isDark) {
    themeButtons.forEach(function (button) {
      button.textContent = isDark ? "Light Mode" : "Dark Mode";
      button.setAttribute("aria-pressed", String(isDark));
    });
  }

  const isDarkInitially = savedTheme === "dark";
  document.body.classList.toggle("dark-theme", isDarkInitially);
  updateThemeControls(isDarkInitially);

  themeButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const isDark = document.body.classList.toggle("dark-theme");
      localStorage.setItem("studenthub-theme", isDark ? "dark" : "light");
      updateThemeControls(isDark);
    });
  });
}

function initNotificationBanner() {
  const banner = document.getElementById("studenthub-banner");
  if (!banner) {
    return;
  }

  const closeButton = banner.querySelector("[data-close-banner]");
  if (!closeButton) {
    return;
  }

  closeButton.addEventListener("click", function () {
    banner.classList.add("is-hidden");
  });
}

// The remainder operator (%) makes Previous and Next wrap around.
function initSlider() {
  const slider = document.querySelector("[data-slider]");
  if (!slider) {
    return;
  }

  const slides = slider.querySelectorAll(".slide");
  const prevButton = document.querySelector("[data-slide-prev]");
  const nextButton = document.querySelector("[data-slide-next]");

  if (!slides.length || !prevButton || !nextButton) {
    return;
  }

  let currentIndex = 0;

  function showSlide(index) {
    slides.forEach(function (slide, slideIndex) {
      slide.classList.toggle("active", slideIndex === index);
      slide.setAttribute("aria-hidden", String(slideIndex !== index));
    });
  }

  prevButton.addEventListener("click", function () {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    showSlide(currentIndex);
  });

  nextButton.addEventListener("click", function () {
    currentIndex = (currentIndex + 1) % slides.length;
    showSlide(currentIndex);
  });
}

// Close all answers before opening the selected answer.
function initFaq() {
  const faqItems = document.querySelectorAll(".faq-item");
  if (!faqItems.length) {
    return;
  }

  faqItems.forEach(function (item) {
    const button = item.querySelector(".faq-question");

    button.addEventListener("click", function () {
      const isOpen = item.classList.contains("open");

      faqItems.forEach(function (faqItem) {
        faqItem.classList.remove("open");
        const question = faqItem.querySelector(".faq-question");
        question.setAttribute("aria-expanded", "false");
      });

      if (!isOpen) {
        item.classList.add("open");
        button.setAttribute("aria-expanded", "true");
      }
    });
  });
}

function initModal() {
  const modal = document.getElementById("eventModal");
  if (!modal) {
    return;
  }

  const modalContent = document.getElementById("eventModalContent");
  const modalTitle = document.getElementById("eventModalTitle");
  const closeButton = document.querySelector("[data-close-modal]");
  const triggers = document.querySelectorAll(".modal-trigger");
  let lastTrigger;

  function closeModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
    if (lastTrigger) {
      lastTrigger.focus();
    }
  }

  triggers.forEach(function (button) {
    button.addEventListener("click", function () {
      const title = button.getAttribute("data-event-title");
      const details = button.getAttribute("data-event-description");

      modalTitle.textContent = title;
      modalContent.textContent = details;
      lastTrigger = button;
      modal.classList.add("show");
      modal.setAttribute("aria-hidden", "false");
      document.body.classList.add("modal-open");
      if (closeButton) {
        closeButton.focus();
      }
    });
  });

  if (closeButton) {
    closeButton.addEventListener("click", closeModal);
  }

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && modal.classList.contains("show")) {
      closeModal();
    }
    // The close button is the only interactive element in this modal.
    if (event.key === "Tab" && modal.classList.contains("show") && closeButton) {
      event.preventDefault();
      closeButton.focus();
    }
  });
}

function initHamburgerMenu() {
  const nav = document.querySelector(".main-nav, .nav");
  const menuButton = document.querySelector(".menu-toggle");

  if (!nav || !menuButton) {
    return;
  }

  menuButton.addEventListener("click", function () {
    nav.classList.toggle("nav-open");
    const expanded = nav.classList.contains("nav-open");
    menuButton.setAttribute("aria-expanded", String(expanded));
  });
}
