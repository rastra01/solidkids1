<?php
session_start();

require_once __DIR__ . '/groq_config.php';

// Pastikan variabel sesi untuk user ID telah diatur
if (!isset($_SESSION['user_id'])) {
    die("User tidak terautentikasi. Silakan login terlebih dahulu.");
}

$logged_in_user_id = $_SESSION['user_id'];
require 'databasekey.php';
require '../vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;
//use Parsedown;
$groq = new Groq('$groqApiKey');
$sql = "
    SELECT v.title, vw.views 
    FROM videos v
    JOIN views vw ON v.video_id = vw.video_id
    WHERE vw.user_id = ?
    ORDER BY vw.views 
";
$chatCompletion = $groq->chat()->completions()->create([
    'model'    => 'openai/gpt-oss-20b',
    'messages' => [
        [
            'role'    => 'system',
            'content' => 'saya adalah sebuah perusahaan yang bergerak di bidang pendidikan. Saya ingin meningkatkan kualitas pendidikan di Indonesia dengan membuat platform edukasi dengan konten yang bermanfaat yang dibuat oleh content creator untuk anak PAUD dan TK.Tolong jawab menggunakan bahasa indonesia semua'
          ],
          [
            'role'    => 'user',
            'content' => 'berikan rekomendasi ide video berdasarkan tabel database berikan 5 saja'.$sql.'tidak usah menampilkan data tabel database berikan rekomendasi saja'.'rapikan jawabannya lalu buatlah rekomendasi ide rata kiri'
          ],
        ],
        'temperature' => 0.7,
  ]);


  $parsedown= new Parsedown();
  $hasil= $parsedown->text($chatCompletion['choices'][0]['message']['content']);
?>
<?php
    $earnings = 0;
    $total_verified_videos = 0;

    // Query untuk menghitung pendapatan
    $sql4 = "
        SELECT SUM(vw.views) * 100 AS earnings
        FROM videos v
        JOIN views vw ON v.video_id = vw.video_id
        WHERE v.user_id = ?
    ";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("i", $logged_in_user_id);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    
    if ($result4->num_rows > 0) {
        $row = $result4->fetch_assoc();
        $earnings = $row['earnings'];
    } else {
        echo "0 results";
    }

    $sql_verified_videos = "
    SELECT COUNT(*) AS total_verified_videos
    FROM videos
    WHERE user_id = ? AND verified = 1
    ";
    $stmt_verified_videos = $conn->prepare($sql_verified_videos);
    $stmt_verified_videos->bind_param("i", $logged_in_user_id);
    $stmt_verified_videos->execute();
    $result_verified_videos = $stmt_verified_videos->get_result();

    if ($result_verified_videos->num_rows > 0) {
        $row = $result_verified_videos->fetch_assoc();
        $total_verified_videos = $row['total_verified_videos'];
    } else {
        echo "0 results";
    }
    $sql = "
        SELECT v.title, vw.views 
        FROM videos v
        JOIN views vw ON v.video_id = vw.video_id
        WHERE vw.user_id = ?
        ORDER BY vw.views 
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $title = [];
    $views = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $title[] = $row["title"];
            $views[] = $row["views"];
        }
    } else {
        echo "0 results";
    }
    $title_json = json_encode(array_unique($title));
    $views_json = json_encode($views);
    $sql2 = "
        SELECT t.name AS topik, SUM(vw.views) AS total_views 
        FROM topics t
        JOIN video_topics vt ON t.topic_id = vt.topic_id
        JOIN views vw ON vt.video_id = vw.video_id
        WHERE vw.user_id = ?
        GROUP BY t.topic_id
        ORDER BY total_views 
    ";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $logged_in_user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    $topik = [];
    $total_views = [];
    
    if ($result2->num_rows > 0) {
        while ($row = $result2->fetch_assoc()) {
            $topik[] = $row["topik"];
            $total_views[] = $row["total_views"];
        }
    } else {
        echo "0 results";
    }
    $sql3 = "
        SELECT 
            DATE_FORMAT(vw.view_date, '%Y-%m') AS bulan,
            SUM(vw.views) AS jumlah_penonton
        FROM 
            views vw
        WHERE vw.user_id = ?
        GROUP BY 
            DATE_FORMAT(vw.view_date, '%Y-%m')
        ORDER BY 
            bulan
    ";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $logged_in_user_id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    
    $data = [];
    if ($result3->num_rows > 0) {
        while ($row = $result3->fetch_assoc()) {
            $data[] = [
                'bulan' => $row['bulan'],
                'jumlah_penonton' => (int)$row['jumlah_penonton']
            ];
        }
    } else {
        echo "0 results";
    }
    
    $conn->close();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/graph.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Mahasiswa | Solid Kids</title>
</head>

<body>
    <div class="container">
        <?php 
       include 'sidebar.php';
       ?>
        <div class="content">
            <div class="header dashboard-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <p class="dashboard-overline">CREATOR STUDIO</p>
                        <h1 class="m-0">Dashboard</h1>
                        <p class="page-subtitle">Pantau performa dan perkembangan konten edukasi Anda.</p>
                    </div>
                    <div class="col-sm-6">
                        <div class="dashboard-actions">
                            <span class="role-badge"><i class="fa-solid fa-graduation-cap"></i> Area Mahasiswa</span>
                            <a class="dashboard-cta" href="upload.php">
                                <span>Upload konten</span>
                                <i class="fa-solid fa-arrow-up-from-bracket" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main dashboard-main">
                <div class="dashboard-section">
                    <div>
                        <p class="section-eyebrow">RINGKASAN</p>
                        <h2>Performa Anda</h2>
                    </div>
                    <span class="section-note"><i class="fa-solid fa-sparkles" aria-hidden="true"></i> Terus berkarya dan bertumbuh</span>
                </div>
                <div class="card-container">
                    <div class="card-mini">
                        <div class="grafik2">
                            <h1>Rp <?php echo number_format($earnings, 0, ',', '.'); ?></h1>
                            <a>Pendapatan</a>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-mini" id="collapsibleCard">
                        <div class="output2">
                            <h1> <?php echo $total_verified_videos; ?></h1>
                            <a>Video Terverifikasi</a>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-video"></i>
                        </div>
                    </div>
                </div>
                <div class="dashboard-section chart-section">
                    <div>
                        <p class="section-eyebrow">INSIGHT KONTEN</p>
                        <h2>Analisis dan rekomendasi</h2>
                    </div>
                    <span class="section-note">Pantau jangkauan dan temukan ide berikutnya</span>
                </div>
                <div class="card-container2">
                    <div class="card">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-play" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Video Anda</h2>
                                    <p class="card-caption">Tayangan pada setiap video</p>
                                </div>
                            </div>
                        </div>
                        <div class="grafik">
                            <canvas id="barChart"></canvas>
                        </div>
                        <div class="additional-info" id="collapsibleContent">
                            <p id="aiResponse" style="display: none;"><?php echo $hasil ?></p>
                        </div>
                    </div>
                    <div class="card" id="collapsibleCard">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Rekomendasi Ide Video</h2>
                                    <p class="card-caption">Inspirasi untuk konten berikutnya</p>
                                </div>
                            </div>
                        </div>
                        <div class="output">
                            <p><?php echo $hasil ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-container2">
                    <div class="card">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Topik Populer Video Anda</h2>
                                    <p class="card-caption">Topik dengan jangkauan tertinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="grafik">
                            <canvas id="barChart2"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Jumlah Penonton</h2>
                                    <p class="card-caption">Perkembangan tayangan bulanan</p>
                                </div>
                            </div>
                        </div>
                        <div class="grafik">
                            <canvas id="barChart3"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                <strong> <a href="#">Solid Kids</a></strong>
                <div class="float-right d-none d-sm-inline-block">
                    <a href="#"><b>solidkids@gmail.com</b></a>
                </div>
            </footer>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bar Chart untuk Video Analisis
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($title); ?>,
                datasets: [{
                    label: 'Jumlah Tayangan',
                    data: <?php echo json_encode($views); ?>,
                    backgroundColor: 'rgba(82, 103, 223, 0.72)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });

        // Bar Chart untuk Topik Populer
        const ctx = document.getElementById('barChart2').getContext('2d');
        const popularTopicsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($topik); ?>,
                datasets: [{
                    label: 'Jumlah Tayangan',
                    data: <?php echo json_encode($total_views); ?>,
                    backgroundColor: 'rgba(82, 103, 223, 0.72)',
                    borderColor: '#5267df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: { grid: { display: false } }
                }
            }
        });
        const ctx2 = document.getElementById('barChart3').getContext('2d');
        const monthlyViewsChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($data, 'bulan')); ?>,
                datasets: [{
                    label: 'Jumlah Tayangan',
                    data: <?php echo json_encode(array_column($data, 'jumlah_penonton')); ?>,
                    backgroundColor: 'rgba(232, 95, 139, 0.7)',
                    borderColor: '#e85f8b',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: { grid: { display: false } }
                }
            }
        });

    });

    function toggleCard() {
        var card = document.getElementById('collapsibleCard');
        var arrow = document.getElementById('arrow');
        var additionalInfo = document.getElementById('collapsibleContent');
        card.classList.toggle('expanded');
        arrow.classList.toggle('expanded');
        additionalInfo.classList.toggle('hidden');
    }

    function toggleAIResponse() {
        var aiResponse = document.getElementById('aiResponse');
        var toggleButton = document.getElementById('toggleButton');
        if (aiResponse.style.display === "none") {
            aiResponse.style.display = "block";
            toggleButton.innerHTML = "Sembunyikan Rekomendasi AI";
        } else {
            aiResponse.style.display = "none";
            toggleButton.innerHTML = "Tampilkan Rekomendasi AI";
        }
    }
    </script>

</body>

</html>