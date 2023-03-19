<?php

namespace Differ\Differ;
use function Functional\sort;
function genDiff($file1Path, $file2Path)
{
    $firstFileJson = file_get_contents($file1Path);
    $secondFileJson = file_get_contents($file2Path);
    $firstFileData = json_decode($firstFileJson, true);
    $secondFileData = json_decode($secondFileJson, true);
    $result = [];
    foreach ($firstFileData as $key => $value) {
        if (array_key_exists($key, $secondFileData) && $value === $secondFileData[$key]) {
            $result[] = ['key' => $key, 'value' => $value, 'sign' => '  '];
        }
        if (!array_key_exists($key, $secondFileData)) {
            $result[] = ['key' => $key, 'value' => $value, 'sign' => '- '];
        }
        if (array_key_exists($key, $secondFileData) && $value !== $secondFileData[$key]) {
            $result[] = ['key' => $key, 'value' => $value, 'sign' => '- '];
            $result[] = ['key' => $key, 'value' => $secondFileData[$key], 'sign' => '+ '];
        }
    }
    foreach ($secondFileData as $key => $value) {
        if (!array_key_exists($key, $firstFileData)) {
            $result[] = ['key' => $key, 'value' => $value, 'sign' => '- '];
        }
    }
    $result = sort($result, fn ($left, $right) => strcmp($left['key'], $right['key']));
    $finalResult = [];
    foreach($result as $item) {
        $finalResult[$item['sign'] . $item['key']] = $item['value'];
    }
    return preg_replace(array('/"/','/,/'), '', json_encode($finalResult, JSON_PRETTY_PRINT));
}