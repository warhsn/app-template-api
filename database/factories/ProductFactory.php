<?php

namespace Database\Factories;

use App\Models\Price;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'product_type_id' => ProductType::factory(),
            'tiered' => fake()->boolean(),
            'recurring' => fake()->boolean(),
        ];
    }

    public function withPrice(array $attributes = []): self
    {
        return $this->has(
            Price::factory()->state($attributes),
            'prices'
        );
    }

    public function forPriceable($priceable, $relationship = 'priceable'): self
    {
        return $this->state(function (array $attributes) use ($priceable) {
            return [
                'priceable_id' => $priceable->getKey() ?? $priceable->create()->getKey(),
                'priceable_type' => $priceable instanceof Factory
                    ? $priceable->newModel()::class
                    : get_class($priceable),
            ];
        });
    }

    public function withService($service): self
    {
        return $this->afterCreating(function ($product) use ($service) {
            // If $service is a factory, create the model
            if ($service instanceof Factory) {
                $service = $service->create();
            }

            $product->services()->attach($service);
        });
    }
}
