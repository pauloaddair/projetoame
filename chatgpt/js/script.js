// JavaScript Document
// Seleciona elementos do DOM
const loginForm = document.getElementById("login-form");
const signupBtn = document.getElementById("signup-btn");
const forgotPasswordBtn = document.getElementById("forgot-password-btn");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const rememberMeCheckbox = document.getElementById("remember-me");

// Adiciona evento de login ao formulário
loginForm.addEventListener("submit", e => {
  e.preventDefault();
  
  const email = emailInput.value;
  const password = passwordInput.value;
  const rememberMe = rememberMeCheckbox.checked;
  
  auth.signInWithEmailAndPassword(email, password)
    .then(() => {
      if (rememberMe) {
        localStorage.setItem("email", email);
        localStorage.setItem("password", password);
      } else {
        localStorage.removeItem("email");
        localStorage.removeItem("password");
      }
    })
    .catch(error => {
      alert(error.message);
    });
});

// Adiciona evento de cadastro ao botão
signupBtn.addEventListener("click", () => {
  const email = emailInput.value;
  const password = passwordInput.value;
  
  auth.createUserWithEmailAndPassword(email, password)
    .then(userCredential => {
      const user = userCredential.user;
      db.collection("users").doc(user.uid).set({
        email: user.email
      });
    })
    .catch(error => {
      alert(error.message);
    });
});

// Adiciona evento de redefinição de senha ao botão
forgotPasswordBtn.addEventListener("click", () => {
  const email = emailInput.value;
  
  auth.sendPasswordResetEmail(email)
    .then(() => {
      alert("Um email para redefinição de senha foi enviado para sua conta.");
    })
    .catch(error => {
      alert(error.message);
    });
});

// Verifica se o usuário está logado ao carregar a página
auth.onAuthStateChanged(user => {
  if (user) {
    window.location.href = "dashboard.html";
  } else {
    const email = localStorage.getItem("email");
    const password = localStorage.getItem("password");
    
    if (email && password) {
      emailInput.value = email;
      passwordInput.value = password;
      rememberMeCheckbox.checked = true;
    }
  }
});
