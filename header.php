<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<div id="main-content">
    <aside>
        <div class="hidden w-64 bg-gray-800 p-4 h-screen fixed left-0 top-0 z-50 shadow-lg transition-all duration-300"
             id="sidebar">
            <ul class="space-y-4">
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <li><a href="dashboard.php"
                           class="flex items-center p-2 text-xl font-normal text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                            </svg>
                            <span class="ml-3">ホーム</span></a></li>
                <?php } ?>
                <li><a href="<?php echo ($_SESSION['role'] === 'admin') ? 'companyBranch.php' : 'storeBranch.php'; ?>"
                       class="flex items-center p-2 text-xl font-normal text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                        </svg>
                        <span class="ml-3">会社/支店選択</span></a></li>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <li><a href="usertable.php"
                           class="flex items-center p-2 text-xl font-normal text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg aria-hidden="true"
                                 class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                 fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                            </svg>
                            <span class="ml-3">社員リスト</span></a></li>
                <?php } ?>
                <li><a href="logout.php"
                       class="flex items-center p-2 text-xl font-normal text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path>
                        </svg>
                        <span class="ml-3">ログアウト</span></a></li>

            </ul>
        </div>
    </aside>
    <!-- Header Bar -->
    <div id="main-content-wrapper">
        <header class="p-4 bg-gray-400 shadow">
            <button id="sidebar-toggle" class="text-2xl text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </header>
    </div>
</div>

<script src="js/header.js"></script>
</body>
</html>
