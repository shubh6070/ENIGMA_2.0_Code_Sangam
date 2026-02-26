<?php
/* ===========================
   CITY COORDINATES
=========================== */
$cities = [
    "Pune"   => ["lat"=>18.5204, "lon"=>73.8567],
    "Mumbai" => ["lat"=>19.0760, "lon"=>72.8777],
    "Nagpur" => ["lat"=>21.1458, "lon"=>79.0882]
];

$cityName = $_GET['city'] ?? "Pune";
$lat = $cities[$cityName]['lat'];
$lon = $cities[$cityName]['lon'];

/* ===========================
   OPEN-METEO API
=========================== */
$url = "https://api.open-meteo.com/v1/forecast?"
     . "latitude=$lat&longitude=$lon"
     . "&current=temperature_2m,relative_humidity_2m,rain";

$data = json_decode(@file_get_contents($url), true);

/* SAFE DATA */
$temperature = $data['current']['temperature_2m'] ?? 0;
$humidity    = $data['current']['relative_humidity_2m'] ?? 0;
$rainfall    = $data['current']['rain'] ?? 0;

/* SDG-11 SCORE */
$sdgScore = round(
    ((100 - $temperature) + (100 - $humidity) + (100 - $rainfall)) / 3
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Resilient City Simulator | SDG-11</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#E0F2FE,#DBEAFE);
}

/* HEADER */
header{
    background:white;
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 20px rgba(0,0,0,0.1);
}

.logo{
    font-size:1.6rem;
    font-weight:800;
    color:#1E40AF;
}

select{
    padding:10px 20px;
    border-radius:25px;
    border:none;
    font-weight:600;
}

/* MAP */
#map{
    height:420px;
    margin:30px;
    border-radius:15px;
}

/* DASHBOARD */
.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    padding:20px 30px;
}

.card{
    background:white;
    padding:25px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
}

.value{
    font-size:2rem;
    font-weight:700;
}

/* 3D VIEW */
.view3d{
    display:flex;
    justify-content:center;
    padding:40px 0;
}

.view3d-card{
    background:white;
    padding:40px 70px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
}

.view3d-card h2{
    color:#1E40AF;
}

.view3d-card button{
    margin-top:20px;
    padding:14px 36px;
    border:none;
    border-radius:30px;
    font-size:1rem;
    font-weight:700;
    cursor:pointer;
    background:#0EA5E9;
    color:white;
}

.view3d-card button:hover{
    background:#1E40AF;
}

/* FOOTER */
footer{
    background:#1E40AF;
    color:white;
    padding:25px;
    text-align:center;
}
</style>
</head>

<body>

<header>
    <div class="logo">🌍 Resilient City Simulator</div>

    <form method="GET">
        <select name="city" onchange="this.form.submit()">
            <?php foreach($cities as $name => $v){ ?>
                <option value="<?= $name ?>" <?= $name==$cityName?'selected':'' ?>>
                    <?= $name ?>
                </option>
            <?php } ?>
        </select>
    </form>
</header>

<div id="map"></div>

<section class="dashboard">
    <div class="card">
        <h3>Temperature</h3>
        <div class="value"><?= $temperature ?> °C</div>
    </div>
    <div class="card">
        <h3>Humidity</h3>
        <div class="value"><?= $humidity ?> %</div>
    </div>
    <div class="card">
        <h3>Rainfall</h3>
        <div class="value"><?= $rainfall ?> mm</div>
    </div>
    <div class="card">
        <h3>SDG-11 Score</h3>
        <div class="value"><?= $sdgScore ?>/100</div>
    </div>
</section>

<section class="view3d">
    <div class="view3d-card">
        <h2>3D City Digital Twin</h2>
        <p>View SDG-11 analysis for <b><?= $cityName ?></b></p>
        <button onclick="open3DView()">🧍 Open 3D View</button>
    </div>
</section>

<footer>
    © 2026 Resilient City Simulator | SDG-11 Digital Twin
</footer>

<script>
var map = L.map('map').setView([<?= $lat ?>, <?= $lon ?>], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'© OpenStreetMap'
}).addTo(map);

L.marker([<?= $lat ?>, <?= $lon ?>])
 .addTo(map)
 .bindPopup("<?= $cityName ?>")
 .openPopup();

/* CITY-WISE 3D ROUTING */
function open3DView(){
    const city = "<?= $cityName ?>";

    if(city === "Nagpur"){
        window.location.href = "nagpur_3d.php";
    } else if(city === "Pune"){
        window.location.href = "pune_3d.php";
    } else if(city === "Mumbai"){
        window.location.href = "mumbai_3d.php";
    }
}
</script>

</body>
</html>