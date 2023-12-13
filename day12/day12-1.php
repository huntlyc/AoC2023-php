<?php

define('DEBUG', true);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$sum = 0;
$inputs = [];
// Parse the input
foreach ($lines as $line) {
    if(empty($line)) continue;

    list($springs,$config) = explode(' ', $line);
    $lineGroups = [];

    $gz = 0;
    for($i = 0; $i < strlen($springs); $i++) {
        if(in_array($springs[$i], ['?', '#'])){
            $gz++;
        }else if($springs[$i] == '.' && $gz > 0){
            $lineGroups[] = $gz;
            $gz = 0;
        }
    }
    if($gz > 0) $lineGroups[] = $gz;

    $inputs[] = [
        'springs' => $springs,
        'config' => $config,
        'groups' => $lineGroups
    ];
}
print_r($inputs);
echo $sum . PHP_EOL;
