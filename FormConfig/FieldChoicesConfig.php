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
    /** @var int */
    private $maxLevel;
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

    public function list(): array
    {
        $combine = array_combine($this->getCurrentLevel($this->labels), $this->getCurrentLevel($this->values));

        return is_array($combine) ? $combine : [];
    }

    public function addChoice(string $choice): void
    {
        dump("added choice:", $choice);
        $this->choices[] = $choice;
    }

    public function isMultiLevel(): bool
    {
        return $this->getMaxLevel() > 0;
    }

    public function hasNextLevel(): bool
    {
        $nextLevel = \array_reduce(
            $this->choices,
            function($values, $choice) {
                return $values[$choice] ?? [];
            },
            $this->values
        );

        return count($nextLevel) < count($nextLevel, COUNT_RECURSIVE);
    }

    public function hasChoosen(): bool
    {
        return count($this->choices) > 0;
    }

    public function getMaxLevel(): int
    {
        if ($this->maxLevel !== null) {
            return $this->maxLevel;
        }

        $this->maxLevel = max(0, $this->calculateMaxLevel($this->values));
        return $this->maxLevel;
    }

    private function calculateMaxLevel(array $choices): int
    {
        $level = 0;
        foreach ($choices as $choice) {
            if (\is_array($choice)) {
                $level = max(
                    $level,
                    1 + $this->calculateMaxLevel($choice[\array_key_first($choice)])
                );
            }
        }
        return $level;
    }

    private function getCurrentLevel(array $elements): array
    {
        return \array_filter(
            \array_map(
                function ($element) {
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
