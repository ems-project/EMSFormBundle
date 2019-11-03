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
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $result = $this->flattenWithKeys($value, $childPrefix, $root . $key . $childPrefix, $result);
                continue;
            }
            
            $result[ $root . $key ] = $value;
        }
        return $result;
    }
}
