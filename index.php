<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="loader.css" type="text/css">
</head>
<body>
<div class="device">
	<div class="device__a">
		<div class="device__a-1"></div>
		<div class="device__a-2"></div>
	</div>
	<div class="device__b"></div>
	<div class="device__c"></div>
	<div class="device__d"></div>
	<div class="device__e"></div>
	<div class="device__f"></div>
	<div class="device__g"></div>
</div>
    <h1>Hangman</h1>
    <form name="word_to_be_guessed" method="POST" action="guess.php">
        <label> 
            Enter your username:
            <input type="text" name="username" required maxlength="25"/>
        </label>
        <label> 
            Enter the word to be guessed:
            <input type="text" name="word" required maxlength="25" autofocus/>
        </label>
        <input type='submit' value='Submit Word'/>
    </form>
</body>
</html>
