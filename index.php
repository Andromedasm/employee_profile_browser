<?php
ob_start();
if (version_compare(PHP_VERSION, '7.4', '<')) {
    die('Error: PHP version 7.4 or higher is required to run this script.');
}

session_start();
// Generate CSRF token
if (empty($_SESSION['csrf_token'])) { // 如果 CSRF 令牌不存在
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // 生成 CSRF 令牌
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Employee Profile Login Portal">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (/MSIE|Trident/.test(window.navigator.userAgent)) {
            // 重定向到不支持的页面或显示不支持的消息
            window.location.href = '404.php';
        }
    </script>
</head>
<body class="bg-gray-200">
<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-center mb-4">
            <img src="img/company_logo.png" alt="Company Logo" class="w-32 h-32">
        </div>
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-4">社員閲覧Login</h2>
        <form action="inc/authenticate.php?csrf_token=<?php echo urlencode($_SESSION['csrf_token']); ?>" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700">メールアドレス</label>
                <div class="flex items-center border border-gray-300 rounded-lg">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-6 w-6 mx-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                    </svg>
                    <input type="email" id="email" name="email" placeholder="email" aria-label="Email"
                           autocomplete="email"
                           class="w-full py-2 pt-2 text-gray-700 focus:outline-none focus:border-blue-500"
                           required>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">パスワード</label>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="flex items-center border border-gray-300 rounded-lg">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-6 w-6 mx-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path>
                    </svg>
                    <input type="password" id="password" name="password" placeholder="Password" aria-label="Password"
                           autocomplete="current-password"
                           class="w-full py-2 pt-2 text-gray-700 focus:outline-none focus:border-blue-500"
                           required>
                </div>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <input type="checkbox" id="remember_me" name="remember_me" class="mr-2">
                    <label for="remember_me" class="text-gray-700">ログイン状態を保存</label>
                </div>
                <!-- Uncomment the line below if you want to add a "Forgot Password" link -->
                <!-- <a href="#" class="text-sm text-blue-500 hover:text-blue-700">Forgot Password?</a> -->
            </div>
            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none">
                ログイン
            </button>
            <input type="hidden" name="login_attempts"
                   value="<?php echo isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0; ?>">
            <?php if (isset($_SESSION['error_message'])): // 如果会话中存在错误消息 ?>
                <div class="text-red-500 mb-4">
                    <?php
                    echo htmlspecialchars($_SESSION['error_message']); // 显示错误消息
                    unset($_SESSION['error_message']); // 删除错误消息
                    ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

</div>
<script>
    document.querySelector('form').addEventListener('submit', (e) => {
        // 验证 CSRF 令牌
        const inputCsrfToken = document.getElementsByName('csrf_token')[0].value; // 获取隐藏的 CSRF 令牌
        const sessionCsrfToken = '<?php echo $_SESSION['csrf_token']; ?>'; // 获取会话中的 CSRF 令牌
        if (inputCsrfToken !== sessionCsrfToken) { // 如果两个令牌不匹配
            e.preventDefault(); // 阻止表单提交
            alert('Invalid CSRF token. Please refresh the page and try again.'); // 显示错误消息
        }
    });
</script>
</body>
</html>