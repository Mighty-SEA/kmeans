<?php

namespace Database\Factories;

use App\Models\NormalizationResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class NormalizationResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NormalizationResult::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'normalized_data' => json_encode([
                [0.1, 0.2, 0.3, 0.4],
                [0.5, 0.6, 0.7, 0.8],
                [0.9, 1.0, 0.5, 0.6],
            ]),
            'min_values' => json_encode([0.1, 0.2, 0.3, 0.4]),
            'max_values' => json_encode([0.9, 1.0, 0.7, 0.8]),
        ];
    }
} 