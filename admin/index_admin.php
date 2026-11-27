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

    <title>Dashboard Admin | Solid Kids</title>
</head>

<body>
    <div class="container">
        <?php include 'sidebaradmin.php';?>
        <div class="content">
            <div class="header dashboard-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <p class="dashboard-overline">OVERVIEW ADMIN</p>
                        <h1 class="m-0">Dashboard</h1>
                        <p class="page-subtitle">Ringkasan aktivitas konten dan pengguna Solid Kids.</p>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <div class="dashboard-actions">
                            <span class="role-badge"><i class="fa-solid fa-shield-halved"></i> Area Administrasi</span>
                            <a class="dashboard-cta" href="../mahasiswa/cek_konten.php">
                                <span>Tinjau konten</span>
                                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div><!-- /.col -->
                </div>
            </div>
            <div class="main dashboard-main">
                <div class="dashboard-section">
                    <div>
                        <p class="section-eyebrow">DATA &amp; ANALITIK</p>
                        <h2>Ringkasan platform</h2>
                    </div>
                    <span class="section-note"><i class="fa-solid fa-chart-line" aria-hidden="true"></i> Performa terbaru</span>
                </div>
                <div class="card-container">
                    <div class="card">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-user-check" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Status Verifikasi Konten Kreator</h2>
                                    <p class="card-caption">Status akun yang terdaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="grafik">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="judul">
                            <div class="chart-heading">
                                <span class="chart-icon"><i class="fa-solid fa-clapperboard" aria-hidden="true"></i></span>
                                <div>
                                    <h2>Jumlah Video per Hari</h2>
                                    <p class="card-caption">Aktivitas unggah konten</p>
                                </div>
                            </div>
                        </div>
                        <div class="grafik">
                            <canvas id="barChart"></canvas>
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
        document.addEventListener("DOMContentLoaded", function() {
            fetch('char_data.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Data grafik gagal dimuat.');
                    }
                    return response.json();
                })
                .then(data => {
                    new Chart(document.getElementById('pieChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Terverifikasi', 'Belum diverifikasi'],
                            datasets: [{
                                data: [data.pieChart.Verified, data.pieChart['Not Verified']],
                                backgroundColor: ['#5267df', '#f0b547'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            },
                            cutout: '62%'
                        }
                    });

                    new Chart(document.getElementById('barChart'), {
                        type: 'line',
                        data: {
                            labels: data.barChart.labels,
                            datasets: data.barChart.datasets.map(dataset => ({
                                ...dataset,
                                borderColor: '#5267df',
                                backgroundColor: 'rgba(82, 103, 223, 0.14)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 4
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                })
                .catch(error => {
                    document.querySelectorAll('.grafik').forEach(chartArea => {
                        chartArea.classList.add('chart-error');
                        chartArea.textContent = 'Data grafik tidak dapat dimuat. Silakan coba lagi nanti.';
                    });
                    console.error('Gagal memuat grafik dashboard:', error);
                });
        });
    </script>
</body>
</html>