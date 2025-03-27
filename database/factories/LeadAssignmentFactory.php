<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadAssignment>
 */
class LeadAssignmentFactory extends Factory
{
    protected $model = LeadAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement([
                LeadAssignment::STATUS_TO_CALL,
                LeadAssignment::STATUS_SUCCESS,
                LeadAssignment::STATUS_FAILED,
            ]),
        ];
    }
}
