function updateClock() {
    const hoursElement = document.getElementById('unique-hours');
    const minutesElement = document.getElementById('unique-minutes');

    const currentTime = new Date();
    const hours = String(currentTime.getHours()).padStart(2, '0');
    const minutes = String(currentTime.getMinutes()).padStart(2, '0');

    hoursElement.textContent = hours;
    minutesElement.textContent = minutes;

    setTimeout(updateClock, 60000);
}

updateClock();
