<?php
/**
 * Упрощенные тесты логики системы голосования (без БД)
 * Запуск: php tests/logic_tests.php
 */

// Функция для вывода результатов теста
function testResult($testName, $result, $expected = null, $actual = null) {
    echo "Тест: $testName - ";
    if ($result) {
        echo "✓ ПРОЙДЕН\n";
    } else {
        echo "✗ ПРОВАЛЕН\n";
        if ($expected !== null && $actual !== null) {
            echo "  Ожидалось: " . var_export($expected, true) . "\n";
            echo "  Получено: " . var_export($actual, true) . "\n";
        }
    }
    echo "\n";
}

// Тест 1: Логика "Выбрать всех участников"
function testSelectAllLogic() {
    echo "=== ТЕСТ 1: Логика 'Выбрать всех участников' ===\n";

    // Имитируем POST данные с выбором "Выбрать всех"
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

    // Логика сохранения полей (как в add_initiative.php и edit_initiative.php)
    $field5 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field5'];
    $field6 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field6'];
    $field7 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field7'];
    $field8 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field8'];
    $field9 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field9'];
    $field10 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field10'];
    $field11 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field11'];
    $field13 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field13'];

    // Все поля должны быть пустыми
    $allFieldsEmpty = empty($field5) && empty($field6) && empty($field7) && empty($field8) &&
                     empty($field9) && empty($field10) && empty($field11) && empty($field13);

    testResult("Все поля пустые при выборе 'Выбрать всех участников'", $allFieldsEmpty, true, $allFieldsEmpty);

    // Проверяем обратную логику (без выбора "Выбрать всех")
    $_POST['init_select_all'] = '0';

    $field5 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field5'];
    $field6 = (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1') ? '' : $_POST['field6'];

    $fieldsNotEmpty = !empty($field5) && !empty($field6);

    testResult("Поля сохраняются при обычном выборе участников", $fieldsNotEmpty, true, $fieldsNotEmpty);
}

// Тест 2: Логика проверки прав на голосование
function testVotingRightsLogic() {
    echo "=== ТЕСТ 2: Логика проверки прав на голосование ===\n";

    // Тест 1: Голосование для всех (все поля пустые)
    $voteDataAll = array(
        'field5' => '', // субъект
        'field6' => '', // регион
        'field7' => '', // профессия
        'field8' => '', // город
        'field9' => '', // индекс
        'field10' => '', // улица
        'field11' => '', // дом
        'field13' => ''  // ранг
    );

    $allFieldsEmpty = empty($voteDataAll['field5']) && empty($voteDataAll['field6']) && empty($voteDataAll['field7']) &&
                     empty($voteDataAll['field8']) && empty($voteDataAll['field9']) && empty($voteDataAll['field10']) &&
                     empty($voteDataAll['field11']) && empty($voteDataAll['field13']);

    testResult("Определение голосования 'для всех'", $allFieldsEmpty, true, $allFieldsEmpty);

    // Тест 2: Голосование с критериями
    $voteDataCriteria = array(
        'field5' => '1', // субъект = 1
        'field6' => '', // регион пустой
        'field7' => '', // профессия пустая
        'field8' => '', // город пустой
        'field9' => '', // индекс пустой
        'field10' => '', // улица пустая
        'field11' => '', // дом пустой
        'field13' => ''  // ранг пустой
    );

    $userData = array(
        'field8' => '1', // субъект пользователя = 1
        'field9' => '2', // регион пользователя = 2
        'field7' => '3', // профессия пользователя = 3
        'field10' => 'Москва', // город пользователя
        'field11' => '101000', // индекс пользователя
        'field12' => 'Ленина', // улица пользователя
        'field13' => '10' // дом пользователя
    );

    // Проверяем логику соответствия критериям
    $userCanVote = true;

    // Проверяем субъект (если указан)
    if(intval($voteDataCriteria['field5']) > 0 && intval($userData['field8']) != intval($voteDataCriteria['field5'])) {
        $userCanVote = false;
    }
    // Проверяем регион (если указан)
    if(intval($voteDataCriteria['field6']) > 0 && intval($userData['field9']) != intval($voteDataCriteria['field6'])) {
        $userCanVote = false;
    }
    // Проверяем профессию (если указана)
    if(intval($voteDataCriteria['field7']) > 0 && intval($userData['field7']) != intval($voteDataCriteria['field7'])) {
        $userCanVote = false;
    }
    // Проверяем город (если указан)
    if(strlen($voteDataCriteria['field8']) > 0 && $userData['field10'] != $voteDataCriteria['field8']) {
        $userCanVote = false;
    }
    // Проверяем индекс (если указан)
    if(strlen($voteDataCriteria['field9']) > 0 && $userData['field11'] != $voteDataCriteria['field9']) {
        $userCanVote = false;
    }
    // Проверяем улицу (если указана)
    if(strlen($voteDataCriteria['field10']) > 0 && $userData['field12'] != $voteDataCriteria['field10']) {
        $userCanVote = false;
    }
    // Проверяем дом (если указан)
    if(strlen($voteDataCriteria['field11']) > 0 && $userData['field13'] != $voteDataCriteria['field11']) {
        $userCanVote = false;
    }
    // Проверяем ранг (если указан)
    if(intval($voteDataCriteria['field13']) > 0 && intval($userData['field6']) != intval($voteDataCriteria['field13'])) {
        $userCanVote = false;
    }

    // Пользователь должен иметь право голосовать (субъект совпадает, остальные критерии пустые)
    testResult("Пользователь соответствует критериям голосования", $userCanVote, true, $userCanVote);

    // Тест 3: Пользователь НЕ соответствует критериям
    $voteDataStrict = array(
        'field5' => '999', // субъект = 999 (несуществующий)
        'field6' => '',
        'field7' => '',
        'field8' => '',
        'field9' => '',
        'field10' => '',
        'field11' => '',
        'field13' => ''
    );

    $userCanVoteStrict = true;

    if(intval($voteDataStrict['field5']) > 0 && intval($userData['field8']) != intval($voteDataStrict['field5'])) {
        $userCanVoteStrict = false;
    }

    testResult("Пользователь НЕ соответствует строгим критериям", !$userCanVoteStrict, true, !$userCanVoteStrict);
}

// Тест 3: Логика фильтрации голосований
function testFilteringLogic() {
    echo "=== ТЕСТ 3: Логика фильтрации голосований ===\n";

    // Имитируем данные пользователя
    $userData = array(
        'field8' => '1', // субъект
        'field9' => '2', // регион
        'field7' => '3', // профессия
        'field10' => 'Москва', // город
        'field11' => '101000', // индекс
        'field12' => 'Ленина', // улица
        'field13' => '10' // дом
    );

    // Создаем условия фильтрации (как в el_buildCatalogSubQuery)
    $userConditions = array();

    // Субъект: field5 голосования -> field8 пользователя
    if(intval($userData['field8']) > 0) {
        $userConditions[] = "(field5 = '' OR field5 IS NULL OR field5 = ".intval($userData['field8']).")";
    } else {
        $userConditions[] = "(field5 = '' OR field5 IS NULL)";
    }

    // Регион: field6 голосования -> field9 пользователя
    if(intval($userData['field9']) > 0) {
        $userConditions[] = "(field6 = '' OR field6 IS NULL OR field6 = ".intval($userData['field9']).")";
    } else {
        $userConditions[] = "(field6 = '' OR field6 IS NULL)";
    }

    // Профессия: field7 голосования -> field7 пользователя
    if(intval($userData['field7']) > 0) {
        $userConditions[] = "(field7 = '' OR field7 IS NULL OR field7 = ".intval($userData['field7']).")";
    } else {
        $userConditions[] = "(field7 = '' OR field7 IS NULL)";
    }

    // Проверяем, что все условия созданы
    $hasAllConditions = count($userConditions) === 3;

    testResult("Создание условий фильтрации для всех полей", $hasAllConditions, true, $hasAllConditions);

    // Проверяем структуру условий
    $hasOrLogic = strpos($userConditions[0], 'OR') !== false && strpos($userConditions[0], 'field5 = \'\'') !== false;

    testResult("Условия содержат логику ИЛИ для пустых полей", $hasOrLogic, true, $hasOrLogic);
}

// Тест 4: Проверка расчетов
function testCalculationLogic() {
    echo "=== ТЕСТ 4: Логика расчетов ===\n";

    // Функция расчета процента (как в el_calcVoteUsers)
    function el_calcPercent($votes, $total) {
        if ($total == 0) return 0;
        return round(($votes / $total) * 100, 2);
    }

    // Тест 1: Нормальный расчет
    $percent1 = el_calcPercent(10, 20);
    $expected1 = 50.00;

    testResult("Расчет процента (10/20)", abs($percent1 - $expected1) < 0.01, $expected1, $percent1);

    // Тест 2: Деление на ноль
    $percent2 = el_calcPercent(5, 0);
    $expected2 = 0;

    testResult("Расчет процента при делении на ноль", $percent2 === $expected2, $expected2, $percent2);

    // Тест 3: 100% результат
    $percent3 = el_calcPercent(15, 15);
    $expected3 = 100.00;

    testResult("Расчет процента 100%", abs($percent3 - $expected3) < 0.01, $expected3, $percent3);
}

// Запуск всех тестов
echo "ЗАПУСК ЛОГИЧЕСКИХ ТЕСТОВ СИСТЕМЫ ГОЛОСОВАНИЯ\n";
echo "===============================================\n\n";

try {
    testSelectAllLogic();
    testVotingRightsLogic();
    testFilteringLogic();
    testCalculationLogic();

    echo "===============================================\n";
    echo "ВСЕ ЛОГИЧЕСКИЕ ТЕСТЫ ЗАВЕРШЕНЫ\n";

} catch (Exception $e) {
    echo "ОШИБКА ПРИ ВЫПОЛНЕНИИ ТЕСТОВ: " . $e->getMessage() . "\n";
}

?>