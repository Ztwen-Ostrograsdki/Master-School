<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClasseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name',
            'school_year',
            'level_id',
            'closed',
            'locked',
            'teacher_id'
        ];
    }
}
