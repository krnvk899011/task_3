<?php
// save.php
session_start();
require_once 'config.php';

// Функции валидации
function validateFullName($name) {
    if (empty($name)) return "ФИО обязательно для заполнения";
    if (strlen($name) > 150) return "ФИО не должно превышать 150 символов";
    if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $name)) {
        return "ФИО должно содержать только буквы, пробелы и дефисы";
    }
    return null;
}

function validatePhone($phone) {
    if (empty($phone)) return "Телефон обязателен для заполнения";
    if (!preg_match('/^[\d\s\+\-\(\)]{5,20}$/', $phone)) {
        return "Телефон должен содержать от 5 до 20 символов: цифры, пробелы, +, -, (, )";
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) return "Email обязателен для заполнения";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Некорректный формат email";
    }
    if (strlen($email) > 100) return "Email не должен превышать 100 символов";
    return null;
}

function validateBirthDate($date) {
    if (empty($date)) return "Дата рождения обязательна";
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return "Некорректный формат даты";
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
        return "Выберите корректный пол";
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
            return "Обнаружен недопустимый язык программирования";
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

// Сохранение данных в БД
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
        
        $pdo->commit();
        return $userId;
        
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

// Валидация всех полей
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

// Если есть ошибки, возвращаемся с сообщениями
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: index.php');
    exit;
}

// Сохраняем данные
try {
    $userId = saveFormData($pdo, $_POST, $languages);
    header("Location: index.php?success=1&id=$userId");
    exit;
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['errors'] = ['database' => 'Ошибка при сохранении данных. Пожалуйста, попробуйте позже.'];
    $_SESSION['old'] = $_POST;
    header('Location: index.php');
    exit;
}
?>
