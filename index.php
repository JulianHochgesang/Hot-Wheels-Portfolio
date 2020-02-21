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

  // Create tables if they don't exist
  $createCarsSql = "CREATE TABLE cars (
    ID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Image VARCHAR(255) NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Category VARCHAR(255) NOT NULL
  )";
  if ($mainResult === FALSE) {
    $connection->query($createCarsSql);
  }
  $createCategoriesSql = "CREATE TABLE categories (
    ID INT(11) AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(255) NOT NULL
  )";
  if ($categoryResult === FALSE) {
    $connection->query($createCategoriesSql);
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

  // Category filter SQL query
  $categorySet = $_POST['category_string'];
  if (strlen($categorySet) > 0) {
    $category_name_by_id_query = "SELECT CategoryName FROM categories WHERE ID=$categorySet";
    $category_name_by_id_result = $connection->query($category_name_by_id_query);
    $cnb_id_row = mysqli_fetch_array($category_name_by_id_result);
    $category_name_by_id = $cnb_id_row['CategoryName'];
    $categorySql = "SELECT * FROM categories WHERE (ID='".$categorySet."')";
    $categorySearchSql = "SELECT * FROM cars WHERE (Category='".$category_name_by_id."')";
    $result = $connection->query($categorySearchSql);
  }
  else {
    $categorySql = "SELECT * FROM categories ORDER BY CategoryName";
  }
  $categoryResult = $connection->query($categorySql);

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
          <div class="link_wrap add_car">
            <p>Fahrzeug hinzufügen</p>
            <a href="#" id="add_car"><span class="ri ri-plus-circle"></span></a>
          </div>
          <div class="input_wrap search_car">
            <p>Fahrzeuge finden</p>
            <form method="post">
              <input type="text" name="search_string" id="search_string" placeholder="Suchbegriff ...">
              <button type="submit" id="search_button"><span class="ri ri-search"></span></button>
            </form>
            <?php if (strlen($searchWord) > 0) : ?>
              <form method="post">
                <input type="hidden" name="search_string" id="search_string" value="">
                <button type="submit" id="search_reset_button">Suche zurücksetzen</button>
              </form>
            <?php endif;?>
          </div>
          <div class="input_wrap categories">
            <p>Kategorien</p>
            <form enctype="multipart/form-data" action="/inc/category.php" method="post">
              <input type="text" name="category_name" id="category_name" placeholder="Neue Kategorie ...">
              <button type="submit" id="category_button"><span class="ri ri-tag"></span></button>
            </form>
            <?php if ($categoryResult->num_rows > 0) : ?>
              <div class="categories_list">
                <?php while($rowC = $categoryResult->fetch_assoc()) : ?>
                  <div class="category-single">
                    <form method="post" class="cl-form-1">
                      <input type="hidden" name="category_string" id="category_string" value="<?php echo $rowC["ID"]; ?>">
                      <button type="submit" class="category_button"><?php echo $rowC["CategoryName"]; ?></button>
                    </form>
                    <form method="post" enctype="multipart/form-data" action="/inc/category.php" class="cl-form-2">
                      <input type="hidden" name="category_delete_id" id="category_delete_id" value="<?php echo $rowC["ID"]; ?>">
                      <button type="submit" class="category_delete_button"><span class="ri ri-trash"></span></button>
                    </form>
                  </div>
                <?php
                  $categories_for_form .= '<option value="'.$rowC["CategoryName"].'">'.$rowC["CategoryName"].'</option>';
                  endwhile;
                ?>
                <?php if (strlen($categorySet) > 0) : ?>
                  <form method="post">
                    <input type="hidden" name="category_string" id="category_string" value="">
                    <button type="submit" id="category_reset_button">Auswahl zurücksetzen</button>
                  </form>
                <?php endif;?>
              </div>
            <?php endif;?>
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
            <div class="fahrzeug" fahrzeugid="<?php echo $row["ID"]; ?>">
              <div class="fahrzeug-inner">
                <div class="fahrzeug-image">
                  <?php if (strlen($row["Category"]) > 0) : ?>
                    <div class="fahrzeug-category"><?php echo $row["Category"]; ?></div>
                  <?php endif; ?>
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
          <?php if ($categoryResult->num_rows > 0) : ?>
            <label>Kategorie:</label>
            <div class="modal-select-outer">
              <select name="add_category" id="add_category">
                <option value="null">Keine Kategorie</option>
                <?php echo $categories_for_form; ?>
              </select>
              <span class="ri ri-arrow-down"></span>
            </div>
          <?php endif;?>
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
          <?php if ($categoryResult->num_rows > 0) : ?>
            <label>Kategorie:</label>
            <div class="modal-select-outer">
              <select name="edit_category" id="edit_category">
                <option value="null">Keine Kategorie</option>
                <?php echo $categories_for_form; ?>
              </select>
              <span class="ri ri-arrow-down"></span>
            </div>
          <?php endif;?>
          <input type="submit" value="Fahrzeug speichern">
          <div class="form_button" id="edit_abort">Abbrechen</div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="/inc/js/lightbox.min.js"></script>
    <script src="/inc/js/main.js"></script>
  </body>
</html>