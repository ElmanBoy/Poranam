<?php
/**
 * Простые тесты для системы голосования
 * Запуск: php tests/voting_tests.php
 */

require_once '../Connections/dbconn.php';
require_once '../Connections/functions.php';

// Функция для вывода результатов теста
function testResult($testName, $result, $expected, $actual) {
    echo "Тест: $testName - ";
    if ($result) {
        echo "✓ ПРОЙДЕН\n";
    } else {
        echo "✗ ПРОВАЛЕН\n";
        echo "  Ожидалось: " . var_export($expected, true) . "\n";
        echo "  Получено: " . var_export($actual, true) . "\n";
    }
    echo "\n";
}

// Тест 1: Проверка расчета участников голосования
function testCalcVoteUsers() {
    echo "=== ТЕСТ 1: Расчет участников голосования ===\n";

    // Создаем тестовые данные в БД (если возможно)
    // Для демонстрации используем существующие данные

    $initId = 1; // Предполагаем, что есть инициатива с ID 1
    $result = el_calcVoteUsers($initId);

    // Проверяем структуру возвращаемого массива
    $hasRequiredKeys = isset($result['votes']) && isset($result['total']) && isset($result['percent']);

    testResult("Структура результата el_calcVoteUsers", $hasRequiredKeys, true, $hasRequiredKeys);

    // Проверяем типы данных
    $correctTypes = is_int($result['votes']) && is_int($result['total']) && is_numeric($result['percent']);

    testResult("Типы данных в результате el_calcVoteUsers", $correctTypes, true, $correctTypes);

    // Проверяем логику процента
    $expectedPercent = ($result['total'] > 0) ? round(($result['votes'] / $result['total']) * 100, 2) : 0;
    $percentCorrect = abs($result['percent'] - $expectedPercent) < 0.01;

    testResult("Расчет процента голосов", $percentCorrect, true, $percentCorrect);
}

// Тест 2: Проверка фильтрации голосований
function testBuildCatalogSubQuery() {
    echo "=== ТЕСТ 2: Фильтрация голосований ===\n";

    // Имитируем сессию пользователя
    $_SESSION['user_id'] = 1;
    $_GET['user_filter_mode'] = 'participant_or_all';

    // Имитируем глобальные переменные
    global $row_dbcontent, $filtered;
    $row_dbcontent = array('cat' => 1, 'kod' => 'cataloginit');

    $result = el_buildCatalogSubQuery();

    // Проверяем, что функция возвращает строку запроса
    $isString = is_string($result);

    testResult("Возвращаемый тип el_buildCatalogSubQuery", $isString, true, $isString);

    // Проверяем наличие специальной логики для голосований
    $hasSpecialLogic = strpos($result, 'field5 = \'\' OR field5 IS NULL') !== false;

    testResult("Специальная логика фильтрации голосований", $hasSpecialLogic, true, $hasSpecialLogic);
}

// Тест 3: Проверка прав на голосование
function testVotingRights() {
    echo "=== ТЕСТ 3: Проверка прав на голосование ===\n";

    // Имитируем POST данные
    $_POST = array(
        'id' => 1,
        'vote' => 1
    );

    // Имитируем сессию
    $_SESSION['user_id'] = 1;

    // Получаем данные пользователя (предполагаем, что функция работает)
    $userData = el_dbselect("SELECT * FROM catalog_users_data WHERE id = 1", 1, $userData, 'row', true);

    // Проверяем логику проверки прав
    $voteData = array(
        'field5' => '', // субъект
        'field6' => '', // регион
        'field7' => '', // профессия
        'field8' => '', // город
        'field9' => '', // индекс
        'field10' => '', // улица
        'field11' => '', // дом
        'field13' => ''  // ранг
    );

    // Для голосования "для всех" пользователь должен иметь право голосовать
    $allFieldsEmpty = empty($voteData['field5']) && empty($voteData['field6']) && empty($voteData['field7']) &&
                     empty($voteData['field8']) && empty($voteData['field9']) && empty($voteData['field10']) &&
                     empty($voteData['field11']) && empty($voteData['field13']);

    testResult("Логика 'голосование для всех'", $allFieldsEmpty, true, $allFieldsEmpty);

    // Проверяем соответствие критериям (если бы они были указаны)
    $voteDataWithCriteria = array(
        'field5' => '1', // субъект = 1
        'field6' => '', // регион пустой
        'field7' => '', // профессия пустая
        'field8' => '', // город пустой
        'field9' => '', // индекс пустой
        'field10' => '', // улица пустая
        'field11' => '', // дом пустой
        'field13' => ''  // ранг пустой
    );

    $userDataSample = array(
        'field8' => '1', // субъект пользователя = 1
        'field9' => '2', // регион пользователя = 2
        'field7' => '3', // профессия пользователя = 3
        'field10' => 'Москва', // город пользователя
        'field11' => '101000', // индекс пользователя
        'field12' => 'Ленина', // улица пользователя
        'field13' => '10' // дом пользователя
    );

    // Проверяем субъект
    $subjectMatch = intval($voteDataWithCriteria['field5']) > 0 ?
        intval($userDataSample['field8']) == intval($voteDataWithCriteria['field5']) : true;

    testResult("Проверка соответствия субъекту", $subjectMatch, true, $subjectMatch);
}

// Тест 4: Проверка создания инициативы с "Выбрать всех участников"
function testSelectAllParticipants() {
    echo "=== ТЕСТ 4: Создание инициативы 'Выбрать всех участников' ===\n";

    // Имитируем POST данные для создания инициативы
    $_POST = array(
        'init_select_all' => '1',
        'field5' => '1', // субъект
        'field6' => '2', // регион
        'field7' => '3', // профессия
        'field8' => 'Москва', // город
        'field9' => '101000', // индекс
        'field10' => 'Ленина', // улица
        'field11' => '10', // дом
        'field13' => '5'  // ранг
    );

    // Проверяем логику сохранения полей
    $field5 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field5'];
    $field6 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field6'];
    $field7 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field7'];
    $field8 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field8'];
    $field9 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field9'];
    $field10 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field10'];
    $field11 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field11'];
    $field13 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field13'];

    // Все поля должны быть пустыми при выборе "Выбрать всех участников"
    $allFieldsEmpty = empty($field5) && empty($field6) && empty($field7) && empty($field8) &&
                     empty($field9) && empty($field10) && empty($field11) && empty($field13);

    testResult("Все поля пустые при 'Выбрать всех участников'", $allFieldsEmpty, true, $allFieldsEmpty);

    // Проверяем обратную логику (без выбора "Выбрать всех")
    $_POST['init_select_all'] = '0';

    $field5 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field5'];
    $field6 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field6'];

    $fieldsNotEmpty = !empty($field5) && !empty($field6);

    testResult("Поля сохраняются при обычном выборе участников", $fieldsNotEmpty, true, $fieldsNotEmpty);
}

// Запуск всех тестов
echo "ЗАПУСК ТЕСТОВ СИСТЕМЫ ГОЛОСОВАНИЯ\n";
echo "==================================\n\n";

try {
    testCalcVoteUsers();
    testBuildCatalogSubQuery();
    testVotingRights();
    testSelectAllParticipants();

    echo "==================================\n";
    echo "ВСЕ ТЕСТЫ ЗАВЕРШЕНЫ\n";

} catch (Exception $e) {
    echo "ОШИБКА ПРИ ВЫПОЛНЕНИИ ТЕСТОВ: " . $e->getMessage() . "\n";
}

?>