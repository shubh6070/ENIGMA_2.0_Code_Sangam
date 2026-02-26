<?php
/* ===========================
   DB CONNECTION
=========================== */
include "db.php";

/* ===========================
   FORM SUBMIT HANDLER
=========================== */
if (isset($_POST['addCity'])) {

    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $lat  = $_POST['lat'];
    $lon  = $_POST['lon'];

    if (!empty($city) && !empty($lat) && !empty($lon)) {

        $query = "INSERT IGNORE INTO tbl_city (city_name, latitude, longitude)
                  VALUES ('$city', '$lat', '$lon')";

        mysqli_query($conn, $query);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add City | SDG-11</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#E0F2FE,#DBEAFE);
}
.form-box{
    max-width:450px;
    margin:120px auto;
    background:white;
    padding:35px;
    border-radius:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    text-align:center;
}
.form-box h2{
    margin-bottom:20px;
}
.form-box input{
    width:100%;
    padding:12px 18px;
    margin-top:15px;
    border-radius:30px;
    border:none;
    background:#F1F5F9;
}
button{
    margin-top:20px;
    width:100%;
    padding:12px;
    border-radius:30px;
    border:none;
    background:#0EA5E9;
    color:white;
    font-weight:700;
    cursor:pointer;
}
button:hover{
    background:#1E40AF;
}
a{
    display:block;
    margin-top:18px;
    text-decoration:none;
    color:#1E40AF;
    font-weight:600;
}
</style>
</head>

<body>

<div class="form-box">
    <h2>➕ Add New City</h2>

    <form method="POST">
        <input type="text" name="city" placeholder="City Name" required>
        <input type="number" step="any" name="lat" placeholder="Latitude" required>
        <input type="number" step="any" name="lon" placeholder="Longitude" required>

        <button type="submit" name="addCity">Add City</button>
    </form>

    <a href="index.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>