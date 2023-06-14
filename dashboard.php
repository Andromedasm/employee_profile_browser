<?php
// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800); // 设置 session 生命周期为 1800 秒（30 分钟）

session_start(); // 启动会话

if (!isset($_SESSION['user_id'])) { // 如果用户未登录
    header('Location: index.php'); // 重定向到首页
    exit();
}

if ($_SESSION['role'] !== 'admin') { // 如果用户角色不是 admin
    header('Location: index.php'); // 重定向到 index.php 页面
    exit();
}

$current_date = date('Y-m-d'); // 获取当前日期
$weekday = date('l'); // 获取当前星期几

// 用日本的曜日表示
$jp_weekday = [
    'Monday' => '月曜日',
    'Tuesday' => '火曜日',
    'Wednesday' => '水曜日',
    'Thursday' => '木曜日',
    'Friday' => '金曜日',
    'Saturday' => '土曜日',
    'Sunday' => '日曜日',
];

$page_title = 'Dashboard';
require 'header.php';
?>

<div class="text-center mt-8 w-full ml-0 transition-all duration-300" id="main-content">

    <main class="p-4">
        <div class="text-center">
            <h1 class="text-4xl font-semibold text-indigo-600 mb-6">
                ようこそ, <?php echo htmlspecialchars($_SESSION['name'] ?: $_SESSION['email']); ?>!</h1>
            <p class="text-lg font-medium text-gray-700">今日は <span
                        class="font-bold"><?php echo $current_date; ?></span>、<?php echo $jp_weekday[$weekday]; ?>。</p>
            <div class="flex items-center justify-center text-center">
                <div class="border-8 border-yellow-100 text-9xl sm:text-7xl font-mono grid grid-cols-2 gap-x-px shadow-2xl rounded text-white">
                    <div class="relative p-8 flex items-center justify-center">
                        <div class="absolute inset-0 grid grid-rows-2">
                            <div class="bg-gradient-to-br from-gray-800 to-black"></div>
                            <div class="bg-gradient-to-br from-gray-700 to-black"></div>
                        </div>
                        <span class="relative" id="unique-hours">05</span>
                        <div class="absolute inset-0 flex items-center">
                            <div class="h-px w-full bg-black"></div>
                        </div>
                    </div>
                    <div class="relative p-8 flex items-center justify-center">
                        <div class="absolute inset-0 grid grid-rows-2">
                            <div class="bg-gradient-to-br from-gray-800 to-black"></div>
                            <div class="bg-gradient-to-br from-gray-700 to-black"></div>
                        </div>
                        <span class="relative" id="unique-minutes">47</span>
                        <div class="absolute inset-0 flex items-center">
                            <div class="h-px w-full bg-black"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>
<script src="js/dashboard.js"></script>