<?php
/**
 * Скрипт для запуска всех тестов проекта
 */

echo "=== ЗАПУСК ТЕСТОВ ПРОЕКТА PORANAM ===\n\n";

// 1. Синтаксическая проверка PHP файлов
echo "1. Проверка синтаксиса PHP файлов...\n";

$phpFiles = array(
    'Connections/functions.php',
    'modules/ajaxHandlers/init_vote.php',
    'modules/ajaxHandlers/add_initiative.php',
    'modules/ajaxHandlers/edit_initiative.php',
    'modules/ajaxHandlers/getVotes.php',
    'js/votes.js'
);

$syntaxErrors = 0;

foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        $output = shell_exec("php -l \"$file\" 2>&1");
        if (strpos($output, 'No syntax errors') === false) {
            echo "✗ Синтаксическая ошибка в $file:\n$output\n";
            $syntaxErrors++;
        } else {
            echo "✓ $file - OK\n";
        }
    } else {
        echo "? $file - файл не найден\n";
    }
}

echo "\n";

// 2. Запуск функциональных тестов голосования
echo "2. Запуск функциональных тестов голосования...\n";

if (file_exists('tests/voting_tests.php')) {
    echo "Запуск voting_tests.php...\n\n";
    system('php tests/voting_tests.php');
} else {
    echo "✗ Файл tests/voting_tests.php не найден\n";
}

echo "\n";

// 3. Проверка подключения к базе данных
echo "3. Проверка подключения к базе данных...\n";

try {
    require_once 'Connections/dbconn.php';
    // Простой запрос для проверки подключения
    $testQuery = el_dbselect("SELECT 1 as test", 0, $testResult, 'row', true);
    if ($testResult && isset($testResult['test'])) {
        echo "✓ Подключение к БД успешно\n";
    } else {
        echo "✗ Ошибка подключения к БД\n";
    }
} catch (Exception $e) {
    echo "✗ Ошибка подключения к БД: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Проверка наличия необходимых таблиц
echo "4. Проверка наличия необходимых таблиц...\n";

$requiredTables = array(
    'catalog_init_data',
    'catalog_users_data',
    'catalog_initresult_data',
    'catalog_scores_data',
    'phpSP_users'
);

foreach ($requiredTables as $table) {
    try {
        $checkTable = el_dbselect("SHOW TABLES LIKE '$table'", 0, $tableResult, 'row', true);
        if ($tableResult) {
            echo "✓ Таблица $table существует\n";
        } else {
            echo "✗ Таблица $table не найдена\n";
        }
    } catch (Exception $e) {
        echo "✗ Ошибка проверки таблицы $table: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Итоги
echo "=== ИТОГИ ТЕСТИРОВАНИЯ ===\n";
echo "Синтаксических ошибок: $syntaxErrors\n";

if ($syntaxErrors === 0) {
    echo "✓ Все PHP файлы прошли синтаксическую проверку\n";
} else {
    echo "✗ Обнаружены синтаксические ошибки\n";
}

echo "\nТестирование завершено.\n";

?>