<?php

//Calls global header and apiKeys for base URL's and arrays
require_once 'shared/header.php';
require_once 'shared/apiKeys.php';

?> 

<!--WEBPAGE INTERFACE-->
    <main class="page">
        <div class="banner">
            <div class="searchDiv">
                <form method="POST" action="results.php">
                    <div class="searchDiv__field">
                        <label for="searchInput">Search For a Recipe </label>
                        <div class="searchDiv__field_bar">
                            <input type="text" name="searchInput" id="searchInput" placeholder="E.g Burgers, Pasta, Salad..."/>
                            <button type="submit" class="searchBtn" name="searchSubmit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <!-- <?= $err ?> -->
                <p>Search for a recipe and a lofi youtube video will automatically play to set the mood while you cook.<br><br>
                    The length of the of video will match within 10 minutes of the expected time it will take to complete the recipe.
                </p>
            </div>
        </div>
    </main>

<?php

//Calls global footer
require_once 'shared/footer.php';

?>