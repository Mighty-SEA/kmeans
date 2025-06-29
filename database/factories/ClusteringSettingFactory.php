<?php

namespace Database\Factories;

use App\Models\ClusteringSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClusteringSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClusteringSetting::class;

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
            'name' => 'Setting ' . $index,
            'num_clusters' => 3,
            'max_iterations' => 100,
            'is_default' => false,
            'attributes' => json_encode([
                'income' => true,
                'dependents' => true,
                'house_status' => true,
                'house_condition' => true,
            ]),
        ];
    }
    
    /**
     * Indicate that the setting is the default.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function default()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => true,
            ];
        });
    }
} 