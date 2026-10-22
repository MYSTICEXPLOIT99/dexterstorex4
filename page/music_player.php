<?php
require_once __DIR__ . '/a_func.php';
$rawUrl = $config['bg_music'] ?? '';
$ytVideoId = '';
if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $rawUrl, $match)) {
    $ytVideoId = $match[1];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เครื่องเล่นเพลงเบื้องหลัง - <?= htmlspecialchars($config['name'] ?? 'CYGNUS') ?></title>
    <!-- Include Tailwind / FontAwesome just in case -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #0f172a;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .player-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            max-width: 320px;
            width: 90%;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
        }
        .music-disc {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: spin 8s linear infinite;
            animation-play-state: paused;
        }
        .music-disc.playing {
            animation-play-state: running;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
        .btn-ctrl {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.2rem;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-ctrl:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="player-card">
        <div class="music-disc" id="disc">
            <i class="fa-solid fa-compact-disc fa-3x text-white"></i>
        </div>
        <h5 class="fw-bold mb-1">เพลงเบื้องหลังระบบ</h5>
        <p class="text-muted small mb-4">ห้ามปิดหน้าต่างนี้หากต้องการเล่นเพลงแบบไม่สะดุด</p>

        <button class="btn-ctrl" id="playBtn">
            <i class="fa-solid fa-play" id="playIcon"></i>
        </button>

        <?php if (!empty($ytVideoId)): ?>
            <!-- YouTube Player -->
            <iframe id="ytPlayer" style="display:none;" 
                    src="https://www.youtube.com/embed/<?= $ytVideoId ?>?enablejsapi=1&autoplay=1&loop=1&playlist=<?= $ytVideoId ?>" 
                    allow="autoplay"></iframe>
        <?php else: ?>
            <!-- MP3 Audio -->
            <audio id="mp3Player" loop src="<?= htmlspecialchars($rawUrl) ?>"></audio>
        <?php endif; ?>
    </div>

    <script>
        const playBtn = document.getElementById('playBtn');
        const playIcon = document.getElementById('playIcon');
        const disc = document.getElementById('disc');
        const mp3Player = document.getElementById('mp3Player');
        const ytPlayer = document.getElementById('ytPlayer');
        let isPlaying = false;

        function updateUI(playing) {
            isPlaying = playing;
            localStorage.setItem('music_playing', playing ? 'true' : 'false');
            if (playing) {
                playIcon.className = 'fa-solid fa-pause';
                disc.classList.add('playing');
            } else {
                playIcon.className = 'fa-solid fa-play';
                disc.classList.remove('playing');
            }
        }

        // Listen to playback commands from main site window
        window.addEventListener('storage', function(e) {
            if (e.key === 'music_cmd') {
                if (e.newValue === 'play') {
                    play();
                } else if (e.newValue === 'pause') {
                    pause();
                }
            }
        });

        function play() {
            if (mp3Player) {
                const savedTime = localStorage.getItem('music_time');
                if (savedTime) mp3Player.currentTime = parseFloat(savedTime);
                mp3Player.play().then(() => {
                    updateUI(true);
                }).catch(err => {
                    console.log("Autoplay blocked. User gesture needed.");
                });
            } else if (ytPlayer) {
                const savedTime = localStorage.getItem('music_time');
                if (savedTime && ytPlayer.contentWindow) {
                    ytPlayer.contentWindow.postMessage(JSON.stringify({
                        event: 'command',
                        func: 'seekTo',
                        args: [parseFloat(savedTime), true]
                    }), '*');
                }
                ytPlayer.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: 'playVideo',
                    args: ''
                }), '*');
                updateUI(true);
            }
        }

        function pause() {
            if (mp3Player) {
                mp3Player.pause();
            } else if (ytPlayer) {
                ytPlayer.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: 'pauseVideo',
                    args: ''
                }), '*');
            }
            updateUI(false);
        }

        playBtn.addEventListener('click', function() {
            if (isPlaying) {
                pause();
            } else {
                play();
            }
        });

        // Setup time updates and sync state back to main site window
        if (mp3Player) {
            mp3Player.addEventListener('timeupdate', function() {
                localStorage.setItem('music_time', mp3Player.currentTime);
            });
        }

        if (ytPlayer) {
            window.addEventListener('message', function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.event === 'infoDelivery' && data.info && data.info.currentTime !== undefined) {
                        localStorage.setItem('music_time', data.info.currentTime);
                    }
                } catch(e) {}
            });
            setInterval(() => {
                if (isPlaying && ytPlayer.contentWindow) {
                    ytPlayer.contentWindow.postMessage(JSON.stringify({
                        event: 'listening',
                        id: 1
                    }), '*');
                }
            }, 1000);
        }

        // Try auto-play immediately upon load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(play, 1000);
        });
    </script>
</body>
</html>
