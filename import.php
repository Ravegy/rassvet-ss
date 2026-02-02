<?php
require_once 'includes/db.php';

// Настройка: разделитель в CSV (в Excel обычно точка с запятой ;)
$delimiter = ';';

echo "<h1>Мастер импорта данных (Optimized)</h1>";
echo "<p>Положите файлы <b>structure.csv</b> и <b>parts.csv</b> в корень сайта и обновите эту страницу.</p><hr>";

// --- ФУНКЦИЯ ИМПОРТА СТРУКТУРЫ ---
function importStructure($pdo, $file, $delimiter) {
    if (!file_exists($file)) return;
    
    echo "<h3>Импорт структуры ($file)...</h3>";
    $handle = fopen($file, "r");
    // Пропускаем первую строку (заголовки)
    fgetcsv($handle, 1000, $delimiter);
    
    $count = 0;
    
    // НАЧАЛО ТРАНЗАКЦИИ
    $pdo->beginTransaction();
    
    try {
        $sql = "INSERT INTO structure (model, cat_id, parent_id, name, image, is_scheme) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE name=VALUES(name), image=VALUES(image), parent_id=VALUES(parent_id), is_scheme=VALUES(is_scheme)";
        $stmt = $pdo->prepare($sql);

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            // Ожидаем: model; cat_id; parent_id; name; image; is_scheme
            $model      = trim($data[0]);
            $cat_id     = trim($data[1]);
            $parent_id  = trim($data[2]);
            $name       = trim($data[3]);
            $image      = trim($data[4]);
            $is_scheme  = (int)trim($data[5]);

            if ($cat_id == '') continue;

            $stmt->execute([$model, $cat_id, $parent_id, $name, $image, $is_scheme]);
            $count++;
        }
        
        // ФИКСАЦИЯ ИЗМЕНЕНИЙ (ЗАПИСЬ В БАЗУ ОДНИМ ПАКЕТОМ)
        $pdo->commit();
        echo "<p style='color:green'>Загружено категорий/схем: <b>$count</b></p>";
        
    } catch (Exception $e) {
        // Если ошибка — отменяем всё, чтобы не поломать базу
        $pdo->rollBack();
        echo "<p style='color:red'>Ошибка при импорте структуры: " . $e->getMessage() . "</p>";
    }

    fclose($handle);
}

// --- ФУНКЦИЯ ИМПОРТА ЗАПЧАСТЕЙ ---
function importParts($pdo, $file, $delimiter) {
    if (!file_exists($file)) return;

    echo "<h3>Импорт запчастей ($file)...</h3>";
    $handle = fopen($file, "r");
    fgetcsv($handle, 1000, $delimiter); // Пропуск заголовка
    
    $count = 0;

    // НАЧАЛО ТРАНЗАКЦИИ
    $pdo->beginTransaction();

    try {
        $sql = "INSERT INTO parts (model, cat_id, pos_code, part_number, qty, name, specs) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            // Ожидаем: model; cat_id; pos_code; part_number; qty; name; specs
            $model       = trim($data[0]);
            $cat_id      = trim($data[1]);
            $pos_code    = trim($data[2]);
            $part_number = trim($data[3]);
            $qty         = trim($data[4]);
            $name        = trim($data[5]);
            $specs       = isset($data[6]) ? trim($data[6]) : '';

            if ($part_number == '') continue;

            $stmt->execute([$model, $cat_id, $pos_code, $part_number, $qty, $name, $specs]);
            $count++;
        }

        // ФИКСАЦИЯ
        $pdo->commit();
        echo "<p style='color:green'>Загружено запчастей: <b>$count</b></p>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p style='color:red'>Ошибка при импорте запчастей: " . $e->getMessage() . "</p>";
    }

    fclose($handle);
}

// --- ЗАПУСК ---
// Очистка таблиц перед импортом для избежания дублей (опционально)
// Будь осторожен: это полностью удаляет старые данные!
$pdo->query("TRUNCATE TABLE structure");
$pdo->query("TRUNCATE TABLE parts");

if (file_exists('structure.csv')) {
    importStructure($pdo, 'structure.csv', $delimiter);
} else {
    echo "<p style='color:red'>Файл structure.csv не найден.</p>";
}

if (file_exists('parts.csv')) {
    importParts($pdo, 'parts.csv', $delimiter);
} else {
    echo "<p style='color:red'>Файл parts.csv не найден.</p>";
}

echo "<hr><a href='catalog.php' style='font-size:20px; font-weight:bold;'>Перейти в каталог</a>";
?>