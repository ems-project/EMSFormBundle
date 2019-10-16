<?php

namespace EMS\FormBundle\FormConfig;

class FieldChoicesConfig
{
    /** @var string */
    private $id;
    /** @var array */
    private $values;
    /** @var array */
    private $labels;

    public function __construct(string $id, array $values, array $labels)
    {
        if (\count($values) !== \count($labels)) {
            throw new \Exception(sprintf('Invalid choice list: %d values != %d labels!', \count($values), \count($labels)));
        }

        $this->id = $id;
        $this->values = $values;
        $this->labels = $labels;
    }

    public function list(): array
    {
        $combine = array_combine($this->getLevel($this->labels), $this->getLevel($this->values));

        return is_array($combine) ? $combine : [];
    }

    private function getLevel(array $elements): array
    {
        return \array_filter(
            \array_map(
                function($element) {
                    if (\is_array($element)) {
                        return \array_key_first($element);
                    }
                    return $element;
                },
                $elements
            )
        );
    }
}
