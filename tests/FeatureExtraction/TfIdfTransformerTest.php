<?php

declare(strict_types=1);

namespace Phpml\Tests\FeatureExtraction;

use Phpml\FeatureExtraction\TfIdfTransformer;
use PHPUnit\Framework\TestCase;

/**
 * Class TfIdfTransformerTest
 *
 * @see https://en.wikipedia.org/wiki/Tf-idf
 *
 * @package Phpml\Tests\FeatureExtraction
 */
class TfIdfTransformerTest extends TestCase
{
    public function testSimpleTransformation(): void
    {
        $samples = [
            [
                0 => 1,
                1 => 1,
                2 => 2,
                3 => 1,
                4 => 0,
                5 => 0,
            ],
            [
                0 => 1,
                1 => 1,
                2 => 0,
                3 => 0,
                4 => 2,
                5 => 3,
            ],
        ];

        $tfIdfSamples = [
            [
                0 => 0,
                1 => 0,
                2 => 0.602,
                3 => 0.301,
                4 => 0,
                5 => 0,
            ],
            [
                0 => 0,
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0.602,
                5 => 0.903,
            ],
        ];

        $transformer = new TfIdfTransformer($samples);
        $transformer->transform($samples);

        self::assertEqualsWithDelta($tfIdfSamples, $samples, 0.001);
    }

    public function testTransformationWithMinIdf(): void
    {
        $samples = [
            [1, 1, 2, 1, 0, 0],
            [1, 1, 0, 0, 2, 3],
        ];

        (new TfIdfTransformer($samples, 1, 0.3))->transform($samples);

        self::assertEqualsWithDelta(
            [
                [0.602, 0.301, 0, 0],
                [0, 0, 0.602, 0.903],
            ],
            $samples,
            0.001
        );
    }

    public function testTransformationWithMinTf(): void
    {
        $samples = [
            [0, 0, 0],
            [1, 1, 0],
            [1, 1, 1],
        ];

        (new TfIdfTransformer($samples, 2))->transform($samples);

        self::assertEqualsWithDelta(
            [
                [0.0, 0.0],
                [0.17609125905568124, 0.17609125905568124],
                [0.17609125905568124, 0.17609125905568124],
            ],
            $samples,
            0.001
        );
    }
}
