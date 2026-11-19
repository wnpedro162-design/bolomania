<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Login - Bolo Mania</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
</head>

<body>

<div class="login-container">

    <div class="login-card">

        <div class="brand">

            <img src="/bolomania/public/imagem/logo.png?v=3" class="logo">

            <div>
                <h1>Bolo Mania</h1>
                <small>Acesso ao sistema</small>
            </div>

        </div>

        <form method="post" action="/bolomania/index.php?controller=auth&action=login">

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <div class="buttons">

                <button class="btn entrar" type="submit">
                    Entrar
                </button>

            </div>

        </form>

        <p style="margin-top:22px; text-align:center; font-size:14px;">
            É cliente? <a href="index.php?controller=loja&action=index" style="color:var(--vinho); font-weight:bold;">Ir para a loja virtual</a>
        </p>

    </div>

</div>

</body>
</html>
