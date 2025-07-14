<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $languages = [
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Nepali', 'code' => 'np'],
            ['name' => 'Hindi', 'code' => 'hi'],
            ['name' => 'Chinese', 'code' => 'zh'],
            ['name' => 'Japanese', 'code' => 'ja'],
            ['name' => 'Korean', 'code' => 'ko'],
            ['name' => 'Spanish', 'code' => 'es'],
            ['name' => 'French', 'code' => 'fr'],
            ['name' => 'German', 'code' => 'de'],
            ['name' => 'Italian', 'code' => 'it'],
            ['name' => 'Portuguese', 'code' => 'pt'],
            ['name' => 'Russian', 'code' => 'ru'],
            ['name' => 'Arabic', 'code' => 'ar'],
            ['name' => 'Thai', 'code' => 'th'],
            ['name' => 'Vietnamese', 'code' => 'vi'],
        ];

        $language = $this->faker->randomElement($languages);
        
        return [
            'name' => $language['name'],
            'code' => $language['code'],
        ];
    }

    /**
     * Create English language
     */
    public function english()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'English',
                'code' => 'en',
            ];
        });
    }

    /**
     * Create Nepali language
     */
    public function nepali()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Nepali',
                'code' => 'np',
            ];
        });
    }

    /**
     * Create Hindi language
     */
    public function hindi()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Hindi',
                'code' => 'hi',
            ];
        });
    }

    /**
     * Create Chinese language
     */
    public function chinese()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Chinese',
                'code' => 'zh',
            ];
        });
    }

    /**
     * Create Japanese language
     */
    public function japanese()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Japanese',
                'code' => 'ja',
            ];
        });
    }

    /**
     * Create Korean language
     */
    public function korean()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Korean',
                'code' => 'ko',
            ];
        });
    }
}
