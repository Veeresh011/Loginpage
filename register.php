<?php
include 'db.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    
    if(empty($username) || empty($password)) {
        $error = "⚠️ Please fill all fields!";
    }
    
    elseif($password != $confirm) {
        $error = "❌ Passwords do not match!";
    }
    else {
        // ✅ Fix 1: Prepared statement (no SQL injection)
        $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0) {
            $error = "❌ Username already taken!";
        } else {
            
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);

            if($stmt->execute()) {
                $success = "✅ Account created! You can login now.";
            } else {
                $error = "❌ Something went wrong!";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cyber Tech</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #0a0a0a, #1a1a2e);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(0,255,0,0.3);
            border-radius: 15px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 0 30px rgba(0,255,0,0.1);
        }
        h2 {
            color: #00ff00;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        .subtitle {
            color: #888;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            color: #00ff00;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(0,255,0,0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 15px;
            outline: none;
        }
        .input-group input:focus {
            border-color: #00ff00;
            box-shadow: 0 0 10px rgba(0,255,0,0.2);
        }
        .btn-register {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #00ff00, #00aa00);
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-register:hover {
            box-shadow: 0 0 20px rgba(0,255,0,0.4);
        }
        .logo {
            text-align: center;
            font-size: 40px;
            margin-bottom: 20px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
        .login-link a {
            color: #00ff00;
            text-decoration: none;
        }
        .error {
            background: rgba(255,0,0,0.1);
            border: 1px solid red;
            color: red;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
        .success {
            background: rgba(0,255,0,0.1);
            border: 1px solid #00ff00;
            color: #00ff00;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="logo">📝</div>
        <h2>CYBER TECH</h2>
        <p class="subtitle">Create New Account</p>

        <?php if($error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if($success != "") { ?>
            <div class="success"><?php echo $success; ?></div>
        <?php } ?>

        <form action="register.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username"
                placeholder="Enter username" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password"
                placeholder="Enter password" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm"
                placeholder="Confirm password" required>
            </div>

            <button type="submit" 
            class="btn-register">REGISTER</button>
        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>