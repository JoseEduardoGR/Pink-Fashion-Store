// Variables globales
const cart = {
  items: [],
  total: 0,
  count: 0,
}

// Inicialización cuando el DOM está listo
document.addEventListener("DOMContentLoaded", () => {
  initNavigation()
  initFormValidations()
  initProductManagement()
  initCartFunctionality()
  initDashboard()
  initProductFilters()
  initModals()
  updateCartBadge()
  initAnimations()
})

// Navegación móvil mejorada
function initNavigation() {
  const hamburger = document.querySelector(".hamburger")
  const navMenu = document.querySelector(".nav-menu")

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      navMenu.classList.toggle("active")
      hamburger.classList.toggle("active")
    })

    // Cerrar menú al hacer click en un enlace
    document.querySelectorAll(".nav-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        navMenu.classList.remove("active")
        hamburger.classList.remove("active")
      })
    })

    // Cerrar menú al hacer click fuera
    document.addEventListener("click", (e) => {
      if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.remove("active")
        hamburger.classList.remove("active")
      }
    })
  }
}

// Dashboard - cambio de secciones mejorado
function initDashboard() {
  const menuLinks = document.querySelectorAll(".menu-link")
  const sections = document.querySelectorAll(".dashboard-section")

  menuLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      // No procesar si es un enlace externo
      if (this.href && !this.hasAttribute("data-section")) {
        return
      }

      // Remover clase active de todos los enlaces y secciones
      menuLinks.forEach((l) => l.classList.remove("active"))
      sections.forEach((s) => s.classList.remove("active"))

      // Agregar clase active al enlace clickeado
      this.classList.add("active")

      // Mostrar la sección correspondiente
      const sectionId = this.getAttribute("data-section")
      const targetSection = document.getElementById(sectionId)
      if (targetSection) {
        targetSection.classList.add("active")
        // Animación suave
        targetSection.style.opacity = "0"
        targetSection.style.transform = "translateY(20px)"
        setTimeout(() => {
          targetSection.style.opacity = "1"
          targetSection.style.transform = "translateY(0)"
        }, 50)
      }
    })
  })
}

// Validaciones mejoradas del lado del cliente
function initFormValidations() {
  // Validación del formulario de login
  const loginForm = document.getElementById("loginForm")
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      if (!validateLoginForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación del formulario de registro
  const registerForm = document.getElementById("registerForm")
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      if (!validateRegisterForm()) {
        e.preventDefault()
      }
    })
  }

  // Validación en tiempo real
  initRealTimeValidation()
}

function validateLoginForm() {
  let isValid = true
  const username = document.getElementById("username")
  const password = document.getElementById("password")

  clearErrors()

  if (!username.value.trim()) {
    showError("usernameError", "El usuario es obligatorio")
    isValid = false
  }

  if (!password.value.trim()) {
    showError("passwordError", "La contraseña es obligatoria")
    isValid = false
  }

  return isValid
}

function validateRegisterForm() {
  let isValid = true;
  const fullName = document.getElementById("full_name");
  const username = document.getElementById("username");
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm_password");
  const phone = document.getElementById("phone");

  clearErrors();

  // Validar nombre completo
  if (!fullName.value.trim()) {
    showError("fullNameError", "El nombre completo es obligatorio");
    isValid = false;
  } else if (fullName.value.trim().length < 2) {
    showError("fullNameError", "El nombre debe tener al menos 2 caracteres");
    isValid = false;
  }

  // Validar usuario
  if (!username.value.trim()) {
    showError("usernameError", "El usuario es obligatorio");
    isValid = false;
  } else if (username.value.trim().length < 3) {
    showError("usernameError", "El usuario debe tener al menos 3 caracteres");
    isValid = false;
  } else if (!/^[a-zA-Z0-9_]+$/.test(username.value.trim())) {
    showError("usernameError", "El usuario solo puede contener letras, números y guiones bajos");
    isValid = false;
  }

  // Validar email
  if (!email.value.trim()) {
    showError("emailError", "El email es obligatorio");
    isValid = false;
  } else if (!isValidEmail(email.value.trim())) {
    showError("emailError", "El email no es válido");
    isValid = false;
  }

  // Validar teléfono (opcional pero si se proporciona debe ser válido)
  if (phone.value.trim() && !isValidPhone(phone.value.trim())) {
    showError("phoneError", "El teléfono no es válido");
    isValid = false;
  }

  // Validar contraseña
  if (!password.value) {
    showError("passwordError", "La contraseña es obligatoria");
    isValid = false;
  } else if (password.value.length < 6) {
    showError("passwordError", "La contraseña debe tener al menos 6 caracteres");
    isValid = false;
  } else if (!isStrongPassword(password.value)) {
    showError("passwordError", "La contraseña debe contener al menos una letra y un número");
    isValid = false;
  }

  // Validar confirmación de contraseña
  if (!confirmPassword.value) {
    showError("confirmPasswordError", "Confirma tu contraseña");
