<?php

namespace Database\Factories;

use App\Models\ClusteringResult;
use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClusteringResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClusteringResult::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $index = 0;
        $index++;
        
        return [
            'beneficiary_id' => Beneficiary::factory(),
            'cluster' => $index % 3,
            'silhouette' => 0.75,
        ];
    }
} 