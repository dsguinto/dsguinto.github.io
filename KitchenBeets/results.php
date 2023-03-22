<?php
session_start();

$css = array("css/results.css"); //Links custom page css file

//Calls global header and apiKeys for base URL's and arrays
require_once 'shared/header.php';
require_once 'shared/apiKeys.php';

//Intialize variables for user displays
$hide = "";
$random = "";

//Changes options on page by adjusting offset value in API request
if(isset($_POST['searchInput'])){
    if($_POST['searchInput'] == ""){
        $hide = "style='display:none;'";
        $random = "<h2>You didn't enter anything, so here are some random recipes!</h2>";
        $_SESSION['searchInput'] = $_POST['searchInput'];
        $_SESSION['offset'] = 0;
    }
    else{
    $_SESSION['searchInput'] = $_POST['searchInput'];
    // $_SESSION['searchInput'] = preg_replace('/\s+/', '+', $_SESSION['searchInput']);
    $_SESSION['offset'] = 0;
    }
}
else{
    $_SESSION['searchInput'] = $_SESSION['searchInput'];
}


//API request to get recipes
$recipeSearchUrl = SPOONACULAR_URL . 'recipes/complexSearch?query=' . $_SESSION['searchInput'] . '&offset=' . $_SESSION['offset'] . '&number=12&apiKey=' . $SPOONACULAR['client_id'];
$recipeSearchUrl = preg_replace('/\s+/', '+', $recipeSearchUrl);
    //curl commands to grab API results
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$recipeSearchUrl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = json_decode(curl_exec($ch), false);
    // var_dump($response);
    curl_close($ch);

    //Initialize/set variables
    $totalResults = $response->totalResults;
    $options = "";

    //Checks total responses, displays error message if applicable
    if ($response->totalResults == 0){
        $options = '<h3>There are no results for this search</h3>';
    }
    else{
        //Loops through each response received from the Spoonacular API request and grabs data to be displayed
        for($i = 0; $i<$response->number; $i++){

        if (!array_key_exists($i, $response->results)){
            break;
        }
        else{
        //Grabs specified values
        $recipeID = $response->results[$i]->id; //Used for another API request to get recipe information
        $recipeTitle = $response->results[$i]->title;
        $recipeImage = $response->results[$i]->image;
        $imageType = $response->results[$i]->imageType;

        //Sets up search results to be displayed
        $options .=    '<div class="recipeOption">
                            <img src="' . $recipeImage . '"/>
                            <form method="POST" action="recipe.php">
                                <input type="hidden" name="recipeID" id="recipeID" value="' . $recipeID . '"/>
                                <button type="submit" class="recipeLink" name="recipeSubmit"> ' . $recipeTitle . '</button>
                            </form>
                        </div>';
        }
        }
    }


    // ------ PAGINATION ---------

    //Hides buttons if at beginning or end of search
    $hidePrevious = "";
    $hideNext = "";

    if($_SESSION['offset'] == 0){
        $hidePrevious = "style='display: none';";
    }
    if($_SESSION['offset'] >= ($totalResults - 12)){
        $hideNext = "style='display: none';";
    }
       
    //If next button is clicked, adds 12 to offset value to display new outputs starting from 12 positions up from the last
    if(isset($_POST['nextPage'])){
        $_SESSION['offset'] += 12;
        header('Location: results.php');
        exit();
    }
       
    //If previous button is clicked, subtracts 12 from offset value to display new outputs starting from 12 positions back from the last
    if(isset($_POST['previousPage'])){
        $_SESSION['offset'] -= 12;
        header('Location: results.php');
        exit();
    }
       

//Checks if one of the recipes is selected and send an API request to get the recipe information
if(isset($_POST['recipeSubmit'])){
    $url2 = SPOONACULAR_URL . 'recipes/informationBulk?ids='. $_POST['recipeID'] .'&includeNutrition=false';
}

?> 

<!--WEBPAGE INTERFACE-->
    <main class="page">
        <h2 <?= $hide?>>Results for "<?= $_SESSION['searchInput']?>"</h2>
        <?= $random ?>
        <div class="searchOptions">
            <?= $options?>
        </div>
        <div class="pageBtns">
            <form method="POST" <?=$hidePrevious?>>
                <input type="hidden" value="offset" />
                <input type="submit" name="previousPage" class="pageBtns__Btn" value="< Previous"/>
            </form>
            <form method="POST" <?=$hideNext?>>
                <input type="hidden" value="offset" />
                <input type="submit" name="nextPage"  class="pageBtns__Btn"value="Next >"/>
            </form>
        </div>
    </main>

<?php

//Calls global footer
require_once 'shared/footer.php';

?>