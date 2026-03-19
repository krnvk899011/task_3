<?php
// index.php
session_start(); // Добавляем старт сессии

$errors = [];
$old = [];

// Показываем сгенерированные логин/пароль, если они есть
$generatedLogin = $_SESSION['generated_login'] ?? null;
$generatedPassword = $_SESSION['generated_password'] ?? null;

// Очищаем после отображения
if ($generatedLogin) {
    unset($_SESSION['generated_login']);
}
if ($generatedPassword) {
    unset($_SESSION['generated_password']);
}

// Читаем Cookies с ошибками (если есть)
if (isset($_COOKIE['form_errors'])) {
    $errors = json_decode($_COOKIE['form_errors'], true);
    setcookie('form_errors', '', time() - 3600, '/');
}

// Читаем сохраненные данные из Cookies (если есть)
if (isset($_COOKIE['form_data'])) {
    $old = json_decode($_COOKIE['form_data'], true);
}

// Если есть временные данные из запроса (приоритет над Cookies)
if (isset($_GET['old'])) {
    $old = array_merge($old, json_decode(urldecode($_GET['old']), true) ?: []);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрационная форма</title>
    <style>
        /* Современный минималистичный дизайн с неоновыми акцентами */
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
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
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

        /* Блок с учетными данными */
        .credentials-box {
            background: linear-gradient(145deg, rgba(56, 189, 248, 0.1), rgba(192, 132, 252, 0.1));
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .credentials-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #38bdf8, #c084fc, transparent);
        }

        .credentials-box h3 {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .credentials-box h3::before {
            content: '✨';
            font-size: 2rem;
            filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.5));
        }

        .credential-item {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 20px;
            padding: 20px 25px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .credential-item:hover {
            border-color: #38bdf8;
            box-shadow: 0 0 30px rgba(56, 189, 248, 0.3);
            transform: translateX(10px);
        }

        .credential-label {
            color: #94a3b8;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            min-width: 70px;
        }

        .credential-item strong {
            color: #fff;
            font-size: 1.3rem;
            font-family: 'Fira Code', monospace;
            word-break: break-all;
        }

        .warning {
            margin-top: 25px;
            padding: 20px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 20px;
            color: #f87171;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .warning::before {
            content: '⚠️';
            font-size: 1.5rem;
        }

        .login-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
            background: linear-gradient(135deg, #38bdf8, #c084fc);
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 20px -10px rgba(56, 189, 248, 0.5);
        }

        .login-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px -10px rgba(56, 189, 248, 0.7);
            gap: 15px;
        }

        .login-link::after {
            content: '→';
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .login-link:hover::after {
            transform: translateX(5px);
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
        }

        .radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #c084fc;
            margin: 0;
            padding: 0;
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: #38bdf8;
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

        /* Кнопка */
        .btn {
            width: 100%;
            padding: 18px;
            margin-top: 30px;
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

        /* Сообщения об ошибках */
        .error-container {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 30px;
            padding: 25px;
            margin-bottom: 30px;
            color: #f87171;
        }

        .error-container strong {
            display: block;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .error-item {
            padding: 10px 0;
            border-bottom: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-item:last-child {
            border-bottom: none;
        }

        .error-title {
            font-weight: 600;
            color: #fecaca;
            margin-bottom: 5px;
        }

        .error-message {
            color: #f87171;
            font-size: 0.9rem;
        }

        .field-error {
            border-color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.05) !important;
        }

        /* Сообщение об успехе */
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 30px;
            padding: 25px;
            margin-bottom: 30px;
            color: #86efac;
            text-align: center;
            font-weight: 500;
            font-size: 1.1rem;
        }

        /* Подсказки */
        .hint {
            display: block;
            margin-top: 8px;
            color: #64748b;
            font-size: 0.85rem;
            padding-left: 15px;
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

            .credentials-box h3 {
                font-size: 1.4rem;
            }

            .credential-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Анимации */
        @keyframes glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
            }
            50% {
                box-shadow: 0 0 40px rgba(192, 132, 252, 0.5);
            }
        }

        .container {
            animation: glow 8s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Регистрационная форма</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-container">
                <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                <?php foreach ($errors as $field => $error): ?>
                    <div class="error-item">
                        <div class="error-title">Поле "<?= htmlspecialchars($field) ?>":</div>
                        <div class="error-message"><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-message">
                ✓ Данные успешно сохранены! ID записи: <?= htmlspecialchars($_GET['id'] ?? '') ?>
            </div>
            
            <?php if ($generatedLogin && $generatedPassword): ?>
                <div class="credentials-box">
                    <h3>Ваши учетные данные для входа</h3>
                    <p style="color: #94a3b8; margin-bottom: 20px;">Сохраните их! Они понадобятся для редактирования данных.</p>
                    
                    <div class="credential-item">
                        <span class="credential-label">Логин:</span>
                        <strong><?= htmlspecialchars($generatedLogin) ?></strong>
                    </div>
                    
                    <div class="credential-item">
                        <span class="credential-label">Пароль:</span>
                        <strong><?= htmlspecialchars($generatedPassword) ?></strong>
                    </div>
                    
                    <div class="warning">
                        Пароль хранится в базе в зашифрованном виде. Если потеряете, восстановить будет невозможно!
                    </div>
                    
                    <a href="login.php" class="login-link">Перейти к входу для редактирования</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form action="save.php" method="POST">
            <div class="form-grid">
                <div class="form-group <?= isset($errors['full_name']) ? 'has-error' : '' ?>">
                    <label for="full_name">ФИО *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                           placeholder="Иванов Иван Иванович"
                           class="<?= isset($errors['full_name']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['full_name']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Допустимы только буквы, пробелы и дефисы</span>
                </div>
                
                <div class="form-group <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                    <label for="phone">Телефон *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                           placeholder="+7 (999) 123-45-67"
                           class="<?= isset($errors['phone']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Допустимы цифры, пробелы, +, -, (, )</span>
                </div>
                
                <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email">E-mail *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="example@mail.com"
                           class="<?= isset($errors['email']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Формат: user@domain.com</span>
                </div>
                
                <div class="form-group <?= isset($errors['birth_date']) ? 'has-error' : '' ?>">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>"
                           class="<?= isset($errors['birth_date']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['birth_date'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['birth_date']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Формат: ГГГГ-ММ-ДД</span>
                </div>
                
                <div class="form-group <?= isset($errors['gender']) ? 'has-error' : '' ?>">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="male"
                                   <?= (isset($old['gender']) && $old['gender'] === 'male') ? 'checked' : '' ?>
                                   required> Мужской
                        </label>
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="female"
                                   <?= (isset($old['gender']) && $old['gender'] === 'female') ? 'checked' : '' ?>
                                   required> Женский
                        </label>
                    </div>
                    <?php if (isset($errors['gender'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['gender']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?= isset($errors['languages']) ? 'has-error' : '' ?>">
                    <label for="languages">Любимый язык программирования *</label>
                    <select name="languages[]" 
                            id="languages" 
                            multiple 
                            size="6"
                            class="<?= isset($errors['languages']) ? 'field-error' : '' ?>"
                            required>
                        <?php
                        $languages = [
                            'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
                            'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
                        ];
                        $selectedLanguages = $old['languages'] ?? [];
                        foreach ($languages as $lang):
                        ?>
                            <option value="<?= $lang ?>" 
                                <?= in_array($lang, $selectedLanguages) ? 'selected' : '' ?>>
                                <?= $lang ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['languages']) ?></div>
                    <?php endif; ?>
                    <small class="hint">
                        Удерживайте Ctrl (Cmd) для выбора нескольких
                    </small>
                </div>
                
                <div class="form-group full-width <?= isset($errors['biography']) ? 'has-error' : '' ?>">
                    <label for="biography">Биография</label>
                    <textarea id="biography" 
                              name="biography" 
                              placeholder="Расскажите о себе..."
                              class="<?= isset($errors['biography']) ? 'field-error' : '' ?>"><?= htmlspecialchars($old['biography'] ?? '') ?></textarea>
                    <?php if (isset($errors['biography'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['biography']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group full-width <?= isset($errors['contract_accepted']) ? 'has-error' : '' ?>">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               name="contract_accepted" 
                               id="contract" 
                               value="1"
                               <?= isset($old['contract_accepted']) ? 'checked' : '' ?>
                               required>
                        <label for="contract">Я ознакомлен(а) с контрактом *</label>
                    </div>
                    <?php if (isset($errors['contract_accepted'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['contract_accepted']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить</button>
        </form>
    </div>
</body>
</html>
