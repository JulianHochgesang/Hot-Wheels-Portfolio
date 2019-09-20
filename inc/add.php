<?php

  require __DIR__ . '/../../vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::create(__DIR__ . '/../../');
  $dotenv->load();

  $db_host = getenv('DB_HOST');
  $db_user = getenv('DB_USER');
  $db_password = getenv('DB_PASSWORD');
  $db_name = getenv('DB_NAME');

  $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

  if ($connection->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $connection->connect_error);
  }

  $image = $_POST['add_image'];
  $imagename = $_FILES['add_image']['name'];
  $imagetype = $_FILES['add_image']['type'];
  $imageerror = $_FILES['add_image']['error'];
  $imagetemp = $_FILES['add_image']['tmp_name'];

  $imagePath = "img/";

  if(is_uploaded_file($imagetemp)) {
      if(move_uploaded_file($imagetemp, $imagePath . $imagename)) {
          echo "Das Bild wurde erfolgreich hochgeladen.<br />";
      }
      else {
          echo "Das Bild konnte nicht verschoben werden.<br />";
      }
  }
  else {
      echo "Das Bild konnte nicht hochgeladen werden.<br />";
  }


  $add_name = mysqli_real_escape_string($connection, $_REQUEST['add_name']);

  $sql = "INSERT INTO cars (Image, Name) VALUES ('$imagename', '$add_name')";
  if (mysqli_query($connection, $sql)){
      echo "Das Fahrzeug wurde erfolgreich angelegt.";
  } else {
      echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
  }

  mysqli_close($connection);

?>

<html>
  <head>
    <meta http-equiv="refresh" content="0; url=/">
  </head>
</html>