<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Template> */
class TemplateFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'key' => Str::slug($name),
            'view_path' => 'templates.eternal-ivory.index',
            'thumbnail' => 'images/templates/eternal-ivory/thumbnail.svg',
            'is_active' => true,
        ];
    }
}
