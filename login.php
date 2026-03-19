<?php
// login.php
session_start();

// Если уже есть активная сессия, перенаправляем на редактирование
if (isset($_SESSION['user_id']) && isset($_SESSION['authenticated'])) {
    header('Location: edit.php');
    exit;
}

$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(125deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            padding: 30px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Анимированный фон */
        body::before {
            content: '';
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: radial-gradient(circle at 30% 50%, rgba(56, 189, 248, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(192, 132, 252, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            padding: 50px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 2px rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
            animation: glow 8s infinite;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #38bdf8, #c084fc, transparent);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #e2e8f0;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            font-size: 1rem;
            color: #fff;
            transition: all 0.3s ease;
            outline: none;
        }

        input[type="text"]:hover,
        input[type="password"]:hover {
            border-color: #38bdf8;
            background: rgba(15, 23, 42, 0.8);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #c084fc;
            box-shadow: 0 0 0 3px rgba(192, 132, 252, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #475569;
        }

        .btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #38bdf8, #c084fc);
            border: none;
            border-radius: 30px;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px -10px rgba(56, 189, 248, 0.5);
        }

        .btn:hover::before {
            left: 100%;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 20px;
            padding: 15px 20px;
            margin-bottom: 25px;
            color: #f87171;
            text-align: center;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .error-message::before {
            content: '⚠️';
            font-size: 1.2rem;
        }

        .info {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 20px;
            padding: 15px 20px;
            margin-top: 25px;
            color: #7dd3fc;
            text-align: center;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .info::before {
            content: 'ℹ️';
            font-size: 1.2rem;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            padding: 10px;
        }

        .back-link:hover {
            color: #38bdf8;
            gap: 15px;
        }

        .back-link::before {
            content: '←';
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .back-link:hover::before {
            transform: translateX(-5px);
        }

        /* Декоративные элементы */
        .login-container::before {
            content: '🔐';
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 48px;
            opacity: 0.1;
            transform: rotate(15deg);
            pointer-events: none;
        }

        /* Анимация */
        @keyframes glow {
            0%, 100% {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.3);
            }
            50% {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(192, 132, 252, 0.5);
            }
        }

        /* Адаптивность */
        @media (max-width: 500px) {
            .login-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.8rem;
                white-space: normal;
            }

            .btn {
                padding: 15px;
                font-size: 1rem;
            }
        }

        /* Стили для автозаполнения */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #fff;
            -webkit-box-shadow: 0 0 0px 1000px rgba(15, 23, 42, 0.9) inset;
            transition: background-color 5000s ease-in-out 0s;
            border-color: #38bdf8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Вход для редактирования</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="auth.php" method="POST">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" 
                       id="login" 
                       name="login"
                       placeholder="Введите ваш логин"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" 
                       id="password" 
                       name="password"
                       placeholder="Введите ваш пароль"
                       required>
            </div>
            
            <button type="submit" class="btn">Войти</button>
        </form>
        
        <div class="info">
            Используйте логин и пароль, полученные при регистрации
        </div>
        
        <a href="index.php" class="back-link">Вернуться к регистрации</a>
    </div>
</body>
</html>
