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
        return \is_array($list) ? $list : [];
    }
}
