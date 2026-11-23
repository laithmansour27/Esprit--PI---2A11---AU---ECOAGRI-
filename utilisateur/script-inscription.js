const STORAGE_KEY = "inscription-users";

const form = document.getElementById("registerForm");
const password = document.getElementById("password");
const password2 = document.getElementById("password2");
const strengthFill = document.getElementById("strengthFill");
const strengthText = document.getElementById("strengthText");
const matchText = document.getElementById("matchText");
const usersList = document.getElementById("usersList");
const emptyUsers = document.getElementById("emptyUsers");
const userCount = document.getElementById("userCount");

function getUsers() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
  } catch {
    return [];
  }
}

function saveUsers(users) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(users));
}

function showToast(message, isError = false) {
  const existing = document.querySelector(".toast");
  if (existing) existing.remove();

  const toast = document.createElement("div");
  toast.className = `toast${isError ? " error" : ""}`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

function passwordStrength(value) {
  let score = 0;
  if (value.length >= 6) score++;
  if (value.length >= 10) score++;
  if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
  if (/\d/.test(value)) score++;
  if (/[^A-Za-z0-9]/.test(value)) score++;
  return score;
}

function updatePasswordStrength() {
  const score = passwordStrength(password.value);
  const labels = ["Très faible", "Faible", "Moyenne", "Bonne", "Forte", "Très forte"];
  const colors = ["#b02a37", "#b02a37", "#d97706", "#d97706", "#2e6b2e", "#1e4a1e"];

  strengthFill.style.width = password.value ? `${Math.max(20, score * 20)}%` : "0%";
  strengthFill.style.background = colors[score];
  strengthText.textContent = password.value ? labels[score] : "Force du mot de passe";
  strengthText.className = `hint ${score >= 3 && password.value ? "success" : "error"}`;
  updatePasswordMatch();
}

function updatePasswordMatch() {
  if (!password2.value) {
    matchText.textContent = "Les mots de passe doivent correspondre";
    matchText.className = "hint";
  } else if (password.value === password2.value) {
    matchText.textContent = "Les mots de passe correspondent";
    matchText.className = "hint success";
  } else {
    matchText.textContent = "Les mots de passe ne correspondent pas";
    matchText.className = "hint error";
  }
}

function renderUsers() {
  const users = getUsers();
  userCount.textContent = users.length;
  usersList.innerHTML = "";
  emptyUsers.style.display = users.length ? "none" : "block";

  users.forEach((user, index) => {
    const item = document.createElement("div");
    item.className = "user-item";
    const safeName = `${user.prenom} ${user.nom}`.replace(/[<>&]/g, "");
    const safeEmail = String(user.email).replace(/[<>&]/g, "");
    item.innerHTML = `
      <div class="user-avatar">${user.prenom.charAt(0)}${user.nom.charAt(0)}</div>
      <div class="user-info">
        <div class="user-name">${safeName}</div>
        <div class="user-meta">${safeEmail}</div>
        <span class="user-role">${user.role}</span>
      </div>
      <button type="button" class="btn-remove-user" data-index="${index}" title="Supprimer">
        <i class="fas fa-trash"></i>
      </button>
    `;
    usersList.appendChild(item);
  });
}

document.querySelectorAll(".toggle-pass").forEach((button) => {
  button.addEventListener("click", () => {
    const input = document.getElementById(button.dataset.target);
    const icon = button.querySelector("i");
    input.type = input.type === "password" ? "text" : "password";
    icon.className = input.type === "password" ? "fas fa-eye" : "fas fa-eye-slash";
  });
});

password.addEventListener("input", updatePasswordStrength);
password2.addEventListener("input", updatePasswordMatch);

usersList.addEventListener("click", (event) => {
  const button = event.target.closest(".btn-remove-user");
  if (!button) return;
  const users = getUsers();
  users.splice(Number(button.dataset.index), 1);
  saveUsers(users);
  renderUsers();
  showToast("Utilisateur supprimé");
});

form.addEventListener("submit", (event) => {
  event.preventDefault();
  form.querySelectorAll(".invalid").forEach((field) => field.classList.remove("invalid"));

  const requiredFields = [...form.querySelectorAll("[required]")];
  const missing = requiredFields.filter((field) =>
    field.type === "checkbox" ? !field.checked : !field.value.trim()
  );

  missing.forEach((field) => field.classList.add("invalid"));

  if (missing.length) {
    missing[0].focus();
    showToast("Veuillez remplir tous les champs obligatoires.", true);
    return;
  }

  if (password.value.length < 6) {
    password.classList.add("invalid");
    password.focus();
    showToast("Le mot de passe doit contenir au moins 6 caractères.", true);
    return;
  }

  if (password.value !== password2.value) {
    password2.classList.add("invalid");
    password2.focus();
    showToast("Les mots de passe ne correspondent pas.", true);
    return;
  }

  const users = getUsers();
  const email = document.getElementById("email").value.trim().toLowerCase();
  if (users.some((user) => user.email.toLowerCase() === email)) {
    document.getElementById("email").classList.add("invalid");
    showToast("Cet email est déjà inscrit.", true);
    return;
  }

  users.push({
    prenom: document.getElementById("prenom").value.trim(),
    nom: document.getElementById("nom").value.trim(),
    email,
    telephone: document.getElementById("telephone").value.trim(),
    role: document.getElementById("role").value,
    region: document.getElementById("region").value
  });

  saveUsers(users);
  form.reset();
  updatePasswordStrength();
  renderUsers();
  showToast("Compte créé avec succès.");
});

renderUsers();