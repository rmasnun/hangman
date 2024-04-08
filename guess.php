<?php
//Start Session
session_start();
    session_set_cookie_params(
        0,                 // Lifetime -- 0 means erase when browser closes
        '/',               // Which paths are these cookies relevant?
        'hangman.joebailey.xyz', // Only expose this to which domain?
        true,              // Only send over the network when TLS is used
        true               // Don't expose to Javascript
    );
    $username = htmlspecialchars($_POST["username"]);
    $_SESSION["username"] = $username;

    //If game restarted or accessed directly
    if (!$_SESSION['word'] && !$_POST["word"] || $_POST["restart"] || !$_POST["guess"] && !$_POST["word"]) {
        //Remove Session for data sanitization
        session_unset();
        session_destroy();
        //Redirect to homepage
        header("Location: index.php");
        exit;
    }
    function noHTML($input, $encoding = 'UTF-8') {
        return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
    }
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman</title>
    <link rel="stylesheet" href="parallax.css" type="text/css">
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
<div id='stars'></div>
<div id='stars2'></div>
<div id='stars3'></div>
<div id='title'>
    <?php
        echo "<span>";
        echo "Hello, ".$_SESSION['username']."";
        echo "</span>";
    ?>
  <br>
  <span>
  Ready to be hanged?
  </span>
</div>
<div class='Hangman-main' style="position:absolute; top:20%;">
<div class="stats" style="">
<!-- Music/ Audio part -->

<div class="audio-player">
  <?php
  // Define the default track
  $defaultTrack = './music/bleeditout.mp3';

  // Check if a track is selected via URL parameters
  $selectedTrack = isset($_GET['track']) ? $_GET['track'] : $defaultTrack;
  ?>


<ul class="music-list">
    <li class="music-item">
      <a href="?track=./music/bleeditout.mp3">Linkin Park - Bleed it out</a>
    </li>
    <li class="music-item">
      <a href="?track=./music/chilli.mp3">Chilli Peppers - Can't Stop</a>
    </li>
    <li class="music-item">
      <a href="?track=./music/music.mp3">Zelda OG Soundtrack</a>
    </li>
    <!-- Add more music tracks as needed -->
  </ul>
  <h2 class="audio-title">Wannabe Funky Player</h2>
  <audio controls loop autoplay muted>
    <source src="<?php echo $selectedTrack; ?>" type="audio/mpeg">
    Your browser does not support the audio element.
  </audio>

  <button class="mute-toggle">Click the above unmute button for music.</button>
</div>


<!-- <audio controls>
  <source src="./music/music.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio> -->

<?php
    //If word has just been entered on homepage
    if ($_POST["word"]) {
        //Create Word Session Variable with lowercase array of word
        $_SESSION['word'] = array_unique(str_split(strtolower(noHTML($_POST["word"]))));
        //Create Word Session Variable with duplicates
        $_SESSION['original_word'] = str_split(strtolower(noHTML($_POST["word"])));
        //Create Letters Session Variable with array length
        $_SESSION['letters'] = sizeof($_SESSION['word']);
        //Set the game in motion
        $_SESSION['turn'] = 1;
        //Print Letters to be guessed
        print("<p>Letters to be guessed: ".$_SESSION['letters']."</p>");
        //Print word to be guesses
        echo "<p>Word to be guessed: ";
        foreach ($_SESSION['original_word'] as $i){
            echo "- ";
        }
        echo "</p>";
        //Print hangman image
        print("<img src='img/1.png' alt='hangman' />");
    }
    //If guess has been made
    else {
        function update($guesses, $incorrect) {
            //Update letters to be guessed variable
            $_SESSION['letters'] = $_SESSION['letters'] - $right;
            print("<p>Letters to be guessed: ".$_SESSION['letters']."</p>");
            //Print letters guessed by joining string with a space
            print("<p>Letters guessed: ".implode(" ", $guesses));
            //Print incorrect gusses left
            print("<p>Incorrect guesses left: ".$incorrect)."</p>";
            //Update Incorrect Session Variable
            $_SESSION['incorrect'] = $incorrect;
            //Print word to be guessed
            echo "<p>Word to be guessed: ";
            foreach ($_SESSION['original_word'] as $i){
                if (in_array($i, $guesses)) {
                    echo $i." ";
                }
                else {
                    echo "- ";
                }
            }
            echo "</p>";
        };
        //Get session variables
        $word = $_SESSION['word'];
        $turn = $_SESSION['turn'];
        //Get guess
        $guess = strtolower(noHTML($_POST["guess"]));

        //If there have been previous guesses
        if ($_SESSION['guesses']) {
            //Get previous guesses
            $guesses = $_SESSION['guesses'];
        }
        else {
            //Create empty guesses array
            $guesses = [];
        };
        // If game isn't over
        if ($turn < 6) {
            //If last turn and guess isn't right
            if ($turn == 5 && !array_diff($word, $guesses)) {
                echo "<h2>Game over!</h2>";
                echo "<div id='record'>";
                echo "<div class='content'>";
                echo    "<p class='rock'>ROCK!</p>";
                echo   "<p>Like, totally.</p>";
                echo "</div>";
                echo "</div>";
                session_unset();
                session_destroy();
                $turn = 6;
            }
            //If already guessed
            else if (in_array($guess, $guesses)) {
                echo "<h2>You've already guessed that letter!</h2>";
                //If Incorrect Session exists
                if ($_SESSION['incorrect']) {
                    $incorrect = $_SESSION['incorrect'];
                }
                else {
                    $incorrect = 5;
                };
                update($guesses, $incorrect);
            }
            //If not already guessed
            else {
                array_push($guesses, $guess);
                //Update Session Guesses
                $_SESSION['guesses'] = $guesses;
                //If game hasn't been won
                if (array_diff($word, $guesses)) {
                    //If guess is right
                    if (in_array($guess, $word)) {
                        echo "<h2>You got it right</h2>";
                        //Update the number of right guesses
                        $right = $right + 1;
                        //If Incorrection Session Variable exists set it
                        if ($_SESSION['incorrect']) {
                            $incorrect = $_SESSION['incorrect'];
                        }
                        else {
                            $incorrect = 5;
                        };
                    }
                    //Guess is wrong
                    else {
                        echo "<h2>You got it wrong</h2>";
                        //Update number of turns
                        $_SESSION['turn'] = $_SESSION['turn'] + 1;
                        $turn = $_SESSION['turn'];
                        $incorrect = 6 - $_SESSION['turn'];
                    };
                    update($guesses, $incorrect);
                }
                //Game won and destroy session
                else {
                    echo "<h2>Game won!</h2>";
                    echo "<p>Word: ".join( "", $_SESSION['original_word'])."</p>";
                    session_unset();
                    session_destroy();
                }
            }
        }
        //Print the hangman image
        echo "<div class='img'>";
        print("<img src='img/".$turn.".png' alt='hangman' />");
        echo "</div>";
    }
?>
</div>
<br>
<br>
<form method='POST' action=''>
    <input type='text' name='guess' required maxlength="1" autofocus style="width:1.5em;text-align:center;" />
    <div class="btn btn-three">
    <input
        class="btn btn-three"
        style="color:#000;z-index:90;"
        type='submit' 
        value='Guess' 
        <?php
            //If game over disable submit button
            if (!$_POST["word"]) {
                if(!array_diff($word, $guesses) || $turn >= 6) {
                    echo "disabled";
                }
            };
        ?>
    />
    </div>
</form>
<div class="btn btn-two" style="z-index:98;">
<form method='POST' action=''>
    <input type='submit' class="btn btn-two" style="color:#000; margin-top: 30px; z-index:99" value='Restart Game'/>
</form>
</div>
</div>
<div class="bars">
    <div class="bars__item"></div>
    <div class="bars__item"></div>
    <div class="bars__item"></div>
    <div class="bars__item"></div>
</div>
<?php
    //If game over disable submit button
    if (!$_POST["word"]) {
        if(!array_diff($word, $guesses) || $turn >= 6) {
            echo "<style>";
            echo ".yellow-stroke:hover path {";
            echo "  stroke: #ff0000;"; // Change color to red on hover
            echo "}";
            echo "</style>";
            
            echo "<div class='container'>";
            echo "<div class='yellow-stroke'>";
            
            echo " <!--The SVG to animate in-->";
            echo "<svg xmlns='http://www.w3.org/2000/svg' id='yellow-stroke' viewBox='0 0 800 200'>";
            // Start with an empty path
            echo "  <path class='brush' fill='none' stroke='#ffcf48' stroke-miterlimit='10' stroke-width='24' d='M0 100H0'/>";
            echo "</svg>";
            
            echo " <!--this is the clipping path-->";
            echo "<svg >";
            echo "   <defs>";
            echo "    <clipPath id='YellowSvgPath' transform='rotate(-2 471 26)'>";
            // Update the path data to cover the entire area
            echo "     <path d='M0 0H800V200H0V0Z'/>";
            echo "    </clipPath>";
            echo "   </defs>";
            // Adjust x and y coordinates as needed
            // echo "   <text x='200' y='20' fill='red'>You lose</text>";
            echo "   </svg>";
            
            echo "</div>";
            echo "</div>";
            echo "<div class='banner'>GAME OVER YOU LOOSE</div>";

            echo "<div class='ripplebutton_div'>";
            echo "<form method='POST' action=''>";
            echo "<input type='submit' class='btn rippleButton' style='color:#000; margin-top: 30px; z-index:100;' value='Restart Game'/>";
            echo "</form>";
            echo "</div>";


        }
    };
?>
</body>
</html>