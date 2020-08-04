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
    /** @var array */
    private $choices = [];
    /** @var ?string */
    private $placeholder;
    /** @var ?string */
    private $sort;

    public function __construct(string $id, array $values, array $labels)
    {
        if (\count($labels) > \count($values)) {
            $this->placeholder = \array_shift($labels);
        }

        if (\count($values) !== \count($labels)) {
            throw new \Exception(sprintf('Invalid choice list: %d values != %d labels!', \count($values), \count($labels)));
        }

        $this->id = $id;
        $this->values = $values;
        $this->labels = $labels;
        $this->sort = null;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function listLabel(): string
    {
        $choices = $this->choices;
        $choice = \array_pop($choices);

        $list = $this->combineValuesAndLabels($this->values, $this->labels, $choices);
        return \array_flip($list)[$choice] ?? '';
    }

    public function list(): array
    {
        return $this->combineValuesAndLabels($this->values, $this->labels, $this->choices);
    }

    public function addChoice(string $choice): void
    {
        if (!isset(\array_flip($this->list())[$choice])) {
            throw new \Exception('invalid choice: happens when previous level choices are changed without ajax calls');
        }

        $this->choices[] = $choice;
    }

    public function isMultiLevel(): bool
    {
        return $this->calculateMaxLevel($this->values) > 0;
    }

    public function getMaxLevel(): int
    {
        return $this->calculateMaxLevel($this->values);
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }

    private function calculateMaxLevel(array $choices): int
    {
        $level = 0;
        foreach ($choices as $choice) {
            if (\is_array($choice)) {
                $level = \max(
                    $level,
                    1 + $this->calculateMaxLevel($choice[\array_key_first($choice)])
                );
            }
        }
        return $level;
    }

    private function getTopLevel(array $elements): array
    {
        return \array_map(
            function ($element) {
                if (\is_array($element)) {
                    return \array_key_first($element);
                }
                return $element;
            },
            $elements
        );
    }

    private function combineValuesAndLabels(array $values, array $labels, array $choices): array
    {
        foreach ($choices as $choice) {
            $idx = \array_search($choice, $this->getTopLevel($values));
            if ($idx === false) {
                continue;
            }
            $values = $values[$idx];
            $labels = $labels[$idx];

            if (!is_array($values) || !is_array($labels)) {
                return [];
            }

            $values = \reset($values);
            $labels = \reset($labels);

            if ($values === false || $labels === false) {
                return [];
            }
        }

        $list = \array_combine($this->getTopLevel($labels), $this->getTopLevel($values));

        return $this->sort(\is_array($list) ? $list : []);
    }

    /**
     * @param array<string|int, string> $list
     */
    private function sort(array $list): array
    {
        if ($list == null) {
            return $list;
        }

        $firstKey = \array_key_first($list);
        /** @var null|string $firstValue */
        $firstValue = $list[$firstKey] ?? null;

        if ($firstValue === null || $firstValue === '') {
            \array_shift($list); //do not sort placeholder
        }

        if ($this->sort === 'label_alpha') {
            $collator = new \Collator('en');
            uksort($list, function ($a, $b) use ($collator) {
                return $collator->compare($a, $b);
            });
        }
        if ($this->sort === 'value_alpha') {
            $collator = new \Collator('en');
            uasort($list, function ($a, $b) use ($collator) {
                return $collator->compare($a, $b);
            });
        }

        if ($firstValue === null ||  $firstValue === '') {
            $list = \array_merge([$firstKey => $firstValue], $list); // merge placeholder back
        }

        return $list;
    }
}
