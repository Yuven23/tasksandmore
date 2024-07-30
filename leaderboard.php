<?php
include_once 'db_config.php'; // Include your database connection configuration file

try {
    // Query to get total days logged in for each user with user names (All-time)
    $sql_logins_all_time = "SELECT ud.user_name, COUNT(DISTINCT ul.login_date) AS total_days_logged_in
                            FROM user_logins ul
                            JOIN user_details ud ON ul.user_email = ud.email
                            GROUP BY ud.user_name";
    $result_logins_all_time = $conn->query($sql_logins_all_time);

    // Store the result in an associative array
    $logins_data_all_time = [];
    while ($row = $result_logins_all_time->fetch_assoc()) {
        $logins_data_all_time[$row['user_name']] = $row['total_days_logged_in'];
    }

    // Query to get total tasks completed for each user with user names (All-time)
    $sql_tasks_all_time = "SELECT ud.user_name, COUNT(t.task_id) AS total_tasks_completed
                           FROM tasks t
                           JOIN accepted_tasks at ON t.task_id = at.task_id
                           JOIN user_details ud ON at.accepted_by = ud.email
                           WHERE t.completed = 1
                           GROUP BY ud.user_name";
    $result_tasks_all_time = $conn->query($sql_tasks_all_time);

    // Store the result in an associative array
    $tasks_data_all_time = [];
    while ($row = $result_tasks_all_time->fetch_assoc()) {
        $tasks_data_all_time[$row['user_name']] = $row['total_tasks_completed'];
    }

    // Combine the data and prepare the all-time leaderboard
    $leaderboard_all_time = [];
    foreach ($logins_data_all_time as $user_name => $total_days) {
        $total_tasks = isset($tasks_data_all_time[$user_name]) ? $tasks_data_all_time[$user_name] : 0;
        $leaderboard_all_time[] = [
            'user_name' => $user_name,
            'total_days_logged_in' => $total_days,
            'total_tasks_completed' => $total_tasks,
        ];
    }

    // Sort the all-time leaderboard by total_tasks_completed and total_days_logged_in
    usort($leaderboard_all_time, function($a, $b) {
        if ($a['total_tasks_completed'] == $b['total_tasks_completed']) {
            return $b['total_days_logged_in'] - $a['total_days_logged_in'];
        }
        return $b['total_tasks_completed'] - $a['total_tasks_completed'];
    });

    // Query to get total days logged in for each user with user names (Monthly)
    $current_month = date('Y-m');
    $current_month_name = date('F Y'); // Full month name and year
    $sql_logins_monthly = "SELECT ud.user_name, COUNT(DISTINCT ul.login_date) AS total_days_logged_in
                           FROM user_logins ul
                           JOIN user_details ud ON ul.user_email = ud.email
                           WHERE DATE_FORMAT(ul.login_date, '%Y-%m') = '$current_month'
                           GROUP BY ud.user_name";
    $result_logins_monthly = $conn->query($sql_logins_monthly);

    // Store the result in an associative array
    $logins_data_monthly = [];
    while ($row = $result_logins_monthly->fetch_assoc()) {
        $logins_data_monthly[$row['user_name']] = $row['total_days_logged_in'];
    }

    // Query to get total tasks completed for each user with user names (Monthly)
    $sql_tasks_monthly = "SELECT ud.user_name, COUNT(t.task_id) AS total_tasks_completed
                          FROM tasks t
                          JOIN accepted_tasks at ON t.task_id = at.task_id
                          JOIN user_details ud ON at.accepted_by = ud.email
                          WHERE t.completed = 1 AND DATE_FORMAT(t.completed_at, '%Y-%m') = '$current_month'
                          GROUP BY ud.user_name";
    $result_tasks_monthly = $conn->query($sql_tasks_monthly);

    // Store the result in an associative array
    $tasks_data_monthly = [];
    while ($row = $result_tasks_monthly->fetch_assoc()) {
        $tasks_data_monthly[$row['user_name']] = $row['total_tasks_completed'];
    }

    // Combine the data and prepare the monthly leaderboard
    $leaderboard_monthly = [];
    foreach ($logins_data_monthly as $user_name => $total_days) {
        $total_tasks = isset($tasks_data_monthly[$user_name]) ? $tasks_data_monthly[$user_name] : 0;
        $leaderboard_monthly[] = [
            'user_name' => $user_name,
            'total_days_logged_in' => $total_days,
            'total_tasks_completed' => $total_tasks,
        ];
    }

    // Sort the monthly leaderboard by total_tasks_completed and total_days_logged_in
    usort($leaderboard_monthly, function($a, $b) {
        if ($a['total_tasks_completed'] == $b['total_tasks_completed']) {
            return $b['total_days_logged_in'] - $a['total_days_logged_in'];
        }
        return $b['total_tasks_completed'] - $a['total_tasks_completed'];
    });

    // Display the leaderboards
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Leaderboard</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('recbgnd2.jpeg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                color: #333;
                margin: 0;
                padding: 0;
            }
            
            .container {
                width: 90%;
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                background-color: rgba(255, 255, 255, 0.8);
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            h1 {
                text-align: center;
                color: #333;
            }
            .btn-container {
                text-align: center;
                margin-bottom: 20px;
            }
            .btn-container button {
                background-color: #000; /* Black background */
                color: white;
                border: none;
                padding: 10px 20px;
                text-align: center;
                font-size: 16px;
                border-radius: 8px;
                cursor: pointer;
                margin: 0 10px;
            }
            .btn-container button:hover {
                background-color: #333; /* Dark gray for hover effect */
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th {
                background-color: #000; /* Black background for column headers */
                color: white; /* White text color for contrast */
                padding: 12px;
                text-align: left;
            }
            td {
                padding: 12px;
                text-align: left;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #ddd;
            }
            @media screen and (max-width: 768px) {
                table {
                    font-size: 14px;
                }
            }
        </style>
        <script>
            function showLeaderboard(type) {
                document.getElementById('allTimeLeaderboard').style.display = type === 'allTime' ? 'block' : 'none';
                document.getElementById('monthlyLeaderboard').style.display = type === 'monthly' ? 'block' : 'none';
            }
        </script>
    </head>
    <body onload=\"showLeaderboard('allTime')\">
        <div class='container'>
            <h1>Leaderboard</h1>
            <div class='btn-container'>
                <button onclick=\"showLeaderboard('allTime')\">All Time</button>
                <button onclick=\"showLeaderboard('monthly')\">Monthly</button>
            </div>
            <div id='allTimeLeaderboard'>
                <h2>All Time Leaderboard</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Total Days Logged In</th>
                            <th>Total Tasks Completed</th>
                        </tr>
                    </thead>
                    <tbody>";

    foreach ($leaderboard_all_time as $user) {
        echo "<tr>
                <td>{$user['user_name']}</td>
                <td>{$user['total_days_logged_in']}</td>
                <td>{$user['total_tasks_completed']}</td>
              </tr>";
    }

    echo "          </tbody>
                </table>
            </div>
            <div id='monthlyLeaderboard' style='display: none;'>
                <h2>Monthly Leaderboard for {$current_month_name}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Total Days Logged In</th>
                            <th>Total Tasks Completed</th>
                        </tr>
                    </thead>
                    <tbody>";

    foreach ($leaderboard_monthly as $user) {
        echo "<tr>
                <td>{$user['user_name']}</td>
                <td>{$user['total_days_logged_in']}</td>
                <td>{$user['total_tasks_completed']}</td>
              </tr>";
    }

    echo "          </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>";

} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
