<?php
require 'inc/conn.php';

// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800); // 设置 session 生命周期为 1800 秒（30 分钟）

session_start();

if (!isset($_SESSION['user_id'])) { // 如果用户未登录
    header('Location: index.php'); // 重定向到首页
    exit();
}

require 'inc/fetch_data.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Table</title>
    <script src="https://cdn.jsdelivr.net/npm/purify@3.4.0/lib/purify.min.js"></script>
    <script src="js/mainTable.js"></script>
    <script>
        const isAdmin = <?php echo $user_role === 'admin' ? 'true' : 'false'; ?>; //
        // 将 PHP 变量值存储在 JavaScript 变量中
        const current_page = <?= $current_page ?>;
        const total_pages = <?= $total_pages ?>;
        const limit = <?= $limit ?>;
        const selected_company = <?= json_encode($selected_company) ?>;
        const selected_branch = <?= json_encode($selected_branch) ?>;
    </script>
    <style>
        body {
            touch-action: pan-y;
        }

        button[disabled] {
            background: #ddd;
            cursor: not-allowed;
        }
    </style>
    <?php require 'header.php'; ?>
</head>
<!-- component -->
<body class="antialiased font-sans bg-gray-200">
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">社員リスト</h2>
        </div>
        <?php
        if (isset($_GET['from_store_branch']) && $_GET['from_store_branch'] === 'true') {
            // Do nothing
        } else {
            require 'elements.php';
        }
        ?>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                    <tr>
                        <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            名前
                        </th>
                        <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            所属支店
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($rows as $row) {
                        // 遍历查询结果
                        $base64ImageData = $row['photo']; // 从数据库中获取图片数据
                        $imgSrc = $base64ImageData ? "data:image/jpeg;base64,$base64ImageData" : 'img/anonymous_avatar.png'; // 如果数据库中没有图片数据，则使用默认图片路径


                        echo '<tr class="clickable-row" data-id="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '">';
                        echo "<td class='px-5 py-5 border-b border-gray-200 bg-white text-sm'>";
                        echo "<div class='flex items-center'>";
                        echo "<div class='flex-shrink-0 w-10 h-10'>";
                        echo "<img class='w-full h-full rounded-full' src='{$imgSrc}' alt=''/>"; // 显示图片
                        echo "</div>";

                        // 检查公司名是否为 'ShiroyamaBusiness'
                        $isShiroyamaBusiness = $row['company'] === 'ShiroyamaBusiness';

                        echo "<div class='ml-3'>";
                        echo "<p class='text-gray-900 whitespace-no-wrap'>{$row['name']}";

                        // 如果公司名为 'ShiroyamaBusiness'，添加括号和 "ビジネス"
                        if ($isShiroyamaBusiness) {
                            echo "<strong class='text-black'>(ビジネス)</strong>";
                        }

                        echo "</p>";
                        echo "</div>";

                        echo "</div>";
                        echo "</td>";
                        echo "<td class='px-5 py-5 border-b border-gray-200 bg-white text-sm'>";
                        echo "<p class='text-gray-900 whitespace-no-wrap'>{$row['branch']}</p>"; // 显示所属支店
                        echo "</td>";
                        echo "</tr>";
                    }

                    $connection = null; // 关闭数据库连接
                    ?>
                    </tbody>

                </table>
                <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                    <span class="text-xs xs:text-sm text-gray-900">
                        <?= $offset + 1 ?> 件目から <?= min($offset + $limit, $total_rows) ?> 件目まで表示中 (全 <?= $total_rows ?> 件)
                    </span>
                    <div class="inline-flex mt-2 xs:mt-0">
                        <button
                                id="prev-page"
                                class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l"
                            <?= $current_page <= 1 ? 'disabled' : '' ?>>
                            前へ
                        </button>
                        <button
                                id="next-page"
                                class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r"
                            <?= $current_page >= $total_pages ? 'disabled' : '' ?>>
                            次へ
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script src="js/flip.js"></script>
<script src="js/swipe-back.js"></script>


</body>

</html>