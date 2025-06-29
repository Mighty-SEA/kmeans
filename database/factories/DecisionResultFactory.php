<?php

namespace Database\Factories;

use App\Models\DecisionResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DecisionResult::class;

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
            'title' => 'Decision Test ' . $index,
            'description' => 'Deskripsi untuk decision test ' . $index,
            'cluster' => $index % 3,
            'count' => 5 + $index,
            'notes' => 'Catatan untuk decision test ' . $index,
        ];
    }
} 