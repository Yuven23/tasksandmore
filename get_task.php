<?php
$tasks = file('tasks.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!empty($tasks)) {
    foreach ($tasks as $task) {
        echo "<li>$task</li>";
    }
}
?>
