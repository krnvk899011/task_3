<?php
// edit.php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Получаем актуальные данные пользователя
try {
    // Данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Языки пользователя
    $stmt = $pdo->prepare("
        SELECT pl.name 
        FROM user_languages ul
        JOIN programming_languages pl ON ul.language_id = pl.id
        WHERE ul.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $userLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Edit error: " . $e->getMessage());
    header('Location: logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
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

        .container {
            max-width: 900px;
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
            animation: glow 8s infinite;
        }

        h1 {
            font-size: 3rem;
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

        /* Информация о пользователе */
        .user-info {
            background: linear-gradient(145deg, rgba(56, 189, 248, 0.1), rgba(192, 132, 252, 0.1));
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 30px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            overflow: hidden;
        }

        .user-info::before {
            content: '👤';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 48px;
            opacity: 0.1;
        }

        .user-info strong {
            color: #38bdf8;
            font-weight: 600;
        }

        .user-info span {
            color: #e2e8f0;
            font-size: 1.1rem;
            font-weight: 500;
            background: rgba(15, 23, 42, 0.5);
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        /* Сетка формы */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #e2e8f0;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        input, select, textarea {
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

        input:hover, select:hover, textarea:hover {
            border-color: #38bdf8;
            background: rgba(15, 23, 42, 0.8);
        }

        input:focus, select:focus, textarea:focus {
            border-color: #c084fc;
            box-shadow: 0 0 0 3px rgba(192, 132, 252, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        input::placeholder, textarea::placeholder {
            color: #475569;
        }

        /* Radio buttons */
        .radio-group {
            display: flex;
            gap: 30px;
            padding: 10px 0;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #e2e8f0;
            cursor: pointer;
            padding: 8px 20px;
            background: rgba(15, 23, 42, 0.4);
            border-radius: 50px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: all 0.3s ease;
        }

        .radio-option:hover {
            border-color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #c084fc;
            margin: 0;
            padding: 0;
        }

        /* Multiple select */
        select[multiple] {
            height: 200px;
            padding: 10px;
        }

        select[multiple] option {
            padding: 12px 15px;
            margin: 3px 0;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.8);
            color: #e2e8f0;
            transition: all 0.2s ease;
        }

        select[multiple] option:hover {
            background: rgba(56, 189, 248, 0.2);
        }

        select[multiple] option:checked {
            background: linear-gradient(135deg, #38bdf8, #c084fc);
            color: #fff;
        }

        /* Textarea */
        textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Подсказка */
        .hint {
            display: block;
            margin-top: 8px;
            color: #64748b;
            font-size: 0.85rem;
            padding-left: 15px;
        }

        /* Кнопки */
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
            margin-top: 30px;
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

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            margin-top: 15px;
        }

        .btn-danger:hover {
            box-shadow: 0 20px 30px -10px rgba(239, 68, 68, 0.5);
        }

        /* Сообщения */
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 30px;
            padding: 20px 25px;
            margin-bottom: 30px;
            color: #86efac;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .success-message::before {
            content: '✓';
            background: rgba(34, 197, 94, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #4ade80;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 30px;
            padding: 20px 25px;
            margin-bottom: 30px;
            color: #f87171;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .error-message::before {
            content: '⚠️';
            font-size: 1.5rem;
        }

        /* Навигация */
        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.2);
            font-weight: 500;
        }

        .nav-link:hover {
            color: #38bdf8;
            border-color: #38bdf8;
            transform: translateY(-2px);
        }

        .nav-link:first-child::before {
            content: '←';
            font-size: 1.2rem;
        }

        .nav-link:last-child {
            color: #f87171;
        }

        .nav-link:last-child:hover {
            color: #ef4444;
            border-color: #ef4444;
        }

        .nav-link:last-child::after {
            content: '→';
            font-size: 1.2rem;
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

        /* Декоративные элементы */
        .container::before {
            content: '✎';
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 48px;
            opacity: 0.1;
            transform: rotate(15deg);
            pointer-events: none;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .radio-group {
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                flex-direction: column;
                gap: 15px;
            }

            .nav-link {
                justify-content: center;
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
    <div class="container">
        <h1>Редактирование данных</h1>
        
        <div class="user-info">
            <strong>Вы вошли как:</strong>
            <span><?= htmlspecialchars($_SESSION['login']) ?></span>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                Данные успешно обновлены!
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="update.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($user['full_name']) ?>"
                           placeholder="Иванов Иван Иванович"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($user['phone']) ?>"
                           placeholder="+7 (999) 123-45-67"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>"
                           placeholder="example@mail.com"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?= htmlspecialchars($user['birth_date']) ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="male"
                                   <?= $user['gender'] === 'male' ? 'checked' : '' ?>
                                   required> Мужской
                        </label>
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="female"
                                   <?= $user['gender'] === 'female' ? 'checked' : '' ?>
                                   required> Женский
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="languages">Любимый язык программирования *</label>
                    <select name="languages[]" 
                            id="languages" 
                            multiple 
                            size="6"
                            required>
                        <?php
                        $allLanguages = [
                            'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
                            'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
                        ];
                        foreach ($allLanguages as $lang):
                        ?>
                            <option value="<?= $lang ?>" 
                                <?= in_array($lang, $userLanguages) ? 'selected' : '' ?>>
                                <?= $lang ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="hint">
                        Удерживайте Ctrl (Cmd) для выбора нескольких
                    </span>
                </div>
                
                <div class="form-group full-width">
                    <label for="biography">Биография</label>
                    <textarea id="biography" 
                              name="biography" 
                              placeholder="Расскажите о себе..."><?= htmlspecialchars($user['biography'] ?? '') ?></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить изменения</button>
        </form>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">На главную</a>
            <a href="logout.php" class="nav-link">Выйти</a>
        </div>
    </div>
</body>
</html>
