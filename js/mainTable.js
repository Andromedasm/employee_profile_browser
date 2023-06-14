// applyRowClickListeners.js
function applyRowClickListeners() {
    const rows = document.querySelectorAll('.clickable-row');

    rows.forEach(row => {
        // 先移除可能存在的旧事件监听器
        row.removeEventListener('click', rowClickHandler);
        row.addEventListener('click', rowClickHandler);
    });
}

function rowClickHandler() {
    const attributes = [
        'id', 'name', 'branch', 'company', 'furigana', 'profile', 'start_date',
        'status', 'gender', 'birthday', 'photo', 'director', 'position',
        'employee_id', 'qualifications'
    ];
    const url = new URL('../profilecard.php', window.location.origin);

    attributes.forEach(attribute => {
        const value = this.getAttribute('data-' + attribute);
        if (value) {
            url.searchParams.append(attribute, value);
        }
    });

    window.location.href = url.toString();
}

// 在文档加载完成后执行 applyRowClickListeners 函数
document.addEventListener('DOMContentLoaded', function () {
    applyRowClickListeners();
});

// searchData.js
async function searchData() {
    const searchInput = document.getElementById("search-input");
    let searchQuery = searchInput.value;

    // 使用 trim() 方法移除查询两端的空白字符
    searchQuery = searchQuery.trim();

    // 如果查询仅包含空白字符，那么就不执行搜索
    if (searchQuery.length === 0) {
        sessionStorage.removeItem('searchQuery');
        window.location.reload();
        return;
    }

    // 将搜索查询保存在会话存储中
    sessionStorage.setItem('searchQuery', searchQuery);

    try {
        const response = await fetch('../search.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `query=${encodeURIComponent(searchQuery)}`
        });

        const data = await response.text();
        document.querySelector("tbody").innerHTML = data;
        applyRowClickListeners();
    } catch (error) {
        console.error("検索結果の取得中にエラーが発生しました:", error);
        document.querySelector("tbody").innerHTML = '';
    }
}

// 在页面加载时检查会话存储以获取之前的搜索查询
window.onload = function () {
    const previousSearchQuery = sessionStorage.getItem('searchQuery');
    if (previousSearchQuery !== null && previousSearchQuery !== '') {
        document.getElementById("search-input").value = previousSearchQuery;
        searchData();
    }
};

// updateTable.js
// 更新 URL 参数以匹配用户选择的限制、公司和分支
function updateUrlParams() {
    const limit = document.getElementById('results-per-page').value; // 获取每页显示的条数
    const company = document.getElementById('employee-company').value; // 获取公司
    const branch = document.getElementById('employee-branch').value; // 获取分支
    const page = 1; // 设置页码为1

    const url = new URL(window.location.href); // 获取当前页面的URL
    url.searchParams.set('limit', limit); // 设置URL中的limit参数
    url.searchParams.set('company', company); // 设置URL中的company参数

    if (branch === 'All') { // 如果分支为All,则删除URL中的branch参数
        url.searchParams.delete('branch'); // 删除URL中的branch参数
    } else { // 如果分支不为All,则设置URL中的branch参数
        url.searchParams.set('branch', branch); // 设置URL中的branch参数
    }

    url.searchParams.set('page', page); // 设置URL中的page参数

    window.location.href = url.toString(); // 重定向到新的URL
}

document.addEventListener('DOMContentLoaded', function () { // 在文档加载完成后执行
    // 获取选择元素
    const limitSelect = document.getElementById('results-per-page'); // 获取每页显示的条数
    const companySelect = document.getElementById('employee-company'); // 获取公司
    const branchSelect = document.getElementById('employee-branch'); // 获取分支

    // 为选择元素添加事件监听器，当它们的值更改时调用 updateUrlParams 函数
    limitSelect.addEventListener('change', updateUrlParams); // 当每页显示的条数更改时,调用updateUrlParams函数
    companySelect.addEventListener('change', updateUrlParams); // 当公司更改时,调用updateUrlParams函数
    branchSelect.addEventListener('change', updateUrlParams); // 当分支更改时,调用updateUrlParams函数
});

// usertable.js
document.addEventListener('DOMContentLoaded', function () {
    applyRowClickListeners();
});



