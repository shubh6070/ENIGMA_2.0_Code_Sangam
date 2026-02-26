<?php
/* ===========================
   GET DATA
=========================== */
$city  = $_GET['city']  ?? "Unknown City";
$score = intval($_GET['score'] ?? 0);

/* ===========================
   LIVING STATUS LOGIC
=========================== */
if ($score >= 80) {
    $status = "Excellent";
    $msg = "The city provides high-quality housing, clean environment, good infrastructure, and sustainable urban development.";
    $color = "#16a34a";
}
elseif ($score >= 60) {
    $status = "Good";
    $msg = "The city has acceptable living conditions with moderate environmental sustainability and infrastructure.";
    $color = "#22c55e";
}
elseif ($score >= 40) {
    $status = "Moderate";
    $msg = "The city requires improvement in housing quality, pollution control, and basic urban facilities.";
    $color = "#f59e0b";
}
else {
    $status = "Poor";
    $msg = "The city faces serious challenges in urban sustainability, infrastructure, and living standards.";
    $color = "#dc2626";
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $city ?> – Human Living Status (SDG-11)</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#E0F2FE,#DBEAFE);
}
.container{
    max-width:700px;
    margin:120px auto;
    background:white;
    padding:50px;
    border-radius:30px;
    box-shadow:0 15px 40px rgba(0,0,0,0.15);
    text-align:center;
}
h1{
    margin-bottom:10px;
}
.status{
    font-size:2.6rem;
    font-weight:900;
    color:<?= $color ?>;
    margin:20px 0;
}
.score{
    font-size:1.3rem;
    margin-bottom:20px;
}
p{
    font-size:1.15rem;
    line-height:1.7;
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
</style>
</head>

<body>

<div class="container">
    <h1>🏙 <?= $city ?></h1>
    <div class="status"><?= $status ?></div>
    <div class="score">SDG-11 Score: <b><?= $score ?>/100</b></div>
    <p><?= $msg ?></p>

    <a href="index.php?city=<?= urlencode($city) ?>">⬅ Back to Dashboard</a>
</div>

</body>
</html>