<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

class Set {
    public $ids = [];
    public $key = null;
    public function __construct($arr) {
        $this->setIds($arr);
    }
    
    public function setIds($arr) {
        $this->ids = $arr;
    }
    public function mergeInto($set) {
        $this->setIds(array_merge($this->ids, $set->ids));
    }
    
    public function pruneDuplicates() {
        $this->ids = array_unique($this->ids);
    }
    public function __toString()
    {
        return implode(',', $this->ids);
    }
}

function mergeIntersectedSets ($idToSet, $newSet) {
  $merged = [];
  foreach ($newSet->ids as $id) {
    if (isset($idToSet[$id])) {
      $oldSet = $idToSet[$id];
      $merged[] = $oldSet;
    }
  }
  return array_unique($merged);
}

function checkExsistingIds ($ids, $needIds) {
    foreach ($needIds as $id) {
        if (isset($ids[$id])) {
            return true;
        }
    }
    return false;
}

function merge($input) {
    $idToSet = [];
    $setToId = [];
    $sets = [];

    foreach ($input as $row) {
        $set = new Set($row);
        if (checkExsistingIds($idToSet, $row)) {
            $mergedSets = mergeIntersectedSets($idToSet, $set);
            foreach($mergedSets as $inSet) {
              $set->mergeInto($inSet);
              foreach($setToId[$inSet->key] as $id) {
                $idToSet[$id] = $set;
              }
              unset($sets[$inSet->key]);
              unset($setToId[$inSet->key]);
            }
            $set->pruneDuplicates();
        }
        $sets[] = $set;
        end($sets);
        $set->key = key($sets);
        $setToId[$set->key] = [];
        foreach ($set->ids as $id) {
            $idToSet[$id] = $set;
            $setToId[$set->key][] = $id;
        }
    }
    return $sets;
}

include 'data.php';

$t1 = microtime(true);
$result = merge($rawData);
$t2 = microtime(true);

echo "Time: ", $t2 - $t1, "<br />Rows: ", count($result);
