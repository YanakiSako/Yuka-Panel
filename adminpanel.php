<?php
session_start();

// 处理登录逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $filename = "cert.txt";
    $file = fopen($filename, "r") or die("无法打开文件！");
    $validUser = false;
    while (!feof($file)) {
        $line = fgets($file);
        $userInfo = explode(" ", $line);
        if ($userInfo[0] == $username) {
            $validUser = true;
            if ($userInfo[1] == $password) {
                $_SESSION["username"] = $userInfo[0];
                $_SESSION["password"] = $userInfo[1];
                $_SESSION["permission"] = $userInfo[2];
                // 记录登录日志
                $log = fopen("log.txt", "a") or die("无法打开日志文件！");
                $logContent = date("Y-m-d H:i:s") . " 登录成功：" . $_SESSION["username"] . "，IP地址：" . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
                fwrite($log, $logContent);
                fclose($log);
                header("Location: adminpanel.php");
                exit();
            } else {
                echo '<script>alert("密码错误");</script>';
            }
            break;
        }
    }
    if (!$validUser) {
        echo '<script>alert("账号不存在");</script>';
    }
    fclose($file);
}

// 处理修改密码的逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"])) {
    $newPassword = $_POST["new_password"];
    // 这里可以添加密码验证逻辑，例如：长度、复杂度等
    // 更新密码
    $_SESSION["password"] = $newPassword;
    // 记录修改密码日志
    $log = fopen("log.txt", "a") or die("无法打开日志文件！");
    $logContent = date("Y-m-d H:i:s") . " 修改密码成功：" . $_SESSION["username"] . PHP_EOL;
    fwrite($log, $logContent);
    fclose($log);
    // 提示密码修改成功
    echo '<script>alert("密码修改成功");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>用户管理面板</title>
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
    h2 {
        text-align: center;
    }
    form {
        margin-top: 20px;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    input[type="text"],
    input[type="password"],
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
    <h2>用户管理面板</h2>
    <?php
    // 显示欢迎消息和修改密码表单
    if (isset($_SESSION["username"])) {
        echo "<p>欢迎，{$_SESSION["username"]}！</p>";
        echo "<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\">";
        echo "<input type=\"password\" name=\"new_password\" placeholder=\"输入新密码\" required><br>";
        echo "<input type=\"submit\" value=\"修改密码\">";
        echo "</form>";

        // 如果权限是13，则显示其他用户信息，并可以为其他人修改账号密码
        if ($_SESSION["permission"] == 13) {
            echo "<h3>其他用户信息</h3>";
            echo "<table>";
            echo "<tr><th>用户名</th><th>密码</th><th>权限</th><th>操作</th></tr>";
            $file = fopen("cert.txt", "r");
            while (!feof($file)) {
                $line = fgets($file);
                if (!empty($line)) {
                    $userInfo = explode(" ", $line);
                    echo "<tr>";
                    echo "<td>{$userInfo[0]}</td>";
                    echo "<td>{$userInfo[1]}</td>";
                    echo "<td>{$userInfo[2]}</td>";
                    echo "<td><a href=\"{$_SERVER["PHP_SELF"]}?edit={$userInfo[0]}\">编辑</a></td>";
                    echo "</tr>";
                }
            }
            fclose($file);
            echo "</table>";
        }
    } else {
        // 显示登录表单
        echo "<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\">";
        echo "<input type=\"text\" name=\"username\" placeholder=\"用户名\" required><br>";
        echo "<input type=\"password\" name=\"password\" placeholder=\"密码\" required><br>";
        echo "<input type=\"submit\" value=\"登录\">";
        echo "</form>";
    }

    // 编辑用户信息页面
    if (isset($_GET["edit"])) {
        $editUser = $_GET["edit"];
        echo "<h3>编辑用户信息</h3>";
        echo "<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"edit_username\" value=\"$editUser\">";
        echo "<input type=\"password\" name=\"edit_password\" placeholder=\"输入新密码\" required><br>";
        echo "<input type=\"number\" name=\"edit_permission\" placeholder=\"输入新权限\" required><br>";
        echo "<input type=\"submit\" value=\"保存\">";
        echo "</form>";
    }

    // 处理编辑用户信息逻辑
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_username"])) {
        $editUsername = $_POST["edit_username"];
        $editPassword = $_POST["edit_password"];
        $editPermission = $_POST["edit_permission"];
        // 更新用户信息
        $filename = "cert.txt";
        $fileContent = file_get_contents($filename);
        $fileLines = explode("\n", $fileContent);
        $newFileContent = "";
        foreach ($fileLines as $line) {
            $userInfo = explode(" ", $line);
            if ($userInfo[0] == $editUsername) {
                $userInfo[1] = $editPassword;
                $userInfo[2] = $editPermission;
                $newLine = implode(" ", $userInfo);
                $newFileContent .= $newLine . "\n";
            } else {
                $newFileContent .= $line . "\n";
            }
        }
        file_put_contents($filename, rtrim($newFileContent));
        // 记录修改用户信息日志
        $log = fopen("log.txt", "a") or die("无法打开日志文件！");
        $logContent = date("Y-m-d H:i:s") . " 修改用户信息成功：" . $_SESSION["username"] . " 修改了 $editUsername 的信息" . PHP_EOL;
        fwrite($log, $logContent);
        fclose($log);
        // 提示用户信息修改成功
        echo '<script>alert("用户信息修改成功");</script>';
        // 重定向到当前页面以清除 URL 中的编辑参数
        header("Location: {$_SERVER["PHP_SELF"]}");
        exit();
    }
    ?>
</div>
</body>
</html>
