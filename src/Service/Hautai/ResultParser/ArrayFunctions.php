<?php
namespace App\Service\Hautai\ResultParser;

trait ArrayFunctions
{
    /**
     * @param $needle
     * @param [] $haystack
     * @return array|bool
     */
    public function recursiveSearchByValue($needle, $hayStack) {
        foreach($hayStack as $firstLevelKey=> $value) {

            if ($needle === $value) {
                return [$firstLevelKey];
            } elseif (is_array($value)) {
                $callback = $this->recursiveSearchByValue($needle, $value);

                if ($callback) {
                    return array_merge([$firstLevelKey], $callback);
                }
            }

        }
        return false;
    }

    function getNestedValue($keymap, $array)
    {
        $nestDepth = sizeof($keymap);
        $value = $array;

        for ($i = 0; $i < $nestDepth; $i++) {
            $value = $value[$keymap[$i]];
        }

        return $value;
    }
}