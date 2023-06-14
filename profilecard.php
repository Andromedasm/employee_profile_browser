<?php
// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800); // 设置 session 生命周期为 1800 秒（30 分钟）

session_start(); // 启动会话

if (!isset($_SESSION['user_id'])) { // 如果用户未登录
    header('Location: index.php'); // 重定向到首页
    exit();
}

include 'inc/get_profile_data.php';

function echoHtml($variable) // 用于输出 HTML 的函数
{
    echo $variable ? htmlspecialchars($variable, ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; // 将变量中的特殊字符转换成 HTML 实体
}

function translateCompany($company) //将公司名称翻译为日语
{
    switch ($company) {
        case 'Shiroyama':
            return '城山';
        case 'ShiroyamaHD':
            return '城山ホールディングス';
        case 'SCOM':
            return '城山コミュニケーションズ';
        case 'SDM':
            return 'エスディーモバイル';
        case 'ShiroyamaBusiness':
            return '城山ビジネス';
        case 'Shuuchi':
            return 'シュウチ';
        default:
            return $company;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile Card</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal { /*模态框遮罩层样式*/
            display: none; /*隐藏模态框*/
            position: fixed; /*固定在屏幕上*/
            z-index: 1; /*放到所有内容之上*/
            left: 0; /*距离屏幕左边0*/
            top: 0; /*距离屏幕上边0*/
            width: 100%; /*宽度100%*/
            height: 100%; /*高度100%*/
            overflow: auto; /*内容超出可滚动*/
            background-color: rgba(0, 0, 0, 0.4); /*背景颜色，带有透明度*/
            backdrop-filter: blur(5px); /*背景模糊*/
        }

        .modal-content { /*模态框内容*/
            background-color: white; /*背景颜色*/
            margin: 15% auto; /*距离上下左右的距离*/
            padding: 20px; /*内边距*/
            border: 1px solid #888; /*边框*/
            width: 80%; /*宽度80%*/
        }
    </style>
    <style>
        body {
            touch-action: pan-y;
        }
    </style>

</head>

<!--背景图片-->
<body class="font-sans antialiased text-gray-900 leading-normal tracking-wider bg-cover"
      style="background-image:url('https://images.unsplash.com/photo-1539689816072-86231273b4d6?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1740&q=80');">

<!--返回按钮-->
<div class="flex justify">
    <a onclick="window.history.back();"
       class="text-white text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
</div>


<div class="max-w-4xl flex items-center h-auto lg:h-screen flex-wrap mx-auto my-32 lg:my-0">

    <!--主列-->
    <div id="profile"
         class="w-full lg:w-3/5 rounded-lg lg:rounded-l-lg lg:rounded-r-none shadow-2xl bg-white opacity-90 mx-6 lg:mx-0">


        <div class="p-4 md:p-12 text-center lg:text-left">
            <!--手机端头像-->
            <div class="block lg:hidden rounded-full shadow-xl mx-auto -mt-16 h-48 w-48 bg-cover bg-center"
                 style="background-image: url('<?php echo $imgSrc ?: 'img/anonymous_avatar.png'; ?>')"></div>

            <p class="text-base font-bold pt-8 lg:pt-0"><?php echoHtml($furigana); ?></p>
            <h1 class="text-3xl font-bold pt-8 lg:pt-0"><?php echoHtml($name); ?></h1>
            <div class="mx-auto lg:mx-0 w-4/5 pt-3 border-b-2 border-green-500 opacity-25"></div>
            <p class="pt-4 text-base font-bold flex items-center justify-center lg:justify-start">
                <svg class="h-4 fill-current text-green-700 pr-4" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 20 20">
                    <path
                            d="M9 12H1v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6h-8v2H9v-2zm0-1H0V5c0-1.1.9-2 2-2h4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v6h-9V9H9v2zm3-8V2H8v1h4z"/>
                </svg>
                <?php echoHtml(translateCompany($company)); ?>
            </p>
            <p class="pt-2 text-gray-600 text-xs lg:text-sm flex items-center justify-center lg:justify-start">
                <svg class="h-4 fill-current text-green-700 pr-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"></path>
                </svg>
                <?php echoHtml($branch); ?>
            </p>
            <p class="pt-2 text-gray-600 text-xs lg:text-sm flex items-center justify-center lg:justify-start">
                <?php if (!empty($director) && $director !== null && $director !== '無' && !preg_match('/[\p{Han}]/u', $director)): ?>
                    <i class="fas fa-user-tie pr-4 text-green-700"></i>
                    <?php echoHtml($director); ?>
                <?php endif; ?>
            </p>
            <p class="pt-2 text-gray-600 text-xs lg:text-sm flex items-center justify-center lg:justify-start">
                <i class="fas fa-user-circle pr-4 text-green-700"></i>
                <?php echoHtml($status) ?>
            </p>
            <div class="pt-6">
                <!--详细内容按钮-->
                <button id="openModalBtn"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    詳細内容
                </button>
            </div>
        </div>

    </div>

    <!--Img Col-->
    <div class="w-full lg:w-2/5">
        <!--电脑端头像-->
        <img src="data:image/png;base64,<?php echo $base64ImageData ?: base64_encode(file_get_contents('img/anonymous_avatar.png')); ?>"
             alt="Profile Picture" id="profile"
             class="rounded-none lg:rounded-lg shadow-2xl hidden lg:block">
    </div>


    <!--黑暗模式按钮-->
    <div class="absolute top-0 right-0 h-12 w-18 p-4">
        <button class="js-change-theme focus:outline-none">🌙</button>
    </div>

    <!--详细内容弹窗-->
    <div id="myModal" class="modal fixed inset-0 flex items-center justify-center p-4 bg-black bg-opacity-50 hidden">
        <div class="modal-content bg-white p-6 rounded-lg w-full md:w-2/3 lg:w-1/2">
            <button class="close float-right text-xl font-bold">&times;</button>
            <h2 class="text-2xl mb-4">詳細内容</h2>
            <img src="<?php echo $imgSrc ? $imgSrc : 'img/anonymous_avatar.png'; ?>" alt="Image in Modal"
                 class="rounded-full w-40 h-40 object-cover mx-auto mb-4 border-2 border-gray-300 shadow-lg">
            <div class="additional-info mt-4 text-base">
                <p class="flex items-center mb-2">
                    <i class="fas fa-language pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($furigana); ?></span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-user pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($name); ?></span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-building pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml(translateCompany($company)); ?></span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-map-marker-alt pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($branch); ?></span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-calendar-alt pr-2 w-6"></i>
                    <span class="pl-2">
                    <?php
                    echoHtml($start_date);
                    $start_date_timestamp = strtotime($start_date);
                    $current_date_timestamp = time();
                    $diff = abs($current_date_timestamp - $start_date_timestamp);
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    echo " (" . $years . " 年 " . $months . "ヶ月)";
                    ?>
                </span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-user-circle pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($status); ?></span>
                </p>
<!--                <p class="flex items-center mb-2">-->
<!--                    <i class="fas fa-birthday-cake pr-2 w-6"></i>-->
<!--                    <span class="pl-2">--><?php //echoHtml($birthday); ?><!--</span>-->
<!--                </p>-->
                <p class="flex items-center mb-2">
                    <i class="fas fa-id-badge pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($employee_id); ?></span>
                </p>
                <p class="flex items-center mb-2">
                    <i class="fas fa-user-tie pr-2 w-6"></i>
                    <span class="pl-2"><?php echoHtml($position); ?></span>
                </p>
                <?php if (!empty($director) && $director !== '無'): ?>
                    <p class="flex items-center mb-2">
                        <i class="fas fa-user-tie pr-2 w-6"></i>
                        <span class="pl-2"><?php echoHtml($director); ?></span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($qualifications) && $qualifications !== null): ?>
                    <p class="flex items-center mb-2">
                        <i class="fas fa-award pr-2 w-6"></i>
                        <span class="pl-2"><?php echoHtml($qualifications); ?></span>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/popper.js@1/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@4"></script>
    <script>
        // Init tooltips
        tippy('.link', {
            placement: 'bottom'
        })

        // Toggle mode
        const toggle = document.querySelector('.js-change-theme');
        const body = document.querySelector('body');
        const profile = document.getElementById('profile');


        toggle.addEventListener('click', () => {

            if (body.classList.contains('text-gray-900')) {
                toggle.textContent = "☀️";
                body.classList.remove('text-gray-900');
                body.classList.add('text-gray-100');
                profile.classList.remove('bg-white');
                profile.classList.add('bg-gray-900');
            } else {
                toggle.textContent = "🌙";
                body.classList.remove('text-gray-100');
                body.classList.add('text-gray-900');
                profile.classList.remove('bg-gray-900');
                profile.classList.add('bg-white');

            }
        });
    </script>
    <script>
        // 获取模态框和按钮元素
        const modal = document.getElementById("myModal"); // 模态框
        const btn = document.getElementById("openModalBtn"); // 按钮
        const closeBtn = document.getElementsByClassName("close")[0]; // 关闭按钮

        // 当用户点击按钮时，打开模态框
        btn.onclick = function () {
            modal.style.display = "block"; // 显示模态框
        }

        // 当用户点击关闭按钮时，关闭模态框
        closeBtn.onclick = function () {
            modal.style.display = "none"; // 关闭模态框
        }

        // 当用户点击模态框外部时，关闭模态框
        window.onclick = function (event) {
            if (event.target === modal) { // 如果用户点击模态框外部
                modal.style.display = "none"; // 关闭模态框
            }
        }
    </script>
    <script src="./js/swipe-back.js"></script>
</body>

</html>
