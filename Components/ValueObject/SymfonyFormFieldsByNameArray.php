<?php

namespace EMS\FormBundle\Components\ValueObject;

class SymfonyFormFieldsByNameArray
{
    /** @var array */
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function getFieldIdsJson(array $exclude = []): string
    {
        if (count($this->fields) === 0) {
            return "";
        }

        $json = \json_encode(\array_diff(\array_keys($this->flattenWithKeys($this->fields)), $exclude));
        return $json === false ? "" : $json;
    }

    private function flattenWithKeys(array $array, $childPrefix = '_', $root = '', $result = [])
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = $this->flattenWithKeys($v, $childPrefix, $root . $k . $childPrefix, $result);
            } else {
                $result[ $root . $k ] = $v;
            }
        }
        return $result;
    }
}
