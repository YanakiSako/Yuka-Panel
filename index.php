<!DOCTYPE html>
<html>
<head>
    <title>注册页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            box-sizing: border-box;
            border: none;
            border-radius: 3px;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: #f00;
            text-align: center;
        }
        .notice {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>注册页面</h2>
        <form id="registrationForm" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="text" name="username" placeholder="用户名"><br>
            <input type="password" name="password" placeholder="密码"><br>
            <input type="submit" value="注册">
        </form>
    </div>

    <div class="notice">
        <h3>公告栏</h3>
        <?php
        // 读取公告文件
        $motdFile = "motd.txt";
        if (file_exists($motdFile)) {
            $motdContent = file_get_contents($motdFile);
            echo nl2br($motdContent); // 将换行符转换为 <br> 标签
        } else {
            echo "暂无公告。";
        }
        ?>
    </div>

    <script>
        document.getElementById("registrationForm").onsubmit = function() {
            var username = document.getElementsByName("username")[0].value.trim();
            var password = document.getElementsByName("password")[0].value.trim();
            if (username === "" || password === "") {
                alert("错误：用户名和密码不能为空。");
                return false;
            }
            return true;
        };
    </script>
</body>
</html>

<?php
// 检查是否有提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取用户输入的用户名和密码
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // 打开文件以读取用户信息
    $filename = "cert.txt";
    $file = fopen($filename, "r") or die("无法打开文件！");
    
    // 检查是否存在相同账号
    $userExists = false;
    while (!feof($file)) {
        $line = fgets($file);
        $userInfo = explode(" ", $line);
        
        // 如果用户名已存在，则设置标志为真
        if ($userInfo[0] == $username) {
            $userExists = true;
            break;
        }
    }
    
    // 关闭文件
    fclose($file);
    
    // 如果存在相同账号，则设置错误消息
    if ($userExists) {
        echo '<script>alert("错误：该用户名已被注册。");</script>';
    } else {
        // 如果不存在相同账号，则将用户名、密码写入文件
        $file = fopen($filename, "a") or die("无法打开文件！");
        fwrite($file, $username . " " . $password . " 5\n");
        fclose($file);
        echo '<script>alert("注册成功！您的权限为 Controller1。");</script>';
    }
}
?>
