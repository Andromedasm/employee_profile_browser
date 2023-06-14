// swipe-back.js
// 用于实现滑动返回功能的js文件
let startX = 0; // 用于保存触控开始时手指的水平坐标
let endX = 0; // 用于保存触控结束时手指的水平坐标

document.addEventListener('touchstart', (e) => { // 监听触控开始事件
    startX = e.touches[0].clientX; // 将手指触控时的水平坐标保存到startX中
});

document.addEventListener('touchmove', (e) => { // 监听触控移动事件
    endX = e.touches[0].clientX; // 将手指离开时的水平坐标保存到endX中
});

document.addEventListener('touchend', () => {
    let distance = Math.abs(endX - startX); // 获取滑动的总距离

    // 判断滑动距离大于100，并且滑动方向为从左向右
    if (distance > 100 && endX > startX) {
        window.history.back(); // 触发返回功能
    }

    startX = 0; // 将startX重置为0
    endX = 0; // 将endX重置为0
});