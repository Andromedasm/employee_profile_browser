<?php
// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800); // 设置 session 生命周期为 1800 秒（30 分钟）

session_start(); // 启动会话

if (!isset($_SESSION['user_id'])) { // 如果用户未登录
    header('Location: index.php'); // 重定向到首页
    exit();
}

require 'inc/conn.php';

$email = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); // 获取当前登录用户的邮箱
$query = "SELECT role, company FROM employee_viewers WHERE email = ?"; // 查询当前登录用户的角色和所属公司

$stmt = $connection->prepare($query); // 准备查询
$stmt->bindValue(1, $email, PDO::PARAM_STR); // 绑定参数
$stmt->execute(); // 执行查询
$user = $stmt->fetch(PDO::FETCH_ASSOC); // 获取查询结果

if (!$user) { // 如果查询结果为空（即，邮箱不存在于数据库）
    header('Location: index.php'); // 跳转到登录页面
    exit();
}

function canAccessCompany($user, $company)
{
    return $user['role'] === 'admin' || $user['company'] === $company;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $page_title = 'Company Branch'; ?>
    <?php require 'header.php'; ?>
</head>

<body>
<div class="flex items-center bg-indigo-100 w-screen min-h-screen" style="font-family: 'Muli', sans-serif;">
    <div class="container ml-auto mr-auto flex flex-wrap items-start">
        <div class="w-full pl-5 lg:pl-2 mb-4 mt-4">
            <h1 class="text-3xl lg:text-4xl text-gray-700 font-extrabold">
                会社選択
            </h1>
        </div>
        <?php
        $companies = [
            'ShiroyamaHD' => 'bg-blue-800',
            'Shiroyama' => 'bg-blue-800',
            'SCOM' => 'bg-orange-500',
            'SDM' => 'bg-gray-300'
            /*'ShiroyamaBusiness' => 'bg-blue-800',
            'Shuuchi' => 'bg-blue-800'*/
        ];

        foreach ($companies as $company => $bg_color) {
            if (canAccessCompany($user, $company)) {
                ?>
                <div class="w-full md:w-1/2 lg:w-1/8 pl-5 pr-5 mb-5 lg:pl-2 lg:pr-2">
                    <a href="storeBranch.php?company=<?php echo $company; ?>">
                        <div
                                class="bg-white rounded-lg m-h-64 p-2 transform hover:translate-y-2 hover:shadow-xl transition duration-300">
                            <figure class="mb-2">
                                <img src="img/building_business_office.png" alt="" class="h-64 ml-auto mr-auto"/>
                            </figure>
                            <div class="rounded-lg p-4 <?php echo $bg_color; ?> flex flex-col">
                                <div>
                                    <h5 class="text-white text-2xl font-bold leading-none">
                                        <?php
                                        if ($company === 'Shiroyama') {
                                            echo '城山';
                                        } elseif ($company === 'ShiroyamaHD') {
                                            echo '城山HD';
                                        } elseif ($company === 'ShiroyamaBusiness') {
                                            echo '城山ビジネス';
                                        } elseif ($company === 'Shuuchi') {
                                            echo 'シュウチ';
                                        } else {
                                            echo $company;
                                        }
                                        ?>
                                    </h5>
                                    <span class="text-xs text-gray-400 leading-none"></span>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-lg text-white font-light">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

</body>
</html>