(function() {
    // 获取侧边栏和主内容元素
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content-wrapper');
    const toggleButton = document.getElementById('sidebar-toggle');

    // 定义一个函数来切换侧边栏的显示/隐藏状态
    function toggleSidebar() {
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('ml-64');
    }

    // 添加一个事件监听器来处理侧边栏切换按钮的点击事件
    toggleButton.addEventListener('click', function(event) {
        event.stopPropagation(); // 阻止事件冒泡到document
        toggleSidebar();
    });

    // 添加一个全局的事件监听器，当点击侧边栏以外的位置时，如果侧边栏可见则隐藏
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !sidebar.classList.contains('hidden')) {
            toggleSidebar();
        }
    });
})();
