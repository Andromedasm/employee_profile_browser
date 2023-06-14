document.addEventListener('DOMContentLoaded', function () { // 监听页面加载完成事件
    document.getElementById("prev-page").addEventListener("click", function () { // 监听上一页按钮的点击事件
        updatePage(current_page - 1); // 更新分页
    });

    document.getElementById("next-page").addEventListener("click", function () { // 监听下一页按钮的点击事件
        updatePage(current_page + 1); // 更新分页
    });

    // 更新分页并在新页面加载用户数据
    function updatePage(newPage) {
        if (newPage >= 1 && newPage <= total_pages) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', newPage);
            urlParams.set('company', selected_company);
            urlParams.set('branch', selected_branch);

            const newUrl = location.origin + location.pathname + '?' + urlParams.toString();
            location.href = newUrl;
        }
    }

    // 获取并保存选定的部门
    const branchSelect = document.getElementById('employee-branch'); // 获取部门选择元素
    let selectedBranch; // 用于保存选定的部门
    selectedBranch = branchSelect.getAttribute('data-selected-branch'); // 获取选定的部门
});
