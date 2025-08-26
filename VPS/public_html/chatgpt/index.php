<!DOCTYPE html>
<html>
  <head>
    <title>Minha Página de Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSS Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="css/style.css">
  </head>
  
  <body>
    <div class="container">
      <div class="row justify-content-center mt-5">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">Faça login ou cadastre-se</h4>
            </div>
            <div class="card-body">
              <form id="login-form">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" required>
                </div>
                <div class="form-group">
                  <label for="password">Senha</label>
                  <input type="password" class="form-control" id="password" required>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="remember-me">
                  <label class="form-check-label" for="remember-me">Lembrar de mim</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Entrar</button>
              </form>
              <hr>
              <button id="signup-btn" class="btn btn-secondary btn-block">Criar uma conta</button>
              <button id="forgot-password-btn" class="btn btn-link btn-block">Esqueceu sua senha?</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- JavaScript Firebase -->
    <script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-firestore.js"></script>
    
    <!-- Configuração do Firebase -->
    <script>
      const firebaseConfig = {
        apiKey: "SUA_API_KEY_AQUI",
        authDomain: "SEU_DOMINIO_AQUI.firebaseapp.com",
        projectId: "SEU_ID_DO_PROJETO_AQUI",
        storageBucket: "SEU_BUCKET_AQUI.appspot.com",
        messagingSenderId: "SEU_SENDER_ID_AQUI",
        appId: "SEU_APP_ID_AQUI"
      };
      
      firebase.initializeApp(firebaseConfig);
      
      const auth = firebase.auth();
      const db = firebase.firestore();
    </script>
    
    <!-- JavaScript Personalizado -->
    <script src="js/script.js"></script>
  </body>
</html>
