<?php
/* ===========================
   DB CONNECTION
=========================== */
include "db.php";

/* ===========================
   GET CITY
=========================== */
$city = $_GET['city'] ?? "Nagpur";

/* ===========================
   FETCH CITY COORDINATES
=========================== */
$stmt = mysqli_prepare(
    $conn,
    "SELECT latitude, longitude FROM tbl_city WHERE city_name = ?"
);
mysqli_stmt_bind_param($stmt, "s", $city);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $lat = $row['latitude'];
    $lon = $row['longitude'];
} else {
    $city = "Nagpur";
    $lat  = 21.1458;
    $lon  = 79.0882;
}

/* ===========================
   WEATHER API (OPEN-METEO)
=========================== */
$weatherUrl =
"https://api.open-meteo.com/v1/forecast?" .
"latitude=$lat&longitude=$lon" .
"&current=temperature_2m,relative_humidity_2m,rain,precipitation,cloud_cover,pressure_msl";

$weather = json_decode(@file_get_contents($weatherUrl), true);

$temp       = $weather['current']['temperature_2m'] ?? 0;
$humidity   = $weather['current']['relative_humidity_2m'] ?? 0;
$rain       = $weather['current']['rain'] ?? 0;
$precip     = $weather['current']['precipitation'] ?? 0;
$cloudCover = $weather['current']['cloud_cover'] ?? 0;
$pressure   = $weather['current']['pressure_msl'] ?? 1013;

/* ===========================
   AIR QUALITY API
=========================== */
$airUrl =
"https://air-quality-api.open-meteo.com/v1/air-quality?" .
"latitude=$lat&longitude=$lon&current=us_aqi";

$air = json_decode(@file_get_contents($airUrl), true);
$aqi = $air['current']['us_aqi'] ?? 0;

/* ===========================
   AQI HEALTH STATUS
=========================== */
if ($aqi <= 50)        $health = "Good (Safe)";
elseif ($aqi <= 100)  $health = "Moderate";
elseif ($aqi <= 150)  $health = "Unhealthy (Sensitive)";
elseif ($aqi <= 200)  $health = "Unhealthy";
else                  $health = "Very Unhealthy";

/* ===========================
   SDG-11 SCORE CALCULATION
=========================== */
$tempScore     = max(0, min(100, 100 - abs($temp - 25) * 4));
$humidityScore = max(0, min(100, 100 - abs($humidity - 50) * 2));
$rainScore     = max(0, min(100, 100 - ($precip * 5)));
$cloudScore    = max(0, min(100, 100 - abs($cloudCover - 50)));
$aqiScore      = max(0, min(100, 100 - $aqi));
$pressureScore = max(0, min(100, 100 - abs($pressure - 1013)));

$sdgScore = round(
    ($tempScore * 0.20) +
    ($humidityScore * 0.15) +
    ($rainScore * 0.15) +
    ($cloudScore * 0.10) +
    ($aqiScore * 0.25) +
    ($pressureScore * 0.15)
);

/* ===========================
   ✅ ONLY CHANGE: 3D CITY COLOR
=========================== */
if ($sdgScore >= 70) {
    $cityColor = 0x10B981; // GREEN
} elseif ($sdgScore >= 60) {
    $cityColor = 0x3B82F6; // BLUE
} elseif ($sdgScore >= 50) {
    $cityColor = 0xFB923C; // ORANGE
} else {
    $cityColor = 0xEF4444; // RED
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $city ?> – SDG-11 3D Digital Twin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://unpkg.com/three@0.158.0/build/three.min.js"></script>

<style>
body{
    margin:0;
    overflow:hidden;
    font-family:'Segoe UI',sans-serif;
    background:#020617;
    color:#e5e7eb;
}
.panel{
    position:absolute;
    top:20px;
    left:20px;
    width:260px;
    background:rgba(255,255,255,0.12);
    padding:22px;
    border-radius:20px;
    backdrop-filter:blur(14px);
}
.panel h3{color:#38bdf8;margin-bottom:12px}
.panel button{
    width:100%;
    margin:6px 0;
    padding:10px;
    border:none;
    border-radius:12px;
    font-weight:700;
    background:#0ea5e9;
    color:white;
    cursor:pointer;
}
.info{
    position:absolute;
    top:20px;
    right:20px;
    max-width:360px;
    background:rgba(255,255,255,0.15);
    padding:22px;
    border-radius:20px;
    display:none;
}
.back{
    position:absolute;
    bottom:30px;
    left:50%;
    transform:translateX(-50%);
    padding:12px 32px;
    background:#22c55e;
    color:white;
    border-radius:30px;
    text-decoration:none;
    font-weight:800;
}
</style>
</head>

<body>

<div class="panel">
    <h3>🏙 <?= $city ?> Digital Twin</h3>
    <button onclick="showTemp()">🌡 Temperature</button>
    <button onclick="showHumidity()">💧 Humidity</button>
    <button onclick="showWeather()">🌦 Weather</button>
    <button onclick="showAir()">🫁 Air Quality</button>
    <button onclick="showSDG()">📊 SDG-11 Score</button>
    <button onclick="openLivingStatus()">🏠 Human Living Status</button>
</div>

<div class="info" id="infoBox"></div>
<a class="back" href="index.php?city=<?= urlencode($city) ?>">⬅ Back</a>

<script>
const temp = <?= $temp ?>;
const humidity = <?= $humidity ?>;
const rain = <?= $rain ?>;
const precip = <?= $precip ?>;
const cloud = <?= $cloudCover ?>;
const pressure = <?= $pressure ?>;
const aqi = <?= $aqi ?>;
const health = "<?= $health ?>";
const sdg = <?= $sdgScore ?>;
const buildingColor = <?= $cityColor ?>;

function show(text){
    const box = document.getElementById("infoBox");
    box.innerHTML = text;
    box.style.display = "block";
}
function showTemp(){ show(`🌡 ${temp} °C`); }
function showHumidity(){ show(`💧 ${humidity} %`); }
function showWeather(){
    show(`🌧 Rain: ${rain} mm<br>☁ Cloud: ${cloud}%<br>🌬 Pressure: ${pressure} hPa`);
}
function showAir(){ show(`🫁 AQI: ${aqi}<br>Health: ${health}`); }
function showSDG(){ show(`<b>📊 SDG-11 Score: ${sdg}/100</b>`); }
function openLivingStatus(){
    location.href = "sdg11_living_status.php?city=<?= urlencode($city) ?>&score=" + sdg;
}

/* THREE.JS 3D CITY */
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x020617);

const camera = new THREE.PerspectiveCamera(70, innerWidth/innerHeight, 0.1, 1000);
camera.position.set(0,12,20);

const renderer = new THREE.WebGLRenderer({antialias:true});
renderer.setSize(innerWidth,innerHeight);
document.body.appendChild(renderer.domElement);

scene.add(new THREE.AmbientLight(0xffffff,0.6));
const light = new THREE.DirectionalLight(0xffffff,1);
light.position.set(10,15,10);
scene.add(light);

const ground = new THREE.Mesh(
    new THREE.PlaneGeometry(60,60),
    new THREE.MeshStandardMaterial({color:0x1e293b})
);
ground.rotation.x = -Math.PI/2;
scene.add(ground);

/* BUILDINGS – COLOR CHANGE ONLY */
for(let x=-15;x<=15;x+=5){
    for(let z=-15;z<=15;z+=5){
        const h = Math.random()*2 + 2;
        const b = new THREE.Mesh(
            new THREE.BoxGeometry(2,h,2),
            new THREE.MeshStandardMaterial({ color: buildingColor })
        );
        b.position.set(x,h/2,z);
        scene.add(b);
    }
}

function animate(){
    requestAnimationFrame(animate);
    renderer.render(scene,camera);
}
animate();
</script>

</body>
</html>