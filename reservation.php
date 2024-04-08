<?php
session_start();

// 处理登录逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_airport"]) && isset($_POST["login_callsign"]) && isset($_POST["student_id"]) && isset($_POST["instructor_id"]) && isset($_POST["z_time"])) {
    $_SESSION["login_airport"] = $_POST["login_airport"];
    $_SESSION["login_callsign"] = $_POST["login_callsign"];
    $_SESSION["user_id"] = uniqid(); // 生成唯一用户ID

    // 构建预约信息
    $appointment = array(
        "login_airport" => $_POST["login_airport"],
        "login_callsign" => $_POST["login_callsign"],
        "student_id" => $_POST["student_id"],
        "instructor_id" => $_POST["instructor_id"],
        "z_time" => $_POST["z_time"]
    );

    // 读取已有预约信息
    $appointments = [];
    if (file_exists("appointments.json")) {
        $appointments = json_decode(file_get_contents("appointments.json"), true);
    }

    // 添加新预约信息
    $appointments[] = $appointment;

    // 将更新后的预约信息写入文件
    file_put_contents("appointments.json", json_encode($appointments));

    // 提示预约成功
    echo '<script>alert("预约成功");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>预约系统</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h2, h3 {
        text-align: center;
    }
    form {
        margin-top: 20px;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    input[type="text"],
    input[type="datetime-local"],
    input[type="submit"] {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    input[type="submit"] {
        background-color: #4caf50;
        color: white;
        border: none;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #45a049;
    }
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>
<div class="container">
    <h2>预约系统</h2>

    <!-- 登录表单 -->
    <?php if (!isset($_SESSION["login_airport"]) || !isset($_SESSION["login_callsign"])): ?>
        <h3>登录</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="login_airport">上线机场：</label>
            <input type="text" id="login_airport" name="login_airport" required><br><br>
            <label for="login_callsign">上线席位：</label>
            <input type="text" id="login_callsign" name="login_callsign" required><br><br>
            <label for="student_id">学员ID：</label>
            <input type="text" id="student_id" name="student_id" required><br><br>
            <label for="instructor_id">教员ID：</label>
            <input type="text" id="instructor_id" name="instructor_id" required><br><br>
            <label for="z_time">上线时间：</label>
            <input type="datetime-local" id="z_time" name="z_time" required><br><br>
            <input type="submit" value="预约">
        </form>
    <?php else: ?>
        <p>已登录，上线机场：<?php echo $_SESSION["login_airport"]; ?>，上线席位：<?php echo $_SESSION["login_callsign"]; ?></p>
    <?php endif; ?>

    <!-- 预约信息表格 -->
    <h3>预约信息</h3>
    <table id="appointmentTable">
        <thead>
            <tr>
                <th>上线机场</th>
                <th>上线席位</th>
                <th>学员ID</th>
                <th>教员ID</th>
                <th>上线时间</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (file_exists("appointments.json")) {
                $appointments = json_decode(file_get_contents("appointments.json"), true);
                if (!empty($appointments)) { // 检查预约信息是否为空
                    foreach ($appointments as $appointment) {
                        echo "<tr>";
                        echo "<td>{$appointment['login_airport']}</td>";
                        echo "<td>{$appointment['login_callsign']}</td>";
                        echo "<td>{$appointment['student_id']}</td>";
                        echo "<td>{$appointment['instructor_id']}</td>";
                        echo "<td>{$appointment['z_time']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>暂无预约信息</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5'>暂无预约信息</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
