<?php
require_once "config.php";

$erreur = "";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";

    if ($email === "" || $mot_de_passe === "") {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
            $_SESSION["user_id"] = $utilisateur["id"];
            $_SESSION["user_nom"] = $utilisateur["nom"];
            $_SESSION["user_email"] = $utilisateur["email"];
            $_SESSION["user_role"] = $utilisateur["role"];

            header("Location: index.php");
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Akio Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #0f766e);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.25);
        }

        .logo-box {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-box img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 18px;
            margin-bottom: 12px;
        }

        .logo-box h1 {
            color: #0f172a;
            font-size: 26px;
        }

        .logo-box p {
            color: #64748b;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #1f2937;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #0f766e;
        }

        .btn-login {
            width: 100%;
            border: none;
            background: #0f766e;
            color: white;
            padding: 14px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #115e59;
        }

        .alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 18px;
            border: 1px solid #fecaca;
        }

        .hint {
            text-align: center;
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="logo-box">
            <img src="images/logo.jpeg" alt="Akio Service">
            <h1>Akio Service</h1>
            <p>Connexion administrateur</p>
        </div>

        <?php if ($erreur): ?>
            <div class="alert">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    placeholder="admin@akioservice.local"
                    required
                >
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input 
                    type="password" 
                    name="mot_de_passe" 
                    id="mot_de_passe" 
                    placeholder="Votre mot de passe"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                Se connecter
            </button>
        </form>

        <p class="hint">
            Accès réservé à l’administrateur.
        </p>

    </div>

</body>
</html>