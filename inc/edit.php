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

  $image = $_POST['edit_image'];
  $imagename = $_FILES['edit_image']['name'];
  $imagetype = $_FILES['edit_image']['type'];
  $imageerror = $_FILES['edit_image']['error'];
  $imagetemp = $_FILES['edit_image']['tmp_name'];

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


  $edit_id = mysqli_real_escape_string($connection, $_REQUEST['edit_id']);
  $edit_name = mysqli_real_escape_string($connection, $_REQUEST['edit_name']);

  if (strlen($imagename) > 0 && strlen($edit_name) > 0) {
    $sql = "UPDATE cars SET Image='$imagename', Name='$edit_name' WHERE ID=$edit_id";
  }
  elseif (strlen($imagename) > 0 && strlen($edit_name) <= 0) {
    $sql = "UPDATE cars SET Image='$imagename' WHERE ID=$edit_id";
  }
  elseif (strlen($imagename) <= 0 && strlen($edit_name) > 0) {
    $sql = "UPDATE cars SET Name='$edit_name' WHERE ID=$edit_id";
  }
  else {
    $sql = "";
  }

  if (mysqli_query($connection, $sql)){
      echo "Das Fahrzeug wurde erfolgreich geändert.";
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