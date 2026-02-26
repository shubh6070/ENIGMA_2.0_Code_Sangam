<?php
/* ===========================
   DB CONNECTION
=========================== */
include "db.php";

/* ===========================
   PAGE CONTROL
=========================== */
$page = $_GET['page'] ?? 'home';

/* ===========================
   ADD CITY (ADMIN FORM)
=========================== */
if (isset($_POST['saveCity'])) {
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $lat  = $_POST['lat'];
    $lon  = $_POST['lon'];

    mysqli_query($conn,
        "INSERT IGNORE INTO tbl_city (city_name, latitude, longitude)
         VALUES ('$city', '$lat', '$lon')"
    );

    header("Location: index.php");
    exit;
}

/* ===========================
   FETCH ALL CITIES
=========================== */
$cities = [];
$result = mysqli_query($conn, "SELECT * FROM tbl_city ORDER BY city_name");

while ($row = mysqli_fetch_assoc($result)) {
    $cities[$row['city_name']] = [
        "lat" => $row['latitude'],
        "lon" => $row['longitude']
    ];
}

/* ===========================
   SELECT CITY
=========================== */
$cityName = $_GET['city'] ?? array_key_first($cities);
$lat = $cities[$cityName]['lat'];
$lon = $cities[$cityName]['lon'];

/* ===========================
   WEATHER API
=========================== */
$url = "https://api.open-meteo.com/v1/forecast?"
     . "latitude=$lat&longitude=$lon"
     . "&current=temperature_2m,relative_humidity_2m,rain";

$data = json_decode(@file_get_contents($url), true);

$temperature = $data['current']['temperature_2m'] ?? 0;
$humidity    = $data['current']['relative_humidity_2m'] ?? 0;
$rainfall    = $data['current']['rain'] ?? 0;

/* ===========================
   SDG-11 SCORE
=========================== */
$sdgScore = round(((100-$temperature)+(100-$humidity)+(100-$rainfall))/3);
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
header{
    background:white;
    padding:18px 32px;
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
    padding:12px 20px;
    border-radius:30px;
    border:none;
    background:#F1F5F9;
    font-weight:600;
}
button{
    padding:12px 26px;
    border-radius:30px;
    border:none;
    background:#0EA5E9;
    color:white;
    font-weight:700;
    cursor:pointer;
}
button:hover{background:#1E40AF;}
#map{
    height:420px;
    margin:30px;
    border-radius:18px;
}
.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    padding:20px 30px;
}
.card{
    background:white;
    padding:25px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
}
.value{
    font-size:2rem;
    font-weight:700;
}
.view3d{
    display:flex;
    justify-content:center;
    padding:40px 0 60px;
}
.view3d-card{
    background:white;
    padding:40px 70px;
    border-radius:24px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
}
.form-box{
    max-width:450px;
    margin:90px auto;
    background:white;
    padding:35px;
    border-radius:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    text-align:center;
}
.form-box input{
    width:100%;
    padding:12px 18px;
    margin-top:15px;
    border-radius:30px;
    border:none;
    background:#F1F5F9;
}
a{text-decoration:none;}
</style>
</head>

<body>

<?php if ($page === 'home') { ?>

<header>
    <div class="logo">🌍 Resilient City Simulator</div>

    <form method="GET">
        <select name="city" onchange="this.form.submit()">
            <?php foreach($cities as $name=>$v){ ?>
                <option value="<?= $name ?>" <?= $name==$cityName?'selected':'' ?>>
                    <?= $name ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <a href="index.php?page=add"><button>➕ Add City</button></a>
</header>

<div id="map"></div>

<section class="dashboard">
    <div class="card"><h3>Temperature</h3><div class="value"><?= $temperature ?> °C</div></div>
    <div class="card"><h3>Humidity</h3><div class="value"><?= $humidity ?> %</div></div>
    <div class="card"><h3>Rainfall</h3><div class="value"><?= $rainfall ?> mm</div></div>
    <div class="card"><h3>SDG-11 Score</h3><div class="value"><?= $sdgScore ?>/100</div></div>
</section>

<section class="view3d">
    <div class="view3d-card">
        <h2>3D City Digital Twin</h2>
        <p><?= $cityName ?> – Real-time SDG-11 Analysis</p>
        <button onclick="location.href='3d_city.php?city=<?= $cityName ?>'">
            🧍 Open 3D View
        </button>
    </div>
</section>

<script>
var map = L.map('map').setView([<?= $lat ?>, <?= $lon ?>], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([<?= $lat ?>, <?= $lon ?>]).addTo(map)
 .bindPopup("<?= $cityName ?>")
 .openPopup();
</script>

<?php } else { ?>

<div class="form-box">
    <h2>➕ Add New City</h2>
    <form method="POST">
        <input type="text" name="city" placeholder="City Name" required>
        <input type="number" step="any" name="lat" placeholder="Latitude" required>
        <input type="number" step="any" name="lon" placeholder="Longitude" required>
        <button name="saveCity" style="margin-top:20px;width:100%;">Add City</button>
    </form>
    <div style="margin-top:18px;">
        <a href="index.php">⬅ Back</a>
    </div>
</div>

<?php } ?>

</body>
</html>