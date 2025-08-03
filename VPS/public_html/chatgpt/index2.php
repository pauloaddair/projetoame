<!DOCTYPE html>
<html>
  <head>
    <title>Login com Firebase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="container">
      <h1>Login com Firebase</h1>
      <form id="login-form">
        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" id="email" required>
        </div>
        <div class="form-group">
          <label>Senha</label>
          <input type="password" class="form-control" id="password" required>
        </div>
        <div class="form-group form-check">
          <input type="checkbox" class="form-check-input" id="remember-me">
          <label class="form-check-label">Lembrar de mim</label>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
        <button type="button" class="btn btn-link" id="signup-btn">Cadastre-se</button>
        <button type="button" class="btn btn-link" id="forgot-password-btn">Esqueceu sua senha?</button>
      </form>
    </div>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-firestore.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="app.js"></script>
  </body>
</html>
