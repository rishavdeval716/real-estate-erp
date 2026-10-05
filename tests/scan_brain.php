<?php
$pattern = '/phase\s*6/i';
$files = glob('C:/Users/risha/.gemini/antigravity-ide/brain/*/.system_generated/logs/transcript.jsonl');
foreach ($files as $f) {
    $lines = file($f);
    foreach ($lines as $idx => $line) {
        if (preg_match($pattern, $line)) {
            echo $f . ':' . ($idx + 1) . PHP_EOL;
            $data = json_decode($line, true);
            if (isset($data['content'])) {
                echo substr($data['content'], 0, 500) . PHP_EOL . '---' . PHP_EOL;
            }
        }
    }
}
