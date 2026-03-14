```php
<?php
// index.php
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрационная форма</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(145deg, #0b1120 0%, #192132 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(147, 51, 234, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 30% 70%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255, 255, 255, 0.2) inset,
                        0 0 40px rgba(79, 70, 229, 0.2);
            padding: 45px;
            position: relative;
            z-index: 1;
            animation: floatIn 0.7s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        h1 {
            text-align: center;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 35px;
            font-weight: 800;
            font-size: 2.5rem;
            letter-spacing: -0.02em;
            position: relative;
            padding-bottom: 20px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #9333ea, #06b6d4);
            border-radius: 4px;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-group {
            margin-bottom: 0;
            position: relative;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        label[for="full_name"]::after,
        label[for="phone"]::after,
        label[for="email"]::after,
        label[for="birth_date"]::after,
        .form-group:has(.radio-group) label::after,
        label[for="languages"]::after,
        .checkbox-group label::after {
            content: ' *';
            color: #ef4444;
            font-weight: 700;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
            font-family: inherit;
            color: #0f172a;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        input:hover,
        select:hover,
        textarea:hover {
            border-color: #94a3b8;
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15), 0 5px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .radio-group {
            display: flex;
            gap: 30px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 20px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 500;
            color: #334155;
            transition: all 0.2s;
            position: relative;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .radio-option:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
        }

        .radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #4f46e5;
            cursor: pointer;
            margin: 0;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 22px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 20px;
            border: 2px solid #a7f3d0;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
        }

        .checkbox-group:hover {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
            border-color: #34d399;
        }

        .checkbox-group input[type="checkbox"] {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: #10b981;
            margin: 0;
        }

        .checkbox-group label {
            margin: 0;
            color: #065f46;
            font-weight: 700;
            font-size: 1rem;
            text-transform: none;
            cursor: pointer;
        }

        select[multiple] {
            height: 200px;
            background: #f8fafc;
            padding: 10px;
            cursor: pointer;
        }

        select[multiple] option {
            padding: 12px 16px;
            margin: 4px 0;
            border-radius: 12px;
            background: white;
            color: #1e293b;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        select[multiple] option:hover {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            transform: translateX(5px);
            border-color: #4f46e5;
        }

        select[multiple] option:checked {
            background: linear-gradient(135deg, #4f46e5, #9333ea);
            color: white;
            font-weight: 600;
            border: none;
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .btn {
            background: linear-gradient(135deg, #4f46e5, #9333ea, #06b6d4);
            color: white;
            border: none;
            padding: 18px 30px;
            font-size: 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            font-weight: 700;
            letter-spacing: 1px;
            margin-top: 35px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px -5px rgba(79, 70, 229, 0.4);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .btn:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 25px 40px -5px rgba(79, 70, 229, 0.6);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: translateY(0) scale(1);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .error-message {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            padding: 18px 22px;
            border-radius: 20px;
            margin-bottom: 30px;
            border-left: 6px solid #dc2626;
            font-size: 0.95rem;
            box-shadow: 0 10px 25px -10px #dc2626;
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-6px); }
            40%, 60% { transform: translateX(6px); }
        }

        .error-message ul {
            margin-left: 25px;
            margin-top: 12px;
        }

        .error-message li {
            margin: 6px 0;
            font-weight: 500;
        }

        .success-message {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #065f46;
            padding: 18px 22px;
            border-radius: 20px;
            margin-bottom: 30px;
            border-left: 6px solid #10b981;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 10px 25px -10px #10b981;
            animation: slideDown 0.5s ease, glow 2s infinite;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 10px 25px -10px #10b981; }
            50% { box-shadow: 0 15px 35px -5px #10b981; }
        }

        .field-error {
            border-color: #dc2626 !important;
            background: #fef2f2 !important;
            animation: pulse 0.5s ease;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .field-error:focus {
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.15) !important;
        }

        small {
            color: #64748b;
            display: block;
            margin-top: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        select:focus + small {
            color: #4f46e5;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #94a3b8, #64748b);
            border-radius: 20px;
            border: 3px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #4f46e5, #9333ea);
        }

        /* Loading animation for form */
        .container {
            position: relative;
        }

        .container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 32px;
            pointer-events: none;
            background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.2) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .container:active::after {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                border-radius: 24px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                padding: 16px 25px;
                font-size: 1.1rem;
            }
            
            select[multiple] {
                height: 160px;
            }
            
            .checkbox-group {
                padding: 14px 18px;
            }
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 20px;
            }
            
            .btn {
                display: none;
            }
        }

        /* Add Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    </style>
</head>
<body>
    <div class="container">
        <h1>Регистрационная форма</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-message">
                ✓ Данные успешно сохранены! ID записи: <?= htmlspecialchars($_GET['id'] ?? '') ?>
            </div>
        <?php endif; ?>
        
        <form action="save.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                           placeholder="Иванов Иван Иванович"
                           class="<?= isset($errors['full_name']) ? 'field-error' : '' ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                           placeholder="+7 (999) 123-45-67"
                           class="<?= isset($errors['phone']) ? 'field-error' : '' ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="example@mail.com"
                           class="<?= isset($errors['email']) ? 'field-error' : '' ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>"
                           class="<?= isset($errors['birth_date']) ? 'field-error' : '' ?>"
                           required>
                </div>
                
                <div class="form-group">
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
                </div>
                
                <div class="form-group">
                    <label for="languages">Любимый язык программирования * (множественный выбор)</label>
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
                    <small style="color: #777; display: block; margin-top: 5px;">
                        Удерживайте Ctrl (Cmd) для выбора нескольких
                    </small>
                </div>
                
                <div class="form-group full-width">
                    <label for="biography">Биография</label>
                    <textarea id="biography" 
                              name="biography" 
                              placeholder="Расскажите о себе..."><?= htmlspecialchars($old['biography'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               name="contract_accepted" 
                               id="contract" 
                               value="1"
                               <?= isset($old['contract_accepted']) ? 'checked' : '' ?>
                               required>
                        <label for="contract">Я ознакомлен(а) с контрактом *</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить</button>
        </form>
    </div>
</body>
</html>
```
