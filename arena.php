<?php
$j=0;
$last_message_ids=[];
while(true) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $globals["oasis_rpg_arena_url"]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $globals["headers"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $result = json_decode($response);
    curl_close($ch);

    foreach ($result as $message) {
            if(str_contains($message->content, "<@{$globals["discord_id"]}>")) {
                echo "You got a warning or something... sleeping for 30 mins";
                sleep(1800);
            }else{
                if (!empty($message->embeds)) {
                    if (str_contains($message->embeds[0]->description, "** started an arena event!")) {
                        $json_data = json_encode(array(
                            "type" => 3,
                            "guild_id" => "829503250100518962",
                            "channel_id" => "1122924019906261052",
                            "message_flags" => 0,
                            "message_id" => "$message->id",
                            "application_id" => "555955826880413696",
                            "session_id" => "e1b4431a222d3585d3ee87af3b63ad0b",
                            "data" => array(
                                "component_type" => 2,
                                "custom_id" => "arena_join",
                            )
                        ));

                        if (!in_array($message->id, $last_message_ids)) {
                            $count = count($last_message_ids);
                            if ($count >= 5) {
                                array_shift($last_message_ids);
                            }
                            $last_message_ids[] = $message->id;

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $globals["discord_interactions"]);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $globals["headers"]);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                            curl_setopt($ch, CURLOPT_POST, TRUE);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                            $response = curl_exec($ch);
                            $result = json_decode($response);
                            curl_close($ch);
                            $j++;
                            echo "Joined. ($message->id) ($j)\n\r";
                        }
                    }
                }
            }

    }
    sleep(5);
}
