<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getSvgArrow()
{
    return '<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>';
}

?>
<div class="my-2 flex sm:flex-row flex-col">
    <div class="flex flex-row mb-1 sm:mb-0">
        <div class="relative">
            <select id="results-per-page"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                <?php
                $resultsPerPageOptions = [5, 10, 20]; // 每页显示的记录数选项
                foreach ($resultsPerPageOptions as $option) { // 遍历每页显示的记录数选项
                    $selected = ($limit == $option) ? 'selected' : ''; // 如果当前选项与当前记录数相同，则选中当前选项
                    echo "<option " . htmlspecialchars($selected, ENT_QUOTES, 'UTF-8') . ">" . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . "</option>"; // 输出每页显示的记录数选项
                }
                ?>
            </select>
            <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <?php echo getSvgArrow(); ?>
            </div>
        </div>

        <div class="relative">
            <select id="employee-company"
                    class="appearance-none h-full rounded-r border-t sm:rounded-r-none sm:border-r-0 border-r border-b block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <?php if ($user_role === 'admin'): ?>
                    <option value="" <?= ($selected_company === '') ? 'selected' : '' ?>>All</option>
                    <option value="ShiroyamaHD" <?= ($selected_company === 'ShiroyamaHD') ? 'selected' : '' ?>>城山HD</option>
                    <option value="Shiroyama" <?= ($selected_company === 'Shiroyama') ? 'selected' : '' ?>>城山</option>
                    <option value="SCOM" <?= ($selected_company === 'SCOM') ? 'selected' : '' ?>>SCOM</option>
                    <option value="SDM" <?= ($selected_company === 'SDM') ? 'selected' : '' ?>>SDM</option>
<!--                    <option value="ShiroyamaBusiness" --><?php //= ($selected_company === 'ShiroyamaBusiness') ? 'selected' : '' ?><!-->城山ビジネス</option>-->
                    <option value="Shuuchi" <?= ($selected_company === 'Shuuchi') ? 'selected' : '' ?>>シュウチ</option>
                <?php else: ?>
                    <option selected><?= $user_company ?></option>
                <?php endif; ?>
            </select>
            <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <?php echo getSvgArrow(); ?>
            </div>
        </div>
        <div class="relative">
            <select id="employee-branch"
                    data-selected-branch="<?php echo htmlspecialchars($selected_branch, ENT_QUOTES, 'UTF-8'); ?>"
                    class="appearance-none h-full rounded-r border-t sm:rounded-r-none sm:border-r-0 border-r border-b block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <?php if ($user_role === 'admin'): ?>
                    <option value="" <?= ($selected_branch === '') ? "selected" : ""; ?>>All</option>
                <?php endif; ?>
                <?= fetchBranchOptions($stmtBranches, $selected_branch) ?>
            </select>
            <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <?php echo getSvgArrow(); ?>
            </div>
        </div>
    </div>
    <?php if ($user_role === 'admin'): ?>
        <div class="block relative">
            <form id="search-form" oninput="searchData()">
            <span class="h-full absolute inset-y-0 left-0 flex items-center pl-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                    <path
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                    </path>
                </svg>
            </span>
                <input id="search-input" placeholder="Search"
                       class="appearance-none rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"/>
            </form>
        </div>
    <?php endif; ?>
</div>