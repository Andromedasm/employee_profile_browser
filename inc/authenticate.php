<?php
ob_start();
session_start(); // 启动会话
require 'conn.php'; // 引入数据库连接文件

// 设置安全的 cookie 属性
//ini_set('session.cookie_secure', 1); // 只在 HTTPS 连接下发送 cookie
//ini_set('session.cookie_httponly', 1); // 阻止 JavaScript 访问 cookie
//ini_set('session.cookie_domain', $_SERVER['SERVER_NAME']); // 指定 cookie 的域
//ini_set('session.cookie_path', '/'); // 指定 cookie 的路径

// 启用同源策略和 CSP
header("X-Content-Type-Options: nosniff"); // 防止 MIME 类型混淆攻击
header("X-Frame-Options: SAMEORIGIN"); // 防止点击劫持攻击
header("X-XSS-Protection: 1; mode=block"); // 防止 XSS 攻击
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self'; frame-src 'none';"); // 启用 CSP
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload"); // 启用 HSTS

// 错误处理与重定向函数
function redirectWithError($error_message, $status_code = 401)
{
    http_response_code($status_code);
    $_SESSION['error_message'] = htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    header("Location: ../index.php");
    exit;
}

// 检查 "记住我" 功能
function checkRememberMe($connection)
{
    // 检查记住我 cookie 是否存在
    if (isset($_COOKIE['remember_me'])) {
        list($user_id, $token) = explode(':', $_COOKIE['remember_me']);

        // 在数据库中查询令牌
        $query = "SELECT * FROM remember_me_tokens WHERE user_id = :user_id AND token = :token";
        $statement = $connection->prepare($query);
        $statement->execute([
            ':user_id' => $user_id,
            ':token' => hash('sha256', $token),
        ]);

        // 如果找到匹配的令牌
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // 验证令牌是否过期
            if (strtotime($row['expires']) > time()) {
                // 通过 user_id 查询用户信息并将其存储在 session 中
                $query = "SELECT * FROM employee_viewers WHERE id = :user_id";
                $stmt = $connection->prepare($query);
                $stmt->execute([':user_id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['company'] = $user['company'];

                // 生成新的会话 ID 来避免会话固定攻击
                session_regenerate_id(true);

                // 获取用户的分支信息
                $query = "SELECT branch FROM user_branches WHERE user_id = :user_id";
                $stmt = $connection->prepare($query);
                $stmt->execute([':user_id' => $user['id']]);
                $userBranches = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $_SESSION['branch'] = $userBranches;

                // 根据用户角色进行重定向
                if ($user['role'] === 'admin') {
                    header("Location: ../dashboard.php");
                } else {
                    header("Location: ../storeBranch.php");
                }
                exit;
            }
        }
    }
}

// 此函数用于验证密码的复杂性(if needed)
/*function validatePasswordComplexity($password) {
    // 密码必须包含至少一个大写字母，一个小写字母，一个数字，一个特殊字符，且长度在8至16之间
    $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/";

    if (!preg_match($password_regex, $password)) {
        redirectWithError("密码必须包含至少一个大写字母、一个小写字母、一个数字、一个特殊字符，并且长度在8至16之间。");
    }
}*/

function authenticate($email, $password, $connection)
{
    // 在数据库中查询用户
    $query = "SELECT * FROM employee_viewers WHERE email = :email";
    $statement = $connection->prepare($query);
    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->execute();

    // 如果查询到了用户
    if (($row = $statement->fetch(PDO::FETCH_ASSOC)) && password_verify($password, $row['password'])) {
        // 验证密码的复杂性(if needed)
//        validatePasswordComplexity($password);
        // 更新 session ID
        session_regenerate_id(true);

        // 将用户信息保存到会话中
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['company'] = $row['company'];

        // 将用户代理保存到 session 中
        $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

        // 获取用户的分支信息
        $query = "SELECT branch FROM user_branches WHERE user_id = :user_id";
        $stmt = $connection->prepare($query);
        $stmt->execute([':user_id' => $row['id']]);
        $userBranches = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $_SESSION['branch'] = $userBranches;
        if ($row['role'] === 'admin') {
            header("Location: ../dashboard.php");
        } else {
            header("Location: ../storeBranch.php");
        }
        exit;
    }

    return "Eメールまたはパスワードが無効。";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查 CSRF 令牌
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        redirectWithError("無効なCSRFトークン。");
    }
    // 获取用户输入的邮箱和密码
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $input_password = trim($_POST['password']); // 获取用户输入的密码（未经哈希处理）


// 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithError("無効なメール形式。");
    }

// 检查是否存在登录尝试次数
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

// 最大登录尝试次数
    $max_login_attempts = 3;

// 登录尝试锁定时间（以秒为单位）
    $lockout_time = 900; // 15 分钟

// 如果超过最大尝试次数
    if ($_SESSION['login_attempts'] >= $max_login_attempts) {
        // 检查锁定时间是否已过
        if (time() - $_SESSION['last_attempt_time'] > $lockout_time) {
            // 重置尝试次数和时间
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = null;
        } else {
            // 设置错误消息并重定向
            $remaining_minutes = ceil(($lockout_time - (time() - $_SESSION['last_attempt_time'])) / 60);
            $error_message = 'Too many failed login attempts. Please try again in ' . $remaining_minutes . ' minutes.';
            redirectWithError($error_message);
        }
    }

// 验证用户输入的用户名和密码（此处使用您现有的验证逻辑）
    $auth_result = authenticate($email, $input_password, $connection);
    if ($auth_result === true) {
        // 重置尝试次数和时间
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = null;

        // 如果用户选择了 "ログイン状態を保存"
        if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on') {
            // 生成一个随机令牌
            $token = bin2hex(random_bytes(32));

            // 使用 Argon2ID 哈希令牌
            $hash_options = [
                'memory_cost' => 1 << 17, // 128MB
                'time_cost' => 4,
                'threads' => 2,
            ];
            $hashed_token = password_hash($token, PASSWORD_ARGON2ID, $hash_options);

            // 保存令牌到数据库中
            $query = "INSERT INTO remember_me_tokens (user_id, token, expires) VALUES (:user_id, :token, :expires)";
            $statement = $connection->prepare($query);
            $statement->execute([
                ':user_id' => $_SESSION['user_id'],
                ':token' => $hashed_token,
                ':expires' => date('Y-m-d H:i:s', strtotime('+30 days')),
            ]);

            // 保存令牌到 cookie 中
            setcookie('remember_me', $_SESSION['user_id'] . ':' . $token,
                [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    //'domain' => $_SERVER['SERVER_NAME'], // add the domain
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict', // add the samesite attribute
                ]
            );
        }
    } else {
        // 更新尝试次数和时间
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();

        // 设置错误消息并重定向
        $remaining_attempts = $max_login_attempts - $_SESSION['login_attempts'];
        $error_message = 'Invalid email or password. You have ' . $remaining_attempts . ' login attempts remaining.';
        redirectWithError($error_message);
    }
} else {
    // 如果用户已经登陆，不需要再次登录
    if (isset($_SESSION['user_id'])) {
        // 验证用户代理（有BUG，注释掉）
        /*if (isset($_SESSION['HTTP_USER_AGENT']) && $_SESSION['HTTP_USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_destroy();
            header("Location: ../index.php");
            exit();
        }*/
        header("Location: ../dashboard.php");
        exit;
    }
    // 如果用户已经设置了 "remember_me" cookie，尝试自动登录
    checkRememberMe($connection);
}

// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800); // 设置 session 生命周期为 1800 秒（30分钟）


