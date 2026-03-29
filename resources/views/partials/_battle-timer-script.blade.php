<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Battle Countdown Timers - Handles elements with class .js-battle-timer
        const timers = document.querySelectorAll('.js-battle-timer');
        
        function updateTimers() {
            const now = new Date();
            
            timers.forEach(timerElement => {
                const endTimeStr = timerElement.dataset.endTime;
                if (!endTimeStr || endTimeStr.trim() === '') {
                    timerElement.innerHTML = "NO CHALLENGE";
                    return;
                }
                
                const endTime = new Date(endTimeStr);
                if (isNaN(endTime.getTime())) {
                    timerElement.innerHTML = "INVALID TIME";
                    return;
                }
                
                const distance = endTime - now;
                
                if (distance < 0) {
                    timerElement.innerHTML = "ENDED";
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let display = '';
                if (days > 0) display += `${days}d `;
                if (hours > 0 || days > 0) display += `${hours}h `;
                if (minutes > 0 || hours > 0 || days > 0) display += `${minutes}m `;
                display += `${seconds}s`;
                
                timerElement.innerHTML = display;
            });
        }
        
        if (timers.length > 0) {
            updateTimers();
            setInterval(updateTimers, 1000);
        }
    });
</script>
