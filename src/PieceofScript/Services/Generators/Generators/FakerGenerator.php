<?php


namespace PieceofScript\Services\Generators\Generators;


use Faker\Generator;

abstract class FakerGenerator extends InternalGenerator
{
    /** @var Generator */
    protected $faker;

    public function __construct(Generator $faker)
    {
        parent::__construct();
        $this->faker = $faker;
    }

}