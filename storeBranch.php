<?php
// 限制 session 生命周期
ini_set('session.gc_maxlifetime', 1800);

session_start();
require 'inc/conn.php';
require 'inc/storeBranchHead.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            touch-action: pan-y;
        }
    </style>
    <?php $page_title = 'Store Branch'; ?>
    <?php require 'header.php'; ?>
    <?php
    $displayName = '';
    switch ($companyName) {
        case 'Shiroyama':
            $displayName = '城山';
            break;
        case 'ShiroyamaHD':
            $displayName = '城山HD';
            break;
        case 'ShiroyamaBusiness':
            $displayName = '城山ビジネス';
            break;
        case 'SCOM':
            $displayName = '城山コミュニケーションズ';
            break;
        case 'SDM':
            $displayName = 'エスディーモバイル';
            break;
        case 'Shuuchi':
            $displayName = 'シュウチ';
            break;
        default:
            $displayName = $companyName;
            break;
    }
    ?>
</head>

<body>
<div class="flex items-center bg-indigo-100 w-screen min-h-screen" style="font-family: 'Muli', sans-serif;">
    <div class="container ml-auto mr-auto flex flex-wrap items-start">
        <div class="w-full pl-5 lg:pl-2 mb-4 mt-4">
            <h1 class="text-3xl lg:text-4xl text-gray-700 font-extrabold">
                <?php echo $displayName; ?>
            </h1>
        </div>
    </div>
</div>
<script>
    window.onload = function () {
        const branches = <?php echo json_encode($displayedBranches); ?>;
        const companyName = "<?php echo $companyName; ?>";
        const branchConfig = {
            "SCOM": {
                "au": "bg-orange-500",
                "UQ": "bg-pink-600",
                "default": "bg-blue-800"
            },
            "SDM": {
                "SB": "bg-gray-300",
                "YM": "bg-red-600",
                "default": "bg-blue-800"
            },
            "Shiroyama": "bg-blue-800",
            "ShiroyamaHD": "bg-blue-800",
        };

        function getCardBackgroundColor(branch) {
            const companyConfig = branchConfig[companyName];
            if (typeof companyConfig === "string") {
                return companyConfig;
            }
            for (const prefix in companyConfig) {
                if (branch.startsWith(prefix)) {
                    return companyConfig[prefix];
                }
            }
            return companyConfig["default"] || "bg-gray-300";
        }

        function createBranchCards(displayedBranches) {
            const container = document.querySelector('.flex.flex-wrap.items-start');
            displayedBranches.forEach(branch => {
                const encodedBranch = encodeURIComponent(branch);
                const cardBackgroundColor = getCardBackgroundColor(branch);
                const card = `
            <div class="w-full md:w-1/2 lg:w-1/8 pl-5 pr-5 mb-5 lg:pl-2 lg:pr-2">
                <a href="usertable.php?branch=${encodedBranch}&from_store_branch=true" class="block no-underline">
                    <div class="bg-white rounded-lg m-h-64 p-2 transform hover:translate-y-2 hover:shadow-xl transition duration-300">
                        <figure class="mb-2 hidden md:block">
                            <img src="img/building_business_office.png" alt="" class="h-64 ml-auto mr-auto">
                        </figure>
                        <div class="rounded-lg p-4 ${cardBackgroundColor} flex flex-col">
                            <span class="text-white font-bold">${branch}</span>
                        </div>
                    </div>
                </a>
            </div>
        `;
                container.insertAdjacentHTML('beforeend', card);
            });
        }

        createBranchCards(branches);
    };
</script>

<script src="js/swipe-back.js"></script>
</body>
</html>
