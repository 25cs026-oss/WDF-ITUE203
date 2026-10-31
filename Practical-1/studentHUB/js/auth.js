// This login system is only for a front-end practical.

document.addEventListener("DOMContentLoaded", function () {
  checkPageAccess();
  showPageMessage();
  setupLogin();
  setupRegistration();
  setupLogout();
  showStudentDetails();
});

function getStudent() {
  var savedStudent = localStorage.getItem("studentUser");

  if (savedStudent === null) {
    return null;
  }

  return JSON.parse(savedStudent);
}

function showMessage(elementId, text, messageType) {
  var messageBox = document.getElementById(elementId);

  if (messageBox === null) {
    return;
  }

  messageBox.textContent = text;
  messageBox.className = "form-message " + messageType + "-message";
}

function checkPageAccess() {
  var loggedIn = sessionStorage.getItem("studentLoggedIn");
  var protectedPage = document.body.getAttribute("data-protected");
  var authPage = document.body.getAttribute("data-auth-page");

  if (protectedPage === "true" && loggedIn !== "true") {
    sessionStorage.setItem(
      "accessDenied",
      "You must log in to access the StudentHub dashboard."
    );
    window.location.replace("../login.html");
    return;
  }

  if ((authPage === "login" || authPage === "register") && loggedIn === "true") {
    window.location.replace("student/dashboard.html");
  }
}

function showPageMessage() {
  var accessMessage = sessionStorage.getItem("accessDenied");
  var logoutMessage = sessionStorage.getItem("logoutMessage");

  if (accessMessage !== null) {
    showMessage("loginMessage", accessMessage, "error");
    sessionStorage.removeItem("accessDenied");
  }

  if (logoutMessage !== null) {
    showMessage("homeMessage", logoutMessage, "success");
    sessionStorage.removeItem("logoutMessage");
  }
}

function setupLogin() {
  var loginForm = document.getElementById("loginForm");

  if (loginForm === null) {
    return;
  }

  var rememberedEmail = localStorage.getItem("rememberedEmail");
  if (rememberedEmail !== null) {
    document.getElementById("loginEmail").value = rememberedEmail;
    document.getElementById("rememberEmail").checked = true;
  }

  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    var student = getStudent();
    if (student === null) {
      showMessage(
        "loginMessage",
        "No registered account found. Please register first.",
        "error"
      );
      return;
    }

    var email = document.getElementById("loginEmail").value.trim();
    var password = document.getElementById("loginPassword").value;

    if (email.toLowerCase() !== student.email.toLowerCase() || password !== student.password) {
      showMessage("loginMessage", "Invalid email or password.", "error");
      return;
    }

    var rememberEmail = document.getElementById("rememberEmail").checked;
    if (rememberEmail) {
      localStorage.setItem("rememberedEmail", student.email);
    } else {
      localStorage.removeItem("rememberedEmail");
    }

    sessionStorage.setItem("studentLoggedIn", "true");
    window.location.href = "student/dashboard.html";
  });
}

function setupRegistration() {
  var form = document.getElementById("registrationForm");

  if (form === null) {
    return;
  }

  var nameInput = document.getElementById("student-name");
  var emailInput = document.getElementById("email");
  var mobileInput = document.getElementById("mobile");
  var courseInput = document.getElementById("course");
  var yearInput = document.getElementById("year");
  var passwordInput = document.getElementById("password");
  var confirmPasswordInput = document.getElementById("confirm-password");
  var genderGroup = document.getElementById("gender-group");
  var termsInput = document.getElementById("terms");
  var strengthMeter = document.getElementById("password-strength");
  var strengthLabel = document.getElementById("password-strength-label");
  var strengthBar = strengthMeter.querySelector(".password-strength-bar");
  var submitted = false;

  function getFieldErrorId(fieldName) {
    var errorIds = {
      name: "student-name-error",
      email: "email-error",
      mobile: "mobile-error",
      course: "course-error",
      year: "year-error",
      password: "password-error",
      confirmPassword: "confirm-password-error",
      gender: "gender-error",
      terms: "terms-error"
    };
    return errorIds[fieldName];
  }

  function updateFieldError(fieldName, control, message) {
    var error = document.getElementById(getFieldErrorId(fieldName));
    error.textContent = message;
    control.setAttribute("aria-invalid", String(message !== ""));
    return message === "";
  }

  function validateField(fieldName) {
    var value;
    var message = "";
    var control;

    switch (fieldName) {
      case "name":
        control = nameInput;
        value = nameInput.value.trim();
        if (value.length < 2 || value.length > 60 || !/^[\p{L}\p{M}]+(?:[ .'-][\p{L}\p{M}]+)*$/u.test(value)) {
          message = "Enter a name with 2–60 letters. Spaces, apostrophes, periods, and hyphens are allowed.";
        }
        break;
      case "email":
        control = emailInput;
        value = emailInput.value.trim();
        if (value.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
          message = "Enter a valid email address, such as student@example.com.";
        }
        break;
      case "mobile":
        control = mobileInput;
        value = mobileInput.value.trim();
        if (!/^\+?[0-9][0-9\s()-]*$/.test(value) || value.replace(/\D/g, "").length < 8 || value.replace(/\D/g, "").length > 15) {
          message = "Enter a valid mobile number with 8–15 digits. A leading + and separators are allowed.";
        }
        break;
      case "course":
        control = courseInput;
        if (courseInput.value === "") {
          message = "Select your course.";
        }
        break;
      case "year":
        control = yearInput;
        if (yearInput.value === "") {
          message = "Select your year of study.";
        }
        break;
      case "password":
        control = passwordInput;
        value = passwordInput.value;
        if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9\s])\S{8,64}$/.test(value)) {
          message = "Use 8–64 characters with uppercase and lowercase letters, a number, and a symbol. Spaces are not allowed.";
        }
        break;
      case "confirmPassword":
        control = confirmPasswordInput;
        if (confirmPasswordInput.value === "") {
          message = "Confirm your password.";
        } else if (confirmPasswordInput.value !== passwordInput.value) {
          message = "Passwords do not match.";
        }
        break;
      case "gender":
        control = genderGroup;
        if (!form.querySelector('input[name="gender"]:checked')) {
          message = "Select a gender option, or choose “Prefer not to say”.";
        }
        break;
      case "terms":
        control = termsInput;
        if (!termsInput.checked) {
          message = "You must accept the terms and conditions to register.";
        }
        break;
    }

    return updateFieldError(fieldName, control, message);
  }

  function updatePasswordStrength() {
    var password = passwordInput.value;
    var score = 0;
    var label = "Very weak";
    var strengthClass = "strength-weak";

    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^A-Za-z0-9\s]/.test(password)) score++;

    if (password === "") {
      label = "Enter a password to check its strength.";
      strengthClass = "";
    } else if (score >= 6) {
      label = "Strong password";
      strengthClass = "strength-strong";
    } else if (score >= 4) {
      label = "Good password";
      strengthClass = "strength-good";
    } else if (score >= 3) {
      label = "Fair password";
      strengthClass = "strength-fair";
    }

    strengthMeter.setAttribute("aria-valuenow", String(score));
    strengthMeter.className = "password-strength " + strengthClass;
    strengthBar.style.width = Math.round((score / 6) * 100) + "%";
    strengthLabel.textContent = label;
  }

  var fieldControls = {
    name: nameInput,
    email: emailInput,
    mobile: mobileInput,
    course: courseInput,
    year: yearInput,
    password: passwordInput,
    confirmPassword: confirmPasswordInput,
    terms: termsInput
  };

  Object.keys(fieldControls).forEach(function (fieldName) {
    var control = fieldControls[fieldName];
    control.addEventListener("blur", function () {
      validateField(fieldName);
    });
    control.addEventListener("input", function () {
      if (submitted || control.value !== "") {
        validateField(fieldName);
      }
    });
    control.addEventListener("change", function () {
      if (submitted) {
        validateField(fieldName);
      }
    });
  });

  form.querySelectorAll('input[name="gender"]').forEach(function (radio) {
    radio.addEventListener("change", function () {
      validateField("gender");
    });
  });
  passwordInput.addEventListener("input", function () {
    updatePasswordStrength();
    if (confirmPasswordInput.value !== "") {
      validateField("confirmPassword");
    }
  });
  confirmPasswordInput.addEventListener("input", function () {
    if (submitted || confirmPasswordInput.value !== "") {
      validateField("confirmPassword");
    }
  });
  form.addEventListener("reset", function () {
    window.setTimeout(function () {
      submitted = false;
      form.querySelectorAll("[aria-invalid]").forEach(function (control) {
        control.removeAttribute("aria-invalid");
      });
      form.querySelectorAll(".field-error").forEach(function (error) {
        error.textContent = "";
      });
      updatePasswordStrength();
      showMessage("registrationMessage", "", "");
    }, 0);
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    submitted = true;
    var fields = ["name", "email", "mobile", "course", "year", "password", "confirmPassword", "gender", "terms"];
    var formIsValid = fields.map(validateField).every(Boolean);

    if (!formIsValid) {
      showMessage("registrationMessage", "Please correct the highlighted fields and try again.", "error");
      document.getElementById("registrationMessage").focus();
      return;
    }

    form.submit();
  });
}

function setupLogout() {
  var logoutButton = document.getElementById("logoutButton");

  if (logoutButton === null) {
    return;
  }

  logoutButton.addEventListener("click", function () {
    sessionStorage.removeItem("studentLoggedIn");
    sessionStorage.setItem("logoutMessage", "You have logged out successfully.");
    window.location.href = "../../home.html";
  });
}

function putText(elementId, text) {
  var element = document.getElementById(elementId);

  if (element !== null) {
    element.textContent = text || "Not available";
  }
}

function showStudentDetails() {
  if (document.body.getAttribute("data-protected") !== "true") {
    return;
  }

  var student = getStudent();
  if (student === null) {
    return;
  }

  var fullName = student.firstName + " " + student.lastName;

  putText("loggedInStudentName", student.firstName || "Student");
  putText("dashboardStudentName", fullName);
  putText("dashboardStudentId", student.studentId);
  putText("dashboardProgram", student.program);
  putText("dashboardSemester", student.semester);
  putText("dashboardEmail", student.email);

  putText("profileName", fullName);
  putText("profileStudentId", student.studentId);
  putText("profileEmail", student.email);
  putText("profilePhone", student.phone);
  putText("profileProgram", student.program);
  putText("profileSemester", student.semester);
}
