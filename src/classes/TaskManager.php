<?php
require_once 'TaskStatus.php';

class TaskManager
{
	/**
	 * @var string
	 */
	private const TASK_NOT_FOUND = 'Завдання не знайдено';
	/**    /**
	 * @var string
	 */
	private string $tasksFile;
	/**
	 * @var array
	 */
	private array $tasks;

	/**
	 * @param string $fileName
	 */
	public function __construct(string $fileName)
	{
		$this->tasksFile = $fileName;
		$this->loadTasks();
	}

	/**
	 * @return void
	 */
	private function loadTasks(): void
	{
		if (file_exists($this->tasksFile)) {
			$tasks = json_decode(file_get_contents($this->tasksFile), true) ?: [];
			foreach ($tasks as $taskId => $task) {
				$tasks[$taskId]['status'] = TaskStatus::tryFrom($task['status']) ?? TaskStatus::INCOMPLETE;
			}
			$this->tasks = $tasks;
		} else {
			$this->tasks = [];
		}
	}

	/**
	 * @return bool
	 */
	public function saveTasks(): bool
	{
		return file_put_contents($this->tasksFile, json_encode($this->tasks)) !== false;
	}

	/**
	 * @param string $taskName
	 * @param int $priority
	 * @return void
	 */
	public function addTask(string $taskName, int $priority): void
	{
		if ($priority < 1 || $priority > 10) {
			throw new InvalidArgumentException('Пріоритет повинен бути від 1 до 10.');
		}
		$taskId = uniqid();
		$this->tasks[$taskId] = [
			'name' => $taskName,
			'priority' => $priority,
			'status' => TaskStatus::INCOMPLETE
		];
	}

	/**
	 * @param string $taskId
	 * @return bool
	 */
	public function deleteTask(string $taskId): bool
	{
		if (isset($this->tasks[$taskId])) {
			unset($this->tasks[$taskId]);
			return true;
		}
		throw new RuntimeException(self::TASK_NOT_FOUND);
	}

	/**
	 * @return array
	 */
	public function getTasks(): array
	{
		uasort($this->tasks, static function (array $a, array $b) {
			return $b['priority'] - $a['priority'];
		});
		return $this->tasks;
	}

	/**
	 * @param string $taskId
	 * @return bool
	 */
	public function completeTask(string $taskId): bool
	{
		if (isset($this->tasks[$taskId])) {
			$this->tasks[$taskId]['status'] = TaskStatus::COMPLETE;
			return true;
		}
		throw new RuntimeException(self::TASK_NOT_FOUND);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->saveTasks();
	}
}
