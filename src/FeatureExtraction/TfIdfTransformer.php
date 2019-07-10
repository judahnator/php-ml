<?php

declare(strict_types=1);

namespace Phpml\FeatureExtraction;

use Phpml\Transformer;

class TfIdfTransformer implements Transformer
{
    /**
     * @var array
     */
    private $idf = [];

    /**
     * The term counts.
     *
     * @var array
     */
    private $termCounts = [];

    /**
     * The minimum accepted term frequency.
     *
     * @var int
     */
    private $minTf;

    /**
     * The minimum accepted IDF value.
     *
     * @var float
     */
    private $minIdf;

    public function __construct(array $samples = [], int $minTf = 0, float $minIdf = 0.0)
    {
        if (count($samples) > 0) {
            $this->fit($samples);
        }
        $this->minTf = $minTf;
        $this->minIdf = $minIdf;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->termCounts = array_fill_keys(array_keys($samples[0]), 0);

        foreach ($samples as $sample) {
            foreach ($sample as $index => $count) {
                if ($count > 0) {
                    $this->termCounts[$index]++;
                }
            }
        }

        $count = count($samples);
        $this->idf = array_map(
            function (float $value) use ($count): float {
                return $value > 0.0
                    ? log($count / $value, 10)
                    : 0;
            },
            $this->termCounts
        );
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        array_walk($samples, function (array &$sample): void {
            foreach ($sample as $index => &$feature) {
                if ($this->termCounts[$index] < $this->minTf || $this->idf[$index] < $this->minIdf) {
                    unset($sample[$index]);

                    continue;
                }
                $feature *= $this->idf[$index];
            }
            $sample = array_values($sample);
        });
    }
}
