<?php
/* ===========================
   INPUT DATA
=========================== */
$city  = $_GET['city'] ?? "Unknown City";
$score = intval($_GET['score'] ?? 0);

/* ===========================
   SENTINEL-2 NDVI (DERIVED)
   Range: 0.2 – 0.8
=========================== */
$ndvi = round(mt_rand(30,70)/100,2); // simulated Sentinel-2 NDVI
$greenCover = round($ndvi * 100);

/* ===========================
   LIVING STATUS LOGIC
=========================== */
if ($score >= 80) {
    $status = "Excellent";
    $msg = "High-quality housing, strong infrastructure, low pollution, and sustainable urban development.";
    $color = "#16a34a";
}
elseif ($score >= 60) {
    $status = "Good";
    $msg = "Acceptable living conditions with moderate environmental sustainability.";
    $color = "#22c55e";
}
elseif ($score >= 40) {
    $status = "Moderate";
    $msg = "Improvement needed in green cover, pollution control, and infrastructure.";
    $color = "#f59e0b";
}
else {
    $status = "Poor";
    $msg = "Critical challenges in urban sustainability and human living conditions.";
    $color = "#dc2626";
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $city ?> – SDG-11 Human Living Status</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#E0F2FE,#DBEAFE);
}
.container{
    max-width:800px;
    margin:80px auto;
    background:white;
    padding:50px;
    border-radius:30px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
    text-align:center;
}
.status{
    font-size:2.5rem;
    font-weight:900;
    color:<?= $color ?>;
}
.score{
    margin:15px 0;
    font-size:1.2rem;
}
.ndvi{
    margin-top:25px;
    font-size:1.2rem;
    font-weight:700;
}
canvas{
    margin-top:30px;
}
a{
    display:inline-block;
    margin-top:30px;
    padding:12px 32px;
    background:#1E40AF;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-weight:700;
}
.flash{
    animation:flash 1.5s infinite;
}
@keyframes flash{
    0%{opacity:1}
    50%{opacity:.4}
    100%{opacity:1}
}
</style>
</head>

<body>

<div class="container">
    <h1>🏙 <?= $city ?></h1>

    <div class="status"><?= $status ?></div>

    <div class="score">SDG-11 Score: <b><?= $score ?>/100</b></div>

    <p><?= $msg ?></p>

    <div class="ndvi flash">
        🌳 Sentinel-2 NDVI (Green Cover – 10% Weight):  
        <b><?= $greenCover ?>%</b>
    </div>

    <!-- GRAPH -->
    <canvas id="ndviChart" height="120"></canvas>

    <a href="index.php?city=<?= urlencode($city) ?>">⬅ Back to Dashboard</a>
</div>

<script>
/* REAL-TIME GRAPH (FLASH MODE) */
const ctx = document.getElementById('ndviChart').getContext('2d');

const ndviChart = new Chart(ctx,{
    type:'bar',
    data:{
        labels:['Green Cover (NDVI)'],
        datasets:[{
            label:'NDVI %',
            data:[<?= $greenCover ?>],
            backgroundColor:'#16a34a'
        }]
    },
    options:{
        animation:{
            duration:1200
        },
        scales:{
            y:{
                min:0,
                max:100
            }
        }
    }
});

/* AUTO UPDATE (SIMULATED REAL-TIME) */
setInterval(()=>{
    let val = Math.floor(Math.random()*20)+40;
    ndviChart.data.datasets[0].data[0]=val;
    ndviChart.update();
},3000);
</script>

</body>
</html>