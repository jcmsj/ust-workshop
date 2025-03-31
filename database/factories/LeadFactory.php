<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $insurance_type = $this->faker->randomElement([
            'Term Life Insurance', 'Mortgage Protection Insurance', 'Whole Life Insurance', 
            'Universal Life Insurance', 'Term to 100 Life Insurance', 'No Medical Exam Life Insurance', 
            'Guaranteed Issue Life Insurance', 'Hard to Insure Life Insurance', 'Final Expense Insurance', 
            'Life Insurance on Children', 'Life Insurance on Seniors', 'Key Person Life Insurance', 
            'Corporate-Owned Insurance', 'Shareholder/Partner Insurance', 'Buy/Sell Agreement Insurance', 
            'Whole Life for Business Owners', 'Whole Life for High Net Worth', 'Whole Life for Estate Planning'
        ]);

        $data = [
            'insurance_type' => $insurance_type,
            'province_territory' => $this->faker->randomElement([
                'NCR',
                'CAR',
                'Region I',
                'Region II',
                'Region III',
                'Region IV-A',
                'Region IV-B',
                'Region V',
                'Region VI-A',
                'Region VI-B',
                'Region VII',
                'Region VIII',
                'Region IX',
                'Region X',
                'Region XI',
                'Region XII',
                'Region XIII',
            ]),
            'birthdate' => $this->faker->date,
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'tobacco_use' => $this->faker->boolean,
            'journey' => $this->faker->randomElement([
                'Still deciding if I need insurance', 'Doing research to find quotes', 
                'Ready to get covered soon', 'Want to get covered right away'
            ]),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'mobile_number' => $this->faker->phoneNumber,
            'email' => $this->faker->safeEmail,
        ];

        // Only add desired_amount for specific insurance types
        if (!in_array($insurance_type, ['Corporate-Owned Insurance', 'Shareholder/Partner Insurance', 'Buy/Sell Agreement Insurance'])) {
            $data['desired_amount'] = $this->faker->randomElement([
                50000, 100000, 150000, 200000, 250000, 300000, 400000, 500000, 
                600000, 700000, 800000, 900000, 1000000, 1250000
            ]);
        }

        // Only add length_coverage for Term Life Insurance
        if ($insurance_type === 'Term Life Insurance') {
            $data['length_coverage'] = $this->faker->randomElement([10,15,20,25,30]);
        }

        // Only add mortgage_amortization for Mortgage Protection Insurance
        if ($insurance_type === 'Mortgage Protection Insurance') {
            $data['mortgage_amortization'] = $this->faker->randomElement([10, 15, 20, 25, 30, 35]);
        }

        // Add length_payment for specific insurance types
        if (in_array($insurance_type, [
            'Whole Life Insurance',
            'Universal Life Insurance',
            'No Medical Exam Life Insurance',
            'Guaranteed Issue Life Insurance',
            'Hard to Insure Life Insurance',
            'Life Insurance on Children',
            'Key Person Life Insurance'
        ])) {
            $data['length_payment'] = $this->faker->randomElement(['10 years', '15 years', '20 years', 'Pay to age 65', 'Life Pay']);
        }

        // Add health_class except for specific insurance types
        if (!in_array($insurance_type, ['Guaranteed Issue Life Insurance', 'No Medical Exam Life Insurance'])) {
            $data['health_class'] = $this->faker->randomElement(['Average', 'Good', 'Excellent']);
        }

        return $data;
    }
}
