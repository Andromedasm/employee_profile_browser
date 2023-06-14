<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

include 'inc/conn.php'; // 引入数据库连接文件

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // 从 GET 请求参数中获取员工 ID
} else {
    // 报错或重定向到其他页面
    die("社員IDが指定されていません。");
}

function isValidBase64($str)
{
    if (base64_encode(base64_decode($str, true)) === $str) {
        return true;
    } else {
        return false;
    }
}

try {
    $query = "SELECT * FROM employee WHERE id = :id"; // 构造 SQL 查询语句
    $stmt = $connection->prepare($query); // 准备查询
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // 绑定参数，避免 SQL 注入攻击
    $stmt->execute(); // 执行查询
    $row = $stmt->fetch(PDO::FETCH_ASSOC); // 获取查询结果

    if ($row) {
        // 使用 PHP array_map 函数和匿名函数，对所有输出的数据进行处理，防止潜在的 XSS 攻击
        $row = array_map(function ($value) {
            return is_null($value) ? '' : htmlspecialchars($value);
        }, $row);

        extract($row);

        $base64ImageData = $row['photo'];
        if (isValidBase64($base64ImageData)) {
            $imgSrc = "data:image/jpeg;base64,$base64ImageData"; // 修改这一行
        } else {
            die("写真データが不正です。");
        }
    }
} catch (PDOException $e) {
    // 处理异常，例如记录错误日志或显示通用错误信息
    error_log("クエリエラー: " . $e->getMessage());
    die("エラーが発生しました。管理者に連絡してください。");
}