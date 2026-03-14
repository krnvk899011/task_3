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
    background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
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
    background: radial-gradient(circle at 20% 50%, rgba(76, 29, 149, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

.container {
    max-width: 900px;
    width: 100%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 32px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    padding: 40px;
    position: relative;
    z-index: 1;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

h1 {
    text-align: center;
    color: #0f172a;
    margin-bottom: 35px;
    font-weight: 700;
    font-size: 2.2rem;
    letter-spacing: -0.02em;
    position: relative;
    padding-bottom: 15px;
}

h1::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #6366f1, #a855f7);
    border-radius: 4px;
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

label::after {
    content: '*';
    color: #ef4444;
    margin-left: 4px;
    opacity: 0.7;
}

.checkbox-group label::after,
.radio-group label::after {
    display: none;
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
    border-radius: 16px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #f8fafc;
    font-family: inherit;
    color: #0f172a;
}

input:hover,
select:hover,
textarea:hover {
    border-color: #94a3b8;
    background: white;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
}

.radio-group {
    display: flex;
    gap: 30px;
    padding: 10px 0;
    background: #f8fafc;
    border-radius: 16px;
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: 500;
    color: #334155;
    transition: color 0.2s;
    position: relative;
}

.radio-option:hover {
    color: #6366f1;
}

.radio-option input[type="radio"] {
    width: 20px;
    height: 20px;
    accent-color: #6366f1;
    cursor: pointer;
    margin: 0;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 16px;
    border: 2px solid #bae6fd;
    transition: all 0.3s;
    cursor: pointer;
}

.checkbox-group:hover {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -10px rgba(6, 182, 212, 0.4);
}

.checkbox-group input[type="checkbox"] {
    width: 22px;
    height: 22px;
    cursor: pointer;
    accent-color: #0284c7;
    margin: 0;
}

.checkbox-group label {
    margin: 0;
    color: #0369a1;
    font-weight: 600;
    font-size: 1rem;
    text-transform: none;
    cursor: pointer;
}

select[multiple] {
    height: 180px;
    background: #f8fafc;
    padding: 8px;
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
    border: 1px solid transparent;
}

select[multiple] option:hover {
    background: #f1f5f9;
    border-color: #6366f1;
}

select[multiple] option:checked {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
}

select[multiple] option:checked:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

.btn {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: white;
    border: none;
    padding: 18px 30px;
    font-size: 1.1rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-top: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 30px -5px rgba(99, 102, 241, 0.6);
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
}

.btn:hover::before {
    left: 100%;
}

.btn:active {
    transform: translateY(0);
    box-shadow: 0 5px 15px -5px rgba(99, 102, 241, 0.6);
}

.error-message {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
    padding: 18px 20px;
    border-radius: 20px;
    margin-bottom: 30px;
    border-left: 6px solid #dc2626;
    font-size: 0.95rem;
    box-shadow: 0 10px 20px -10px rgba(220, 38, 38, 0.3);
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-10px); }
    40% { transform: translateX(10px); }
    60% { transform: translateX(-5px); }
    80% { transform: translateX(5px); }
}

.error-message ul {
    margin-left: 25px;
    margin-top: 10px;
}

.error-message li {
    margin: 5px 0;
}

.success-message {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
    padding: 18px 20px;
    border-radius: 20px;
    margin-bottom: 30px;
    border-left: 6px solid #16a34a;
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 10px 20px -10px rgba(22, 163, 74, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.field-error {
    border-color: #dc2626 !important;
    background: #fef2f2 !important;
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
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #94a3b8, #64748b);
    border-radius: 10px;
    border: 2px solid #f1f5f9;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #64748b, #475569);
}

@media (max-width: 768px) {
    .container {
        padding: 25px;
        border-radius: 24px;
    }
    
    h1 {
        font-size: 1.8rem;
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
    }
    
    select[multiple] {
        height: 150px;
    }
}

/* Добавляем Google Fonts в head */
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
