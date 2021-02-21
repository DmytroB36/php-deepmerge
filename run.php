<?php

require 'data.php';

/** @var $rawData array */

/**
 * @param $data
 * @return array
 */
function createIndex($data): array
{
    $indexArray = [];

    $it = new ArrayIterator($data);

    while ($it->valid()) {

        $subIt = new ArrayIterator($it->current());

        while ($subIt->valid()) {
            $indexArray[$subIt->current()][$it->key()] = true;
            $subIt->next();
        }

        $it->next();
    }

    return $indexArray;
}

/**
 * @param array $data
 * @return array
 */
function deepMerge(array $data): array
{
    $index = createIndex($data);

    $it = new ArrayIterator($data);

    $dataMerged = [];

    while ($it->valid()) {

        $subIt = new ArrayIterator($it->current());

        $keysList = [];

        while ($subIt->valid()) {
            $keysList = $keysList + $index[$subIt->current()];
            $subIt->next();
        }

        $itemsToMerge = array_intersect_key($data, $keysList);

        $dataMerged[] = array_unique(array_merge(...$itemsToMerge));

        foreach ($keysList as $key => $_) if ($it->offsetExists($key)) $it->offsetUnset($key);

        $it->rewind();
    }

    return $dataMerged;
}

$m1 = microtime(true);
$merged = deepMerge($rawData);
$m2 = microtime(true) - $m1;

echo "Merged count: = " . count($merged) . " in " . number_format($m2, 5) . " seconds\n";
