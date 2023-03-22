<?php

$css = array("css/recipe.css"); //Links custom page css file

//Calls global header and apiKeys for base URL's and arrays
require_once 'shared/header.php';
require_once 'shared/apiKeys.php';

// -------------- SPOONACULAR API SECTION ---------------------

//Checks if header search button is submitted and sends API request to search for recipes
if(isset($_POST['searchSubmit'])){
$recipeSearchUrl = SPOONACULAR_URL . 'recipes/complexSearch?query=' . $_POST['searchInput'] . '&offset=10&number=12&apiKey=' . $SPOONACULAR['client_id'];
}

//Sends API request to get information about the recipe selected from the previous page (using Recipe ID)
$getRecipeUrl= SPOONACULAR_URL . 'recipes/informationBulk?ids='.  $_POST['recipeID'] .'&includeNutrition=false' . '&apiKey=' . $SPOONACULAR['client_id'];

    //curl commands to grab API results
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$getRecipeUrl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = json_decode(curl_exec($ch), false);
    // var_dump($response);
    curl_close($ch);

    //Grabs values from API response
    $recipeTitle = $response[0]->title;
    $readyTimeMins = $response[0]->readyInMinutes; //Used to find youtube video
    $servings = $response[0]->servings;
    $sourceURL = $response[0]->sourceUrl;
    $summary = $response[0]->summary;
    $ingredientDetails = $response[0]->extendedIngredients;

    //Initialize variables to display listed data
    $ingredientsList = "";
    $instructions = "";

    //Loops through all the ingredients from the API response and grabs ingredient details to be displayed
    for($i = 0; $i < count($ingredientDetails); $i++){
        $ingredientName = $response[0]->extendedIngredients[$i]->name;
        $ingredientAmount = $response[0]->extendedIngredients[$i]->amount;
        $ingredientUnit = $response[0]->extendedIngredients[$i]->unit;

        $ingredientsList .= "<p> " . $ingredientAmount . " " . $ingredientUnit .  " " . $ingredientName . "</p>";
    }

    //Checks if instructions exist (some responses have "null" instructions list), displays appropriate output
    //Loops through all the instructions from the API response and grabs instruction steps to be displayed
    if ($response[0]->analyzedInstructions != null){
        $steps = $response[0]->analyzedInstructions[0]->steps;
        for($i = 0; $i < count($steps); $i++){
            $stepNumber = $steps[$i]->number;
            $step = $steps[$i]->step;
    
            $instructions .= "<p> Step " . $stepNumber. ": " . $step . "</p>";
        }
    }
    else{
        $instructions .= "<p>Please follow the recipe from the source link below!</p>";
    }


//  -------------- YOUTUBE API SECTION ---------------------
     //Sends API request to grab lofi youtube videos
     $searchUrl = YOUTUBE_URL . 'v3/search?part=snippet&maxResults=50&q=lofi+cooking&type=video&key=' . $YOUTUBE['client_id'];
      
     //curl commands to grab API results
     $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$searchUrl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        ));

    $searchResult = json_decode(curl_exec($ch), false);
    curl_close($ch);

    //Initialize user interface variables
    $hide = "";
    $err = "";

    //Displays error message if YOUTUBE API request quota is exceeded for the day, plays a live LOFI video as replacement
    if($searchResult->error->code == "403"){
        $hide = "style='display:none;'";
        $err = "<h2 style='text-align:center;'>The Youtube API request quota has been exceed for the day.<br>So here's a live Lofi Playlist in the meanwhile.</h2>";
        $showVideo = "5qap5aO4i9A";
    }


    //Sets up specific response array to grab items from
    $items = $searchResult->items;

    //Loops through each result from the "lofi search" API request to determine its duration (through another API request)
    for ($i=0; $i<count($items); $i++){
        //Gets video ID's for second API request
        $id = $searchResult->items[$i]->id->videoId;

        //Sends API request to get specific video details based off of its video ID
        $getUrl = YOUTUBE_URL . 'v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id=' . $id . '&key=' . $YOUTUBE['client_id'];

         //curl commands to grab API results        
         $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$getUrl");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            ));

        $videoDetails = json_decode(curl_exec($ch), false);
        curl_close($ch);

        //Gets duration of each video (in ISO 8601 format)
        $duration = $videoDetails->items[0]->contentDetails->duration;
        $durationLong = "";
        
        //Checks for live videos for long recipes (+3hrs), converts ISO format to "3 hours" so that any recipe over 3 hours will get a live video
        if($duration == "P0D"){
            $duration = "PT3H";
        }
    
        //Converts duration to get time in minutes from youtube video duration
        $start = new DateTime('@0');
        $start->add(new DateInterval($duration));
        $minutes = intval($start->format('i'));
        $hours = intval($start->format('H'));
        $hours = $hours * 60; //Converts hours to minutes 

 
        $duration = $hours + $minutes; //Total duration in minutes
        $maxTime = $readyTimeMins; //From Spoonacular API Request

        //FOR TESTING PURPOSES
        // echo $duration . "  ";
        // echo $maxTime. "  ";
        // echo ($maxTime - 5). "  ";
        // echo ($maxTime + 10). "  ";
        // echo $id;
        // echo '<br>';

        //Checks if video matches in length or is +10 minutes longer than the maxReadyTime, ends loop once video is found
        if ( ($duration == ($maxTime)) || ($duration > ($maxTime) && ($duration < ($maxTime + 10)) )){
            $showVideo = $id; //Gets video ID of video to be displayed
            $i = count($items);
        }
        //For short recipes, if the maxReadyTime is less than then, grabs the video thats shorter than 15 minutes (search)
        else if(($maxTime < 10) && ($duration < 10 && $duration > 5)){
            $showVideo = $id; //Gets video ID of video to be displayed
            $i = count($items);
        }
        //For long recipes (+3hrs), plays a live video (no length)
        else if(($maxTime >= 180) && ($duration >= 180)){
            $showVideo = $id; //Gets video ID of video to be displayed
            $i = count($items);
        }
        else{
            $showVideo = "";
        }

    }

?> 

<!--WEBPAGE INTERFACE-->
    <main class="page">
        <div class="split">
            <h2><?= $recipeTitle?></h2>
            <p><u>Time to Make</u>: <?= $readyTimeMins?> Minutes</p>
            <p><u>Servings</u>: <?= $servings?></p>
            <p class="split__block"><?= $summary?></p>
            <div class="split__block">
                <h3>Ingredients List</h3>
                <?= $ingredientsList?>
            </div>
            <div class="split__block">
                <h3>Instructions</h3>
                <?= $instructions?>
            </div>
            <div class="split__block">
                <p><u>Source</u>: <a href="<?= $sourceURL?>" target="_blank"><?= $sourceURL?></a></p>
            </div>
        </div>
        <div class="split youtube">
            <h2 <?=$hide?> > ~ Hit Play & Cook Away ~</h2>
            <?=$err?>
            <iframe src="https://www.youtube.com/embed/<?=$showVideo?>?autoplay=1"></iframe>
        </div>
    </main>

<?php

//Calls global footer
require_once 'shared/footer.php';

?>