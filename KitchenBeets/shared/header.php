<!--WEBPAGE INTERFACE-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <title>Kitchen Beets</title>
        <script src="https://kit.fontawesome.com/2956115494.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="css/global.css" />
        <?php
        //Allows for custom css files to be linked to each inidividual page
            if (isset($css) && is_array($css))
            foreach ($css as $path)
                printf('<link rel="stylesheet" type="text/css" href="%s" />', $path);
        ?>
        <link rel="shortcut icon" href="images/favicon.ico" />
    </head>
    <body>
    <header>
        <div class="headerDiv">
            <div class="logo">
                <img class="logoImg" src="images/earphones.jpeg" />
                <a href="index.php"><h1>Kitchen Beets</h1></a>
                <img class="logoImg" id="fade" src="images/burger.jpeg" />
            </div>
            <form method="POST" action="results.php">
                <div class="headerDiv__field">
                    <div class="headerDiv__field_bar">
                        <input type="text" name="searchInput" id="searchInput" placeholder="Search..."/>
                        <button type="submit" class="miniSearchBtn" name="searchSubmit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </header>