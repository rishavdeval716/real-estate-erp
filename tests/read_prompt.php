<?php
$files = glob('C:/Users/risha/.gemini/antigravity-ide/brain/*/.system_generated/logs/transcript.jsonl');
foreach ($files as $f) {
    $lines = file($f);
    foreach ($lines as $idx => $line) {
        if (stripos($line, 'phase 6') !== false || stripos($line, 'phase vi') !== false) {
            $d = json_decode($line, true);
            if (isset($d['type']) && $d['type'] === 'USER_INPUT') {
                echo basename(dirname(dirname(dirname($f)))) . ':' . ($idx+1) . PHP_EOL;
                echo 'USER: ' . substr($d['content'], 0, 400) . PHP_EOL . "===\n";
            }
        }
    }
}
