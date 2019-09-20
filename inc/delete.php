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

  $id = $_POST['delete_car_id'];

  $sql = "DELETE FROM cars WHERE ID=$id";
  if (mysqli_query($connection, $sql)){
      echo "Das Fahrzeug wurde erfolgreich gelöscht.";
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