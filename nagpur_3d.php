<?php
/* ===========================
   NAGPUR CITY COORDINATES
=========================== */
$city = "Nagpur";
$lat  = 21.1458;
$lon  = 79.0882;

/* ===========================
   REAL-TIME WEATHER (SATELLITE)
=========================== */
$url = "https://api.open-meteo.com/v1/forecast?"
     . "latitude=$lat&longitude=$lon"
     . "&current=temperature_2m,relative_humidity_2m,rain";

$data = json_decode(@file_get_contents($url), true);

/* SAFE DATA */
$temp     = $data['current']['temperature_2m'] ?? 0;
$humidity = $data['current']['relative_humidity_2m'] ?? 0;
$rain     = $data['current']['rain'] ?? 0;

/* ===========================
   SDG-11 ANALYSIS
=========================== */
$heatRisk  = ($temp > 38) ? "High" : (($temp >= 30) ? "Moderate" : "Low");
$floodRisk = ($rain > 10) ? "High" : (($rain >= 3) ? "Moderate" : "Low");

$sdgScore = round(
    ((100 - $temp) + (100 - $humidity) + (100 - $rain)) / 3
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Nagpur – 3D Digital Twin (SDG-11)</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://unpkg.com/three@0.158.0/build/three.min.js"></script>

<style>
body{
    margin:0;
    overflow:hidden;
    font-family:'Segoe UI',sans-serif;
    background:#020617;
    color:white;
}

/* CONTROL PANEL */
.panel{
    position:absolute;
    top:20px;
    left:20px;
    background:rgba(255,255,255,0.15);
    padding:18px 20px;
    border-radius:18px;
    backdrop-filter:blur(10px);
}

.panel h3{
    margin-bottom:10px;
}

.panel button{
    width:190px;
    margin:6px 0;
    padding:10px;
    border:none;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
}

/* INFO DISPLAY */
.info{
    position:absolute;
    top:20px;
    right:20px;
    background:rgba(255,255,255,0.15);
    padding:22px 30px;
    border-radius:18px;
    backdrop-filter:blur(10px);
    display:none;
    font-size:1.2rem;
}

/* BACK BUTTON */
.back{
    position:absolute;
    bottom:25px;
    left:50%;
    transform:translateX(-50%);
    padding:12px 30px;
    background:white;
    color:#1E40AF;
    text-decoration:none;
    border-radius:30px;
    font-weight:700;
}
</style>
</head>

<body>

<!-- CONTROL PANEL -->
<div class="panel">
    <h3>🏙 Nagpur City</h3>
    <button onclick="showTemp()">🌡 Temperature</button>
    <button onclick="showHumidity()">💧 Humidity</button>
    <button onclick="showRain()">🌧 Rainfall</button>
    <button onclick="showSDG()">📊 SDG-11 Score</button>
    <button onclick="openLivingStatus()">🏠 Human Living Status</button>
</div>

<!-- INFO BOX -->
<div class="info" id="infoBox"></div>

<a class="back" href="index.php?city=Nagpur">⬅ Back</a>

<script>
/* DATA FROM PHP */
const temp = <?= $temp ?>;
const humidity = <?= $humidity ?>;
const rain = <?= $rain ?>;
const sdg = <?= $sdgScore ?>;

/* DISPLAY FUNCTIONS */
function showTemp(){
    show(`🌡 Temperature<br><b>${temp} °C</b><br><?= $heatRisk ?> Heat Risk`);
}
function showHumidity(){
    show(`💧 Humidity<br><b>${humidity} %</b>`);
}
function showRain(){
    show(`🌧 Rainfall<br><b>${rain} mm</b><br><?= $floodRisk ?> Flood Risk`);
}
function showSDG(){
    show(`📊 SDG-11 Health Score<br><b>${sdg} / 100</b>`);
}
function show(text){
    const box = document.getElementById("infoBox");
    box.innerHTML = text;
    box.style.display = "block";
}

/* OPEN HUMAN LIVING STATUS PAGE */
function openLivingStatus(){
    window.location.href =
        "sdg11_living_status.php?city=Nagpur&score=" + sdg;
}

/* ===========================
   THREE.JS – 3D CITY MODEL
=========================== */
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x020617);

const camera = new THREE.PerspectiveCamera(
    70, window.innerWidth / window.innerHeight, 0.1, 1000
);
camera.position.set(0, 12, 20);

const renderer = new THREE.WebGLRenderer({ antialias:true });
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

/* LIGHTS */
scene.add(new THREE.AmbientLight(0xffffff, 0.6));
const light = new THREE.DirectionalLight(0xffffff, 1);
light.position.set(10, 15, 10);
scene.add(light);

/* GROUND */
const ground = new THREE.Mesh(
    new THREE.PlaneGeometry(60, 60),
    new THREE.MeshStandardMaterial({ color:0x1e293b })
);
ground.rotation.x = -Math.PI / 2;
scene.add(ground);

/* ROADS */
for(let i = -20; i <= 20; i += 10){
    const road = new THREE.Mesh(
        new THREE.BoxGeometry(40, 0.1, 3),
        new THREE.MeshStandardMaterial({ color:0x334155 })
    );
    road.position.set(0, 0.05, i);
    scene.add(road);
}

/* HOUSES */
for(let x = -15; x <= 15; x += 5){
    for(let z = -15; z <= 15; z += 5){
        const h = Math.random() * 2 + 2;
        const house = new THREE.Mesh(
            new THREE.BoxGeometry(2, h, 2),
            new THREE.MeshStandardMaterial({ color:0x38bdf8 })
        );
        house.position.set(x, h/2, z);
        scene.add(house);
    }
}

/* RENDER LOOP */
function animate(){
    requestAnimationFrame(animate);
    renderer.render(scene, camera);
}
animate();
</script>

</body>
</html>