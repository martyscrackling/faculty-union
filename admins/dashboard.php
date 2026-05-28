<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once('../class/database.php');
require_once('sidebar.php');
$navtext = "Dashboard";
require_once('navbar.php');
$database = new Database();
$db = $database->getConnection();

// Quick Stats
$count_officers = $db->query("SELECT COUNT(*) FROM officers")->fetchColumn();
$count_objectives = $db->query("SELECT DISTINCT COUNT(content) FROM objectives")->fetchColumn();

date_default_timezone_set("Asia/Manila"); 
        $hour = date("H");
        $greeting = "Hello";

        if ($hour >= 5 && $hour < 12) {
            $greeting = "Good Morning, Admin!";
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting = "Good Afternoon, Admin!";
        } elseif ($hour >= 18 && $hour < 22) {
            $greeting = "Good Evening, Admin!";
        } else {
            $greeting = "Good Night, Admin!";
        }

        $today = date("Y-m-d");
        $current_date = date("l, F j, Y");
        
        $current_year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");
        $current_month = isset($_GET['month']) ? intval($_GET['month']) : date("n");
        
        if ($current_month < 1) {
            $current_month = 12;
            $current_year--;
        }
        if ($current_month > 12) {
            $current_month = 1;
            $current_year++;
        }
        
        $prev_month = $current_month - 1;
        $prev_year = $current_year;
        if ($prev_month < 1) {
            $prev_month = 12;
            $prev_year--;
        }
        
        $next_month = $current_month + 1;
        $next_year = $current_year;
        if ($next_month > 12) {
            $next_month = 1;
            $next_year++;
        }
        
        $month_name = date("F", mktime(0, 0, 0, $current_month, 1, $current_year));
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - WMSU FU</title>
    <link rel="icon" href="../images/facultyunion.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6e1414; /* maroon */
            --secondary: #4f0f0f; /* darker maroon */
            --accent: #8c1d1d;
            --light: #fff7f7;
            --today: #b71c1c;
            --gray-light: #f0e5e5;
            --shadow: rgba(139, 23, 23, 0.12);
            --text-dark: #3b0b0b;
        }

        .main-content { margin-left: 250px; padding: 30px; }
        .card-stat { border-left: 5px solid var(--accent); }
        .header-logo { width: 100px; height: 100px; object-fit: contain; display: block; filter: drop-shadow(0 10px 18px rgba(0,0,0,.35)); }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.2rem 1.4rem;
            border-radius: 12px;
            box-shadow: 0 6px 22px var(--shadow);
            margin-bottom: 1.25rem;
        }

        .greeting { font-size: 1.6rem; font-weight:700; margin-bottom:4px; }
        .date-display { font-size: 0.95rem; opacity: 0.95; }

        .clock {
            font-size: 2rem;
            font-weight:700;
            padding: 0.45rem 1rem;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            box-shadow: inset 0 -2px 0 rgba(0,0,0,0.06);
        }

        .calendar-wrapper {
            background-color: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 6px 22px rgba(0,0,0,0.06);
            margin-top: 1.25rem;
        }

        .month-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .month-title { font-size: 1.25rem; font-weight:700; color:var(--text-dark); display:flex; gap:0.5rem; align-items:center; }

        .month-navigation { display:flex; align-items:center; gap:0.75rem; }
        .month-selector { padding:0.45rem 0.6rem; border:1px solid var(--gray-light); border-radius:6px; font-size:0.95rem; }

        .nav-btn {
            background-color: var(--gray-light);
            border: none;
            color: var(--primary);
            font-size: 1.05rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer;
            transition: all .15s ease;
        }
        .nav-btn:hover { background: var(--primary); color:#fff; transform: translateY(-2px); }

        .calendar { display:grid; grid-template-columns: repeat(7, 1fr); gap:0.5rem; }
        .day-name { text-align:center; font-weight:700; padding:0.6rem 0; background:var(--gray-light); border-radius:8px; font-size:0.9rem; }
        .day { text-align:center; padding:0.9rem 0; border-radius:8px; cursor:pointer; transition: all .12s ease; }
        .day:hover { transform:translateY(-4px); background: #fff4f4; }
        .other-month { color:#b7b7b7; }
        .today { border:4px solid var(--today); color:var(--today); font-weight:700; background:transparent; }

        .no-underline { text-decoration:none !important; }

        .homelink { text-decoration:none; color: #fff; transition: .3s ease; }
        .homelink:hover { color: #ffecec; }

        @media (max-width:767px){ .main-content{ margin-left:0; padding:16px; } .month-header{flex-direction:column; align-items:flex-start;} .month-navigation{margin-top:8px;} .clock{width:100%;} }
    </style>
</head>
<body>


<div class="main-content">
    <div class="content-page px-3">
        
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="rounded p-4 text-start mt-4">
            <div class="header">
        <div class="greeting-section">
            <div class="greeting"><?php echo $greeting; ?></div>
            <div class="date-display"><?php echo $current_date; ?></div>
            <div class="home mt-3">
                <h6><a href="../dashboard/descd.php" class="homelink">Go to homepage</a></h6>
            </div>
        </div>
        
        <div class="header-right d-flex align-items-center">
           
            <div class="ml-3">
                <div class="clock" id="clock"></div>
            </div>
        </div>
    </div>
    <div class="calendar-wrapper">
        <div class="month-header">
            <div class="month-title">
                <?php echo $month_name . " " . $current_year; ?>
            </div>
            <div class="month-navigation">
                <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="nav-btn no-underline">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <select class="month-selector" id="monthSelector" onchange="changeMonth()">
                    <?php
                        for ($m = 1; $m <= 12; $m++) {
                            $month_text = date("F", mktime(0, 0, 0, $m, 1));
                            $selected = ($m == $current_month) ? "selected" : "";
                            echo "<option value='$m' $selected>$month_text</option>";
                        }
                    ?>
                </select>
                
                <select class="month-selector" id="yearSelector" onchange="changeMonth()">
                    <?php
                        $start_year = date("Y") - 5;
                        $end_year = date("Y") + 5;
                        for ($y = $start_year; $y <= $end_year; $y++) {
                            $selected = ($y == $current_year) ? "selected" : "";
                            echo "<option value='$y' $selected>$y</option>";
                        }
                    ?>
                </select>
                
                <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="nav-btn no-underline">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>
        </div>
        <div class="calendar">
            <?php
                $days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                foreach ($days as $day) {
                    echo "<div class='day-name'>$day</div>";
                }

                // First day of the month
                $first_day_of_month = date("w", mktime(0, 0, 0, $current_month, 1, $current_year));
                
                // Number of days in the month
                $days_in_month = date("t", mktime(0, 0, 0, $current_month, 1, $current_year));
                
                // Days from previous month
                if ($first_day_of_month > 0) {
                    $prev_month_days = date("t", mktime(0, 0, 0, $prev_month, 1, $prev_year));
                    for ($i = $first_day_of_month - 1; $i >= 0; $i--) {
                        $day_num = $prev_month_days - $i;
                        echo "<div class='day other-month'>$day_num</div>";
                    }
                }

                // Days of current month
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $date = sprintf("%04d-%02d-%02d", $current_year, $current_month, $day);
                    $class = ($date === $today) ? "day today" : "day";
                    echo "<div class='$class'>$day</div>";
                }
                
                // Days from next month
                $used_cells = $first_day_of_month + $days_in_month;
                $remaining_cells = 7 - ($used_cells % 7);
                if ($remaining_cells < 7) {
                    for ($day = 1; $day <= $remaining_cells; $day++) {
                        echo "<div class='day other-month'>$day</div>";
                    }
                }
            ?>
        </div>
    </div>
            </div>
            </div>
        </div>
    </div>
</div>
    
    <div class="row d-md-none">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Officers</h6>
                        <h3 style="color: black;"><?php echo $count_officers; ?></h3s>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Objectives</h6>
                        <h3><?php echo $count_objectives; ?></h3>
                    </div>
                    <i class="fas fa-bullseye fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        function updateClockAndGreeting() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

            const timeString = `${(hours % 12 || 12)}:${minutes}:${seconds} ${hours >= 12 ? 'PM' : 'AM'}`;
            const clockEl = document.getElementById("clock");
            if (clockEl) clockEl.innerText = timeString;

            const greetingEl = document.querySelector(".greeting");
            const dateDisplayEl = document.querySelector(".date-display");
            if (greetingEl && dateDisplayEl) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const currentDateStr = now.toLocaleDateString('en-US', options);

                let greeting = "Hello";
                if (hours >= 5 && hours < 12) {
                    greeting = "Good Morning, Admin!";
                } else if (hours >= 12 && hours < 18) {
                    greeting = "Good Afternoon, Admin!";
                } else if (hours >= 18 && hours < 22) {
                    greeting = "Good Evening, Admin!";
                } else {
                    greeting = "Good Night, Admin!";
                }

                greetingEl.textContent = greeting;
                dateDisplayEl.textContent = currentDateStr;
            }
        }

        function changeMonth() {
            const month = document.getElementById("monthSelector").value;
            const year = document.getElementById("yearSelector").value;
            window.location.href = `?month=${month}&year=${year}`;
        }

        setInterval(updateClockAndGreeting, 1000);
        updateClockAndGreeting();
    </script>

</div>

</body>
</html>