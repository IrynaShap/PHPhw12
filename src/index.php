<?php
require 'classes/TaskManager.php';
const DATA_PATH = __DIR__ . "/../data/";
/**
 * @return string
 */
function getStringInput(): string
{
	return trim(fgets(STDIN));
}

$fileName = $taskManager = null;

while (true) {
	echo "\n-----------------------------\n";
	if ($fileName) {
		echo "Поточний файл: $fileName\n";
		echo "Список доступних опцій:\n";
		echo "1. Додати завдання\n";
		echo "2. Видалити завдання\n";
		echo "3. Переглянути завдання\n";
		echo "4. Виконати завдання\n";
		echo "5. Зберегти і вибрати інший файл\n";
		echo "6. Зберегти і вийти\n";
		echo "Введіть свій вибір: ";
		$choice = getStringInput();
	} else {
		$choice = '5';
	}
	try {
		switch ($choice) {
			case '1':
				echo "\nВведіть назву завдання: ";
				$taskName = getStringInput();
				echo "Введіть пріоритет завдання від 1 до 10: ";
				$priority = getStringInput();
				if (!is_numeric($priority)) {
					throw new RuntimeException("Пріоритет повинен бути цілим числом від 1 до 10.");
				}
				$taskManager->addTask($taskName, (int)$priority);
				echo "Завдання успішно додано.\n";
				break;
			case '2':
				echo "\nВведіть ID завдання для видалення: ";
				$taskId = getStringInput();
				$taskManager->deleteTask($taskId);
				echo "Завдання успішно видалено.\n";
				break;
			case '3':
				echo "\nСписок завдань:\n";
				foreach ($taskManager->getTasks() as $id => $task) {
					echo "ID: $id, Назва: {$task['name']}, Пріоритет: {$task['priority']}, Статус: {$task['status']->value}\n";
				}
				break;
			case '4':
				echo "\nВведіть ID завдання для виконання: ";
				$taskId = getStringInput();
				$taskManager->completeTask($taskId);
				echo "Завдання успішно виконано.\n";
				break;
			case '5':
				if ($taskManager) {
					$taskManager->saveTasks();
				}
				echo "Введіть ім'я файлу для роботи (якщо такого файлу немає, буде створено новий файл): ";
				$fileName = basename(getStringInput());
				$taskManager = new TaskManager(DATA_PATH . $fileName);
				break;
			case '6':
				echo "\nДякуємо за використання нашої системи. До побачення!\n";
				exit(0);
			default:
				echo "\nНеправильний вибір. Будь ласка, спробуйте ще раз.\n";
		}
	} catch (Exception $e) {
		echo "\nПомилка: " . $e->getMessage() . "\n";
	}
}