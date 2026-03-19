<?php
// save.php
require_once 'config.php';

// Функции валидации (те же, что были)
function validateFullName($name) {
    if (empty($name)) return "ФИО обязательно для заполнения";
    if (strlen($name) > 150) return "ФИО не должно превышать 150 символов";
    if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $name)) {
        return "ФИО должно содержать только буквы (А-Яа-яA-Za-z), пробелы и дефисы";
    }
    return null;
}

function validatePhone($phone) {
    if (empty($phone)) return "Телефон обязателен для заполнения";
    if (!preg_match('/^[\d\s\+\-\(\)]{5,20}$/', $phone)) {
        return "Телефон должен содержать только цифры, пробелы, символы +, -, (, )";
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) return "Email обязателен для заполнения";
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        return "Email должен быть в формате user@domain.com (только латиница, цифры, точки, дефисы)";
    }
    if (strlen($email) > 100) return "Email не должен превышать 100 символов";
    return null;
}

function validateBirthDate($date) {
    if (empty($date)) return "Дата рождения обязательна";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return "Дата должна быть в формате ГГГГ-ММ-ДД";
    }
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return "Некорректная дата";
    }
    $minDate = new DateTime('1900-01-01');
    $maxDate = new DateTime('today');
    if ($d < $minDate || $d > $maxDate) {
        return "Дата рождения должна быть между 1900-01-01 и сегодня";
    }
    return null;
}

function validateGender($gender) {
    $allowed = ['male', 'female'];
    if (!in_array($gender, $allowed)) {
        return "Выберите корректный пол (Мужской или Женский)";
    }
    return null;
}

function validateLanguages($languages) {
    if (empty($languages) || !is_array($languages)) {
        return "Выберите хотя бы один язык программирования";
    }
    $allowed = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
                'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
    foreach ($languages as $lang) {
        if (!in_array($lang, $allowed)) {
            return "Выбран недопустимый язык программирования";
        }
    }
    return null;
}

function validateBiography($bio) {
    if (strlen($bio) > 65535) {
        return "Биография слишком длинная";
    }
    return null;
}

function validateContract($contract) {
    if ($contract !== '1') {
        return "Необходимо подтвердить ознакомление с контрактом";
    }
    return null;
}

// Функция генерации логина
function generateLogin($fullName) {
    // Берем первую букву имени и фамилию транслитом
    $nameParts = explode(' ', $fullName);
    $login = '';
    
    if (isset($nameParts[0])) {
        // Фамилия
        $login .= transliterate($nameParts[0]);
    }
    if (isset($nameParts[1])) {
        // Первая буква имени
        $login .= '_' . substr(transliterate($nameParts[1]), 0, 1);
    }
    
    // Добавляем случайные цифры
    $login .= rand(100, 999);
    
    return strtolower($login);
}

// Функция транслитерации (русские буквы в латиницу)
function transliterate($string) {
    $converter = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    ];
    return strtr($string, $converter);
}

// Функция генерации пароля
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// Сохранение данных в БД с созданием учетной записи
function saveFormData($pdo, $data, $languages) {
    try {
        $pdo->beginTransaction();
        
        // Вставка в таблицу users
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, phone, email, birth_date, gender, biography, contract_accepted)
            VALUES (:full_name, :phone, :email, :birth_date, :gender, :biography, :contract_accepted)
        ");
        
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':birth_date' => $data['birth_date'],
            ':gender' => $data['gender'],
            ':biography' => $data['biography'] ?? '',
            ':contract_accepted' => isset($data['contract_accepted']) ? 1 : 0
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Получаем ID языков из справочника
        $placeholders = implode(',', array_fill(0, count($languages), '?'));
        $stmt = $pdo->prepare("SELECT id, name FROM programming_languages WHERE name IN ($placeholders)");
        $stmt->execute($languages);
        $langIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Вставка связей
        $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
        foreach ($languages as $lang) {
            if (isset($langIds[$lang])) {
                $stmt->execute([$userId, $langIds[$lang]]);
            }
        }
        
        // Генерируем логин и пароль
        $login = generateLogin($data['full_name']);
        $password = generatePassword();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Сохраняем учетную запись
        $stmt = $pdo->prepare("
            INSERT INTO user_accounts (user_id, login, password_hash)
            VALUES (:user_id, :login, :password_hash)
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':login' => $login,
            ':password_hash' => $passwordHash
        ]);
        
        $pdo->commit();
        
        // Возвращаем userId и сгенерированные логин/пароль
        return [
            'user_id' => $userId,
            'login' => $login,
            'password' => $password
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Основная логика обработки
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$errors = [];

// Валидация всех полей (как раньше)
if ($error = validateFullName($_POST['full_name'] ?? '')) {
    $errors['full_name'] = $error;
}
if ($error = validatePhone($_POST['phone'] ?? '')) {
    $errors['phone'] = $error;
}
if ($error = validateEmail($_POST['email'] ?? '')) {
    $errors['email'] = $error;
}
if ($error = validateBirthDate($_POST['birth_date'] ?? '')) {
    $errors['birth_date'] = $error;
}
if ($error = validateGender($_POST['gender'] ?? '')) {
    $errors['gender'] = $error;
}
$languages = $_POST['languages'] ?? [];
if ($error = validateLanguages($languages)) {
    $errors['languages'] = $error;
}
if ($error = validateBiography($_POST['biography'] ?? '')) {
    $errors['biography'] = $error;
}
if ($error = validateContract($_POST['contract_accepted'] ?? '')) {
    $errors['contract_accepted'] = $error;
}

// Если есть ошибки, сохраняем в Cookies и возвращаемся
if (!empty($errors)) {
    setcookie('form_errors', json_encode($errors), 0, '/');
    setcookie('form_data', json_encode($_POST), 0, '/');
    header('Location: index.php');
    exit;
}

// Если ошибок нет - сохраняем данные
try {
    $result = saveFormData($pdo, $_POST, $languages);
    
    // Сохраняем данные в Cookies на 1 год
    setcookie('form_data', json_encode($_POST), time() + 365*24*60*60, '/');
    setcookie('form_errors', '', time() - 3600, '/');
    
    // Сохраняем логин и пароль в сессии для отображения
    session_start();
    $_SESSION['generated_login'] = $result['login'];
    $_SESSION['generated_password'] = $result['password'];
    
    header("Location: index.php?success=1&id=" . $result['user_id']);
    exit;
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    
    $errors['database'] = 'Ошибка при сохранении данных. Пожалуйста, попробуйте позже.';
    setcookie('form_errors', json_encode($errors), 0, '/');
    setcookie('form_data', json_encode($_POST), 0, '/');
    
    header('Location: index.php');
    exit;
}
?>
