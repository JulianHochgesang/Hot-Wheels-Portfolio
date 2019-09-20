<?php

  // Credentials
  require __DIR__ . '/../vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
  $dotenv->load();

  $db_host = getenv('DB_HOST');
  $db_user = getenv('DB_USER');
  $db_password = getenv('DB_PASSWORD');
  $db_name = getenv('DB_NAME');

  // Main connection
  $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

  if ($connection->connect_error) {
    die("Verbindung zur Datenbank ist fehlgeschlagen: " . $connection->connect_error);
  }

  // Main SQL query (fallback / counter)
  $mainSql = "SELECT * FROM cars ORDER BY Name";
  $mainResult = $connection->query($mainSql);
  $numberOfRows = mysqli_num_rows($mainResult);

  // Create table if it doesn't exist
  $createSql = "CREATE TABLE cars (
    ID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Image VARCHAR(255) NOT NULL,
    Name VARCHAR(255) NOT NULL
  )";
  if ($mainResult === FALSE) {
    $connection->query($createSql);
  }

  // Search SQL query
  $searchWord = $_POST['search_string'];
  if (strlen($searchWord) > 0) {
    $sql = "SELECT * FROM cars WHERE (Name LIKE '%".$searchWord."%') ORDER BY Name";
  }
  else {
    $sql = "SELECT * FROM cars ORDER BY Name";
  }
  $result = $connection->query($sql);  

?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hot Wheels Portfolio</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet">
    <link href="/assets/Icons/style.css" type="text/css" rel="stylesheet" />
    <link href="/inc/css/style.css" type="text/css" rel="stylesheet" />
    <link rel="shortcut icon" href="/assets/favicon.png" type="image/png" />
    <link rel="icon" href="/assets/favicon.png" type="image/png" />
  </head>
  <body>
    <div class="sidebar">
      <div class="sidebar-inner">
        <div class="logo">
          <img src="/assets/Hot_wheels_logo.svg" />
        </div>
        <div class="navigation">
          <div class="link_wrap">
            <a href="#" id="add_car"><span class="ri ri-plus-circle"></span></a>
          </div>
          <div class="input_wrap">
            <form method="post">
              <input type="text" name="search_string" id="search_string" placeholder="Suchbegriff ...">
              <button type="submit" id="search_button"><span class="ri ri-search"></span></button>
            </form>
            <form method="post">
              <input type="hidden" name="search_string" id="search_string" value="">
              <button type="submit" id="search_reset_button">Suche zurücksetzen</button>
            </form>
          </div>
          <?php if ($mainResult->num_rows > 0) : ?>
            <div class="info_cars">
              <span><?php echo $numberOfRows; ?></span>
              <p>Fahrzeuge</p>
            </div>
          <?php endif;?>
        </div>
        <div class="copyright"><a href="http://julianhochgesang.de" target="_blank">© <?php echo date("Y"); ?>, Julian Hochgesang</a></div>
      </div>
    </div>

    <div class="content">
      <div class="content-inner">
        <?php if ($result->num_rows > 0) : ?>
          <?php while($row = $result->fetch_assoc()) : ?>
            <div class="fahrzeug">
              <div class="fahrzeug-inner">
                <div class="fahrzeug-image">
                  <div class="fahrzeug-id"><?php echo $row["ID"]; ?></div>
                  <div class="fahrzeug-delete">
                    <form action="/inc/delete.php" method="post" onsubmit="return confirm('Möchtest du das Fahrzeug wirklich löschen?');">
                      <input type="hidden" id="delete_car_id" name="delete_car_id" value="<?php echo $row["ID"]; ?>">
                      <button type="submit" id="trash_button"><span class="ri ri-trash"></span></button>
                    </form>
                  </div>
                  <div class="fahrzeug-edit">
                    <a href="#" id="edit_car"><span class="ri ri-pencil"></span></a>
                  </div>
                  <a class="lightbox-hwp" href="<?php echo "img/" . $row["Image"]; ?>" data-lightbox="lightbox-group" title="<?php echo $row["Name"]; ?>">
                    <img src="<?php echo "img/" . $row["Image"]; ?>" alt="<?php echo $row["Name"]; ?>" />
                  </a>
                </div>
                <p><b><?php echo $row["Name"]; ?></b></p>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <?php echo "In der Datenbank befinden sich zurzeit leider keine Fahrzeuge"; ?>
        <?php endif; $connection->close(); ?>
      </div>
    </div>

    <div class="modalbox" id="modal_add">
      <div class="modal_inner">
        <h3>Ein Fahrzeug hinzufügen</h3>
        <form enctype="multipart/form-data" action="/inc/add.php" method="post">
          <input type="file" accept="image/*" name="add_image" id="add_image">
          <input type="text" name="add_name" id="add_name" placeholder="Name des Fahrzeugs">
          <input type="submit" value="Fahrzeug hinzufügen">
          <div class="form_button" id="add_abort">Abbrechen</div>
        </form>
      </div>
    </div>

    <div class="modalbox" id="modal_edit">
      <div class="modal_inner">
        <h3>Ein Fahrzeug bearbeiten</h3>
        <p>ID: <span id="edit_id_span"></span></p>
        <form enctype="multipart/form-data" action="/inc/edit.php" method="post">
          <input type="hidden" name="edit_id" id="edit_id">
          <input type="file" accept="image/*" name="edit_image" id="edit_image">
          <input type="text" name="edit_name" id="edit_name" placeholder="Name des Fahrzeugs">
          <input type="submit" value="Fahrzeug speichern">
          <div class="form_button" id="edit_abort">Abbrechen</div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="/inc/js/hammer.min.js"></script>
    <script src="/inc/js/jquery.scrolllock.js"></script>
    <script src="/inc/js/lightbox.js"></script>
    <script src="/inc/js/main.js"></script>
  </body>
</html>