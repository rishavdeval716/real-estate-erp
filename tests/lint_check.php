<?php
$dirs = [
    __DIR__ . '/../app/Controllers',
    __DIR__ . '/../app/Models',
    __DIR__ . '/../app/Views',
    __DIR__ . '/../app/Config',
    __DIR__ . '/../app/Database',
];

$errors = [];
$checked = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $out = [];
            $code = 0;
            exec("php -l \"$path\"", $out, $code);
            $checked++;
            if ($code !== 0) {
                $errors[] = implode("\n", $out);
            }
        }
    }
}

echo "Linted $checked files.\n";
if (empty($errors)) {
    echo "ALL FILES PASSED SYNTAX CHECK!\n";
} else {
    echo "ERRORS FOUND:\n" . implode("\n---\n", $errors) . "\n";
    exit(1);
}
