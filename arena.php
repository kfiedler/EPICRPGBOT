<?php
$globals["oasis_rpg_arena_url"] = "https://discord.com/api/v9/channels/1122924019906261052/messages?limit=10";
$globals["discord_interactions"] = "https://discord.com/api/v9/interactions";
$globals["discord_messages"] = "https://discord.com/api/v9/channels/1217655398467764334/messages";

$globals["headers"] = array(
    "authorization: {$globals["authorization"]}",
    "content-type: application/json",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
    "x-super-properties: eyJvcyI6IldpbmRvd3MiLCJicm93c2VyIjoiQ2hyb21lIiwiZGV2aWNlIjoiIiwic3lzdGVtX2xvY2FsZSI6ImVuLVVTIiwiYnJvd3Nlcl91c2VyX2FnZW50IjoiTW96aWxsYS81LjAgKFdpbmRvd3MgTlQgMTAuMDsgV2luNjQ7IHg2NCkgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzEyMi4wLjAuMCBTYWZhcmkvNTM3LjM2IiwiYnJvd3Nlcl92ZXJzaW9uIjoiMTIyLjAuMC4wIiwib3NfdmVyc2lvbiI6IjEwIiwicmVmZXJyZXIiOiIiLCJyZWZlcnJpbmdfZG9tYWluIjoiIiwicmVmZXJyZXJfY3VycmVudCI6Imh0dHBzOi8vZGlzY29yZC5jb20vIiwicmVmZXJyaW5nX2RvbWFpbl9jdXJyZW50IjoiZGlzY29yZC5jb20iLCJyZWxlYXNlX2NoYW5uZWwiOiJzdGFibGUiLCJjbGllbnRfYnVpbGRfbnVtYmVyIjoyNzk5NDMsImNsaWVudF9ldmVudF9zb3VyY2UiOm51bGx9",
);
$i=0;
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
        if(!str_contains($message->content, "<@{$globals["discord_id"]}>")) {
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