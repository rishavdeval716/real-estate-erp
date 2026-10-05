<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../app');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$errors = 0;
$count = 0;

foreach ($files as $file) {
    $filePath = $file[0];
    $count++;
    $output = [];
    $returnVar = 0;
    exec("php -l \"$filePath\"", $output, $returnVar);
    if ($returnVar !== 0) {
        echo "SYNTAX ERROR in $filePath:\n" . implode("\n", $output) . "\n\n";
        $errors++;
    }
}

echo "Checked $count PHP files in app/.\n";
if ($errors === 0) {
    echo "SUCCESS: 0 syntax errors detected!\n";
    exit(0);
} else {
    echo "FAILED: $errors syntax error(s) found.\n";
    exit(1);
}
