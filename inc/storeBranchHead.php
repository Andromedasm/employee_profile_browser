<?php

$user_id = $_SESSION['user_id']; // 获取当前用户的 ID
$query = "SELECT id, role, company FROM employee_viewers WHERE id = ?"; // 创建查询用户信息的 SQL 语句
$stmt = $connection->prepare($query); // 准备查询
$stmt->bindValue(1, $user_id, PDO::PARAM_INT); // 绑定参数
$stmt->execute(); // 执行查询
$user = $stmt->fetch(PDO::FETCH_ASSOC); // 获取用户信息

if (!isset($_SESSION['user_id'])) { // 如果用户未登录
    header('Location: index.php'); // 重定向到主页
    exit();
}

if ($user['role'] === 'admin') {
    if (isset($_GET['company'])) { // 如果 URL 中包含 company 参数
        $companyName = htmlspecialchars($_GET['company'], ENT_QUOTES, 'UTF-8'); // 获取 company 参数的值，并进行 HTML 转义
    } else { // 如果 URL 中不包含 company 参数
        $companyName = "all"; // 设置 companyName 为 "all"
    }
} else {
    $companyName = $user['company']; // 如果用户不是管理员，设置 companyName 为用户的 company
}

if ($user['role'] === 'admin') { // 如果用户角色是管理员
    if ($companyName === "all") { // 如果 companyName 为 "all"
        $query_branches = "SELECT DISTINCT branch_name as branch FROM branches"; // 创建查询所有分支的 SQL 语句
        $stmt_branches = $connection->prepare($query_branches); // 准备查询
    } else { // 如果 companyName 不为 "all"
        $query_branches = "SELECT DISTINCT branch_name as branch FROM branches WHERE company = :companyName"; // 创建查询指定公司分支的 SQL 语句
        $stmt_branches = $connection->prepare($query_branches); // 准备查询
        $stmt_branches->bindValue(':companyName', $companyName, PDO::PARAM_STR); // 绑定参数
    }
    $stmt_branches->execute(); // 执行查询
    $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC); // 获取分支信息
    $displayedBranches = array_column($branches, 'branch'); // 获取分支名称，并存储到数组中
} else { // 如果用户角色不是管理员
    $query_branches = "SELECT DISTINCT branch FROM user_branches WHERE user_id = :userId"; // 创建查询分支的 SQL 语句
    $stmt_branches = $connection->prepare($query_branches); // 准备查询
    $stmt_branches->bindValue(':userId', $user['id'], PDO::PARAM_INT); // 绑定参数
    $stmt_branches->execute(); // 执行查询
    $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC); // 获取分支信息
    $displayedBranches = array_column($branches, 'branch'); // 获取分支名称，并存储到数组中
}
?>
