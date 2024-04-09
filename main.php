<?php
$user = readline('enter your username (account1 or account2):');

switch($user){
    case "account1":
        $globals["authorization"] = "";
        $globals["username"] = "";
        $globals["discord_id"] = "";
        break;
    case "account2":
        $globals["authorization"] = "";
        $globals["username"] = "";
        $globals["discord_id"] = "";
        break;
}

$type = readline('bot or arena :');

switch($type){
    case "bot":
        include("bot.php");
        break;
    case "arena":
        include("arena.php");
        break;
}

function training($globals){
    $globals["inventory"] = getInventory($globals);
    sendCommand($globals, "rpg training");
    sleep(3);
    $msgs = getMessages($globals, 10);
    foreach($msgs as $message){
        if(str_contains($message->content, "{$globals["username"]}")){
            switch (true){
                case str_contains($message->content, "What is "):
                    preg_match("/<:(.*):(.*)>/m", $message->content, $matches);
                    $firstletters = substr($matches[1], 0, 4);
                    preg_match_all("/\*\*(\d+)\*\**.+/m", $message->content, $matches);
                    foreach($matches[0] as $key => $match){
                        if(str_contains($match, $firstletters)){
                            sendCommand($globals, $matches[1][$key]);
                        }
                    }
                    break;
                case str_contains($message->content, "Do you have more than"):
                    preg_match("/(\d+).*<:(.*):(.*)>/m", $message->content, $matches);
                    $key = array_keys(array_column($globals["inventory"], 'icon_id'), $matches[3]);
                    if($matches[1] < $globals["inventory"][$key[0]]["qty"]){
                        sendCommand($globals, "yes");
                    }else{
                        sendCommand($globals, "no");
                    }
                    break;
                case str_contains($message->content, "How many "):
                    preg_match("/How many <:(.*):(.*)>/m", $message->content, $matches);
                    preg_match_all("/$matches[2]/", $message->content, $matches);
                    $count = count($matches[0])-1;
                    sendCommand($globals, "$count");
                    break;
                case str_contains($message->content, "Is this a "):
                    preg_match("/\*\*(.*)\*\* \?.*:(.*):/m", $message->content, $matches);
                    if(str_contains($matches[1], $matches[2])){
                        sendCommand($globals, "yes");
                    }else{
                        sendCommand($globals, "no");
                    }
                    break;
                case str_contains($message->content, "** letter of"):
                    preg_match("/the \*\*(.*?)\*\*.*<:(.*?):/m", $message->content, $matches);
                    $number_names = array(
                        "first" => 0,
                        "second" => 1,
                        "third" => 2,
                        "fourth" => 3,
                        "fifth" => 4,
                        "sixth" => 5,
                        "seventh" => 6,
                        "eighth" => 7,
                        "ninth" => 8,
                        "tenth" => 9,
                    );
                    $item = str_split($matches[2]);
                    sendCommand($globals, $item[$number_names[$matches[1]]]);
            }
        }

    }
}

function getInventory($globals){
    sendCommand($globals, "rpg i");
    sleep(3);
    $msgs = getMessages($globals, 4);
    foreach($msgs as $message){
        if(str_contains($message->embeds[0]->author->name, "{$globals["username"]} — inventory")){
            foreach($message->embeds[0]->fields as $field){
                preg_match_all("/<:(.*):(.*)>.*\*\*(.*)\*\*:.(\d+,\d+,\d+|\d+,\d+|\d+)/m", $field->value,$matches);
                foreach($matches[2] as $key => $icon_id){
                    $inventory[] = array(
                        "icon_name" => $matches[1][$key],
                        "icon_id" => $icon_id,
                        "icon_text" => $matches[3][$key],
                        "qty" => $matches[4][$key],
                    );
                }
            }
        }
    }
    $globals["inventory"] = $inventory;
    return $inventory;
}

function sendCommand($globals, $command){
    getMessages($globals, 10);
    $json_data = json_encode(array(
        "mobile_network_type" => "unknown",
        "content" => "$command",
        "tts" => false,
        "flags" => 0,
    ));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $globals["discord_messages"]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $globals["headers"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $result = json_decode($response);
    curl_close($ch);
    return $result;
}

function getMessages($globals, $limit){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $globals["discord_messages"]."?limit=$limit");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $globals["headers"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $result = json_decode($response);
    curl_close($ch);
    foreach($result as $message){
        if(str_contains($message->content, "<@{$globals["discord_id"]}>, you are in the **jail**! Use the command `jail`")){
            echo "\r\nJail! Going to sleep for 4hrs...\r\n";
            sleep(14400);
            sendCommand($globals, "rpg profile");
            getMessages($globals, 5);
            sleep(1);
        }
        if(str_contains($message->content, "<@{$globals["discord_id"]}> sleep")){
            preg_match_all('/sleep (\d+)/m', $message->content, $matches);
            echo "Going to sleep.\r\n";
            $i = 1;
            while ($i < $matches[1][0]) {
                if($i % 60 == 0){
                    $msgs = getMessages($globals, 10);
                    foreach($msgs as $msg){
                        echo "Starting back up!\r\n";
                        if (str_contains($msg->content, "<@{$globals["discord_id"]}> start")) break;
                    }
                }
                sleep(1);
                $i++;
            }
        }
        if(str_contains($message->content, "<@{$globals["discord_id"]}> stop")){
            echo "Stopping.\r\n";
            die();
        }
    }
    return $result;
}
