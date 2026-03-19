<?php
session_start();
require_once 'config.php';

// Включаем отображение всех ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Смотрим, что пришло из формы
echo "<h3>Отладочная информация:</h3>";
echo "<pre>";
echo "Метод запроса: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST данные:\n";
print_r($_POST);
echo "Сырые данные POST:\n";
echo file_get_contents('php://input');
echo "</pre>";

// Проверяем, что это POST запрос
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Ошибка: Не POST запрос");
}

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

echo "Логин после обработки: '$login'\n";
echo "Пароль после обработки: '$password'\n";

if (empty($login) || empty($password)) {
    die("Ошибка: Пустой логин или пароль");
}

// Если дошли до сюда - данные пришли
echo "Данные получены, пробуем авторизацию...";

// Дальше можно добавить код проверки в БД
try {
    // Поиск пользователя по логину
    $stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "\n\n✅ Пользователь найден в БД!\n";
        echo "Логин из БД: " . $user['login'] . "\n";
        echo "Хеш пароля: " . $user['password_hash'] . "\n";
        
        // Проверяем пароль
        if (password_verify($password, $user['password_hash'])) {
            echo "✅ Пароль верный!\n";
            
            // Создаем сессию
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['authenticated'] = true;
            
            echo "✅ Сессия создана! Перенаправляем на edit.php...\n";
            echo "Через 3 секунды вы будете перенаправлены...";
            
            // Перенаправление через JavaScript
            echo '<script>setTimeout(function() { window.location.href = "edit.php"; }, 3000);</script>';
            
        } else {
            echo "❌ Пароль НЕ верный!\n";
        }
    } else {
        echo "\n❌ Пользователь с логином '$login' НЕ найден в БД!\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Ошибка БД: " . $e->getMessage() . "\n";
}
?>
