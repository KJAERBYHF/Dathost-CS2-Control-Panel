<?php
// Include the configuration
$config = json_decode(file_get_contents('config.json'), true);
$username = $config['USER'];
$password = $config['PASSWORD'];
$serverid = $config['SERVERID'];

// Define a function to make API requests using cURL
function makeApiRequest($url, $method = 'GET', $data = [], $headers = []) {
    global $username, $password;

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
        return false;
    }

    // Close cURL resource
    curl_close($ch);

    return $response;
}

// Define a function to make API requests with form data using cURL
function makeApiRequestWithFormData($url, $method = 'GET', $data = [], $headers = []) {
    global $username, $password;

    $ch = curl_init($url);

    // Set cURL options for sending form data
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
        return false;
    }

    // Close cURL resource
    curl_close($ch);

    return $response;
}

function handleApiResponse($message, $response) {
    if ($response === false) {
        echo "Error sending the command.";
    } else {
        echo "Command has been sent.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start'])) {
        $response = makeApiRequest("https://dathost.net/api/0.1/game-servers/$serverid/start", 'POST');
        handleApiResponse("Server start request sent", $response);
    } elseif (isset($_POST['stop'])) {
        $response = makeApiRequest("https://dathost.net/api/0.1/game-servers/$serverid/stop", 'POST');
        handleApiResponse("Server stop request sent", $response);
    } elseif (isset($_POST['exec'])) {
        $command = $_POST['execCommand'];

        // Check for special commands and set the appropriate server command
        switch (strtolower($command)) {
            case 'anubis':
                $serverCommand = 'map de_anubis';
                break;
            case 'nuke':
                $serverCommand = 'map de_nuke';
                break;
            case 'inferno':
                $serverCommand = 'map de_inferno';
                break;
            case 'mirage':
                $serverCommand = 'map de_mirage';
                break;
            case 'ancient':
                $serverCommand = 'map de_ancient';
                break;
            case 'vertigo':
                $serverCommand = 'map de_vertigo';
                break;
            case 'overpass':
                $serverCommand = 'map de_overpass';
                break;
            case 'live':
                $serverCommand = 'exec live; say Scrim Enabled';
                break;
            case 'prac':
                $serverCommand = 'exec prac; say Prac Mode Enabled';
                break;
            case 'full':
                $serverCommand = 'mp_match_can_clinch 0; say Full Match Enabled';
                break;
            default:
                $serverCommand = $command; // Use the original command if no match is found
                break;
        }

        $data = ['line' => $serverCommand];
        $response = makeApiRequestWithFormData("https://dathost.net/api/0.1/game-servers/$serverid/console", 'POST', $data);
        handleApiResponse("Executed command: $serverCommand", $response);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Server Kontrol Panel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Gabarito&display=swap" />
     <link rel="stylesheet" type="text/css" href="styles.css">
     
</head>
<body>
    <h1>Server Kontrol Panel</h1>

<form method="post">
    <button type="submit" name="start">Start Server</button>
    <button type="submit" name="stop">Stop Server</button>
    <br><br>
    <label for="execCommand">Execute Command:</label>
    <input type="text" id="execCommand" name="execCommand">
    <button type="submit" name="exec">Execute</button>
</form>
Server Connect: “Put your server connection info here or delete it this line” <br />

Type prac in the text field  - to enable prac <br />
Type live in the text field - to enable live/scrim <br />
Type full in the text field - to play all rounds <br />
 <br />
To change the map just type out the map name, fx. Mirage  <br />

</body>
</html>


