<?php

header('Access-Control-Allow-Origin: *');

require_once("octave.php");
require_once("utils.php");


function carShockAbsorber($from, $to) {
    return executeExperiment($from, $to, "carShockAbsorber", "carShockAbsorberRange", CAR_SHOCK_ABSORBER);
}

function executeExperiment($from, $to, $singleCommand, $rangeCommand, $labels) {
    $scriptRepository = require("experiment-script-repository.php");
    $ret = new stdClass();
    $script = $from == 0 ? $scriptRepository[$singleCommand] : $scriptRepository[$rangeCommand];
    $cmdOut = $from == 0 ? executeOctaveCommand(sprintf(escapeCommand($script), $to)) : executeOctaveCommand(sprintf(escapeCommand($script), $from, $to));
    $ret->content = parseOutput($cmdOut->consoleOutput, $labels);
    $ret->returnCode = $cmdOut->returnValue;
    $ret->rangeFrom = $from;
    $ret->rangeTo = $to;
    return $ret;
}

function parseOutput($output, $labels) {
    $trimOutput = array();
    foreach ($output as $line) {
        $vector = array_map('doubleval', preg_split('/\s+/', trim($line)));
        array_push($trimOutput, convertVectorToObject($vector, $labels));
    }
    return $trimOutput;
}

function convertVectorToObject($vector, $labels) {
    $ret = new stdClass();
    $ret->{$labels[0]} = $vector[0];
    $ret->{$labels[1]} = $vector[1];
    $ret->{$labels[2]} = $vector[2];
    return $ret;
}

?>
