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

  $category_name = mysqli_real_escape_string($connection, $_REQUEST['category_name']);
  $category_delete_id = mysqli_real_escape_string($connection, $_REQUEST['category_delete_id']);

  if (strlen($category_name) > 0) {
    $sql = "INSERT INTO categories (CategoryName) SELECT '$category_name' FROM DUAL WHERE NOT EXISTS(SELECT 1 FROM categories WHERE CategoryName = '$category_name') LIMIT 1;";
    if (mysqli_query($connection, $sql)){
        echo "Die Kategorie wurde erfolgreich angelegt.";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
    }
  }
  else {
    $category_name_by_id_query = "SELECT CategoryName FROM categories WHERE ID=$category_delete_id";
    $category_name_by_id_result = $connection->query($category_name_by_id_query);
    $cnb_id_row = mysqli_fetch_array($category_name_by_id_result);
    $category_name_by_id = $cnb_id_row['CategoryName'];
    $sql = "DELETE FROM categories WHERE ID=$category_delete_id";
    $sql2 = "UPDATE cars SET Category=NULL WHERE Category='$category_name_by_id'";
    if (mysqli_query($connection, $sql)){
        echo "Die Kategorie wurde erfolgreich gelöscht.";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
    }
    if (mysqli_query($connection, $sql2)){
      echo "<br />Die Kategorie wurde erfolgreich von den Fahrzeugen entfernt.";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
    }
  }

  mysqli_close($connection);

?>

<html>
  <head>
    <meta http-equiv="refresh" content="0; url=/">
  </head>
</html>