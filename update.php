<?php
// update.php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Функции валидации (те же, что в save.php)
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
        return "Телефон должен содержать только цифры, пробелы, символы +, -, (, )";
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) return "Email обязателен для заполнения";
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        return "Email должен быть в формате user@domain.com";
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

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: edit.php');
    exit;
}

// Валидация
$errors = [];

if ($error = validateFullName($_POST['full_name'] ?? '')) {
    $errors[] = $error;
}
if ($error = validatePhone($_POST['phone'] ?? '')) {
    $errors[] = $error;
}
if ($error = validateEmail($_POST['email'] ?? '')) {
    $errors[] = $error;
}
if ($error = validateBirthDate($_POST['birth_date'] ?? '')) {
    $errors[] = $error;
}
if ($error = validateGender($_POST['gender'] ?? '')) {
    $errors[] = $error;
}
$languages = $_POST['languages'] ?? [];
if ($error = validateLanguages($languages)) {
    $errors[] = $error;
}
if ($error = validateBiography($_POST['biography'] ?? '')) {
    $errors[] = $error;
}

// Если есть ошибки
if (!empty($errors)) {
    $errorString = implode('\\n', $errors);
    header('Location: edit.php?error=' . urlencode($errorString));
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Обновляем данные пользователя
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'] ?? '',
        $user_id
    ]);
    
    // Удаляем старые языки
    $stmt = $pdo->prepare("DELETE FROM user_languages WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Добавляем новые языки
    $stmt = $pdo->prepare("SELECT id, name FROM programming_languages WHERE name IN (" . implode(',', array_fill(0, count($languages), '?')) . ")");
    $stmt->execute($languages);
    $langIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
    foreach ($languages as $lang) {
        if (isset($langIds[$lang])) {
            $stmt->execute([$user_id, $langIds[$lang]]);
        }
    }
    
    $pdo->commit();
    
    // Обновляем данные в сессии
    $_SESSION['edit_data'] = [
        'full_name' => $_POST['full_name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'birth_date' => $_POST['birth_date'],
        'gender' => $_POST['gender'],
        'biography' => $_POST['biography'] ?? '',
        'languages' => $languages
    ];
    
    header('Location: edit.php?success=1');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Update error: " . $e->getMessage());
    header('Location: edit.php?error=' . urlencode('Ошибка при обновлении данных'));
    exit;
}
?>
