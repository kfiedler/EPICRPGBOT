<?php
$j=0;
$last_message_ids=[];

$globals["inventory"] = [];

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
