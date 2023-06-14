<?php
require_once 'config.php'; // 引入数据库配置文件

// 连接到 PostgreSQL 数据库
try {
    global $connection; // 使用 global 关键字将 $connection 变量声明为全局变量
    $connection = new PDO("pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";user=" . DB_USER . ";password=" . DB_PASSWORD); // 创建一个 PDO 实例
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置 PDO 错误模式
} catch (PDOException $e) { // 捕获异常
    error_log("エラーが発生しました: " . $e->getMessage()); // 记录错误日志
    echo "システムエラーが発生しました。しばらくしてからもう一度お試しください。"; // 输出通用错误信息
}

