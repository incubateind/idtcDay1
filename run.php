<?php

require_once(dirname(__FILE__) . "/vendor/autoload.php");
$arguments = getopt("d::", array("data::"));
if (!isset($arguments["data"])) {
    print "Data folder not set.";
    exit(1);
}

$config = json_decode(file_get_contents($arguments["data"] . "/config.json"), true);

if (isset($config["storage"]["input"]["tables"][0]["destination"])) {
    $sourceFile  = $config["storage"]["input"]["tables"][0]["destination"];
} else {
    $sourceFile = $config["storage"]["input"]["tables"][0]["source"];
}
$destinationFile = "sliced.csv";

try {
    $splitter = new \Keboola\DockerDemo\Splitter();
    $rows = $splitter->processFile(
        $arguments["data"] . "/in/tables/{$sourceFile }",
        $arguments["data"] . "/out/tables/{$destinationFile}",
        $config["parameters"]["primary_key_column"],
        $config["parameters"]["data_column"],
        $config["parameters"]["string_length"]
    );
} catch (\Keboola\DockerDemo\Splitter\Exception $e) {
    print $e->getMessage();
    exit(1);
}

print "Processed {$rows} rows.";
exit(0);
