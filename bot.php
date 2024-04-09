<?php
date_default_timezone_set("America/Chicago");
error_reporting(0);
$i=0;
$j=0;
$last_message_ids=[];

$globals["oasis_rpg_arena_url"] = "https://discord.com/api/v9/channels/1122924019906261052/messages?limit=10";
$globals["discord_interactions"] = "https://discord.com/api/v9/interactions";
$globals["discord_messages"] = "https://discord.com/api/v9/channels/1227059160042963014/messages";
$globals["inventory"] = [];

$globals["headers"] = array(
    "authorization: {$globals["authorization"]}",
    "content-type: application/json",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
    "x-super-properties: eyJvcyI6IldpbmRvd3MiLCJicm93c2VyIjoiQ2hyb21lIiwiZGV2aWNlIjoiIiwic3lzdGVtX2xvY2FsZSI6ImVuLVVTIiwiYnJvd3Nlcl91c2VyX2FnZW50IjoiTW96aWxsYS81LjAgKFdpbmRvd3MgTlQgMTAuMDsgV2luNjQ7IHg2NCkgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzEyMi4wLjAuMCBTYWZhcmkvNTM3LjM2IiwiYnJvd3Nlcl92ZXJzaW9uIjoiMTIyLjAuMC4wIiwib3NfdmVyc2lvbiI6IjEwIiwicmVmZXJyZXIiOiIiLCJyZWZlcnJpbmdfZG9tYWluIjoiIiwicmVmZXJyZXJfY3VycmVudCI6Imh0dHBzOi8vZGlzY29yZC5jb20vIiwicmVmZXJyaW5nX2RvbWFpbl9jdXJyZW50IjoiZGlzY29yZC5jb20iLCJyZWxlYXNlX2NoYW5uZWwiOiJzdGFibGUiLCJjbGllbnRfYnVpbGRfbnVtYmVyIjoyNzk5NDMsImNsaWVudF9ldmVudF9zb3VyY2UiOm51bGx9",
);

$start_time = time();
$workers = array("chop","fish","pickup","mine");
$workers = array("chainsaw","bigboat","tractor","drill");

$cooldowns = array(
    "hunt" => 60,
    "worker" => 300,
    "training" => 900,
    "adventure" => 3600,
);

$next_run = array(
    "hunt" => $start_time + rand($cooldowns["hunt"]+1, $cooldowns["hunt"]+11),
    "worker" => $start_time + rand($cooldowns["worker"]+1, $cooldowns["worker"]+11),
    "adventure" => $start_time + rand($cooldowns["adventure"]+1, $cooldowns["adventure"]+11),
    "training" => $start_time + rand($cooldowns["training"]+1, $cooldowns["training"]+11),
);

echo "Starting...\r\n";
while(true){
    $ct = date("h:i:s A", time());
    $write = "-------------- {$ct} --------------\r\n";
    $running = "Idling";
    foreach($next_run as $key => $task){
        $write .= "Next Run: ";
        $write .= $key." ".date("h:i:s A", $task)."\r\n";
        if($task <= time()){
            $next_run[$key] = time() + rand($cooldowns[$key]+1, $cooldowns[$key]+11);
            switch ($key){
                case "worker":
                    $job = $workers[rand(0,3)];
                    sendCommand($globals, "rpg {$job}");
                    $running = "Working($job)...";
                    break;
                case "training":
                    training($globals);
                    $running = "Training...";
                    break;
                default:
                    sendCommand($globals, "rpg {$key}");
                    $running = "$key";
                    break;
            }
        }
        sleep(1);
    }
    $running_task = "------------------- {$running} --------------------\r\n";
    $offset = (strlen($running_task) - 40)/2;
    $write .= substr($running_task, $offset, -$offset);
    popen('cls', 'w');
    echo $write;
    sleep(10);
}
