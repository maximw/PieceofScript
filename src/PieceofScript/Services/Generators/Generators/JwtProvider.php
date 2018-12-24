<?php

namespace PieceofScript\Services\Generators\Generators;


use Faker\Factory;
use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Generators\Generators\Faker\FakerAddress;
use PieceofScript\Services\Generators\Generators\Faker\FakerArrayElement;
use PieceofScript\Services\Generators\Generators\Faker\FakerAsciify;
use PieceofScript\Services\Generators\Generators\Faker\FakerBoolean;
use PieceofScript\Services\Generators\Generators\Faker\FakerCity;
use PieceofScript\Services\Generators\Generators\Faker\FakerCountry;
use PieceofScript\Services\Generators\Generators\Faker\FakerCountryCode;
use PieceofScript\Services\Generators\Generators\Faker\FakerCurrencyCode;
use PieceofScript\Services\Generators\Generators\Faker\FakerDateTime;
use PieceofScript\Services\Generators\Generators\Faker\FakerDomain;
use PieceofScript\Services\Generators\Generators\Faker\FakerEmail;
use PieceofScript\Services\Generators\Generators\Faker\FakerEmoji;
use PieceofScript\Services\Generators\Generators\Faker\FakerFirstName;
use PieceofScript\Services\Generators\Generators\Faker\FakerHtml;
use PieceofScript\Services\Generators\Generators\Faker\FakerImageFile;
use PieceofScript\Services\Generators\Generators\Faker\FakerImageUrl;
use PieceofScript\Services\Generators\Generators\Faker\FakerInteger;
use PieceofScript\Services\Generators\Generators\Faker\FakerIpv4;
use PieceofScript\Services\Generators\Generators\Faker\FakerIpv6;
use PieceofScript\Services\Generators\Generators\Faker\FakerLanguageCode;
use PieceofScript\Services\Generators\Generators\Faker\FakerLastName;
use PieceofScript\Services\Generators\Generators\Faker\FakerLatitude;
use PieceofScript\Services\Generators\Generators\Faker\FakerLetter;
use PieceofScript\Services\Generators\Generators\Faker\FakerLocale;
use PieceofScript\Services\Generators\Generators\Faker\FakerLogin;
use PieceofScript\Services\Generators\Generators\Faker\FakerLongitude;
use PieceofScript\Services\Generators\Generators\Faker\FakerMacAddress;
use PieceofScript\Services\Generators\Generators\Faker\FakerMd5;
use PieceofScript\Services\Generators\Generators\Faker\FakerName;
use PieceofScript\Services\Generators\Generators\Faker\FakerPassword;
use PieceofScript\Services\Generators\Generators\Faker\FakerPersonTitle;
use PieceofScript\Services\Generators\Generators\Faker\FakerPhoneNumber;
use PieceofScript\Services\Generators\Generators\Faker\FakerPostCode;
use PieceofScript\Services\Generators\Generators\Faker\FakerRealText;
use PieceofScript\Services\Generators\Generators\Faker\FakerRegexify;
use PieceofScript\Services\Generators\Generators\Faker\FakerSafeEmail;
use PieceofScript\Services\Generators\Generators\Faker\FakerSentences;
use PieceofScript\Services\Generators\Generators\Faker\FakerSha1;
use PieceofScript\Services\Generators\Generators\Faker\FakerSha256;
use PieceofScript\Services\Generators\Generators\Faker\FakerText;
use PieceofScript\Services\Generators\Generators\Faker\FakerTimestamp;
use PieceofScript\Services\Generators\Generators\Faker\FakerTimezone;
use PieceofScript\Services\Generators\Generators\Faker\FakerUrl;
use PieceofScript\Services\Generators\Generators\Faker\FakerWords;
use PieceofScript\Services\Generators\IGeneratorProvider;

class JwtProvider implements IGeneratorProvider
{
    protected $faker;


    public function __construct()
    {
        $this->faker = Factory::create(Config::get()->getFakerLocale());
        $this->faker->seed(Config::get()->getRandomSeed());
    }

    public function getGenerators(): array
    {
        return [
            new FakerAddress($this->faker),
            new FakerArrayElement($this->faker),
            new FakerAsciify($this->faker),
            new FakerBoolean($this->faker),
            new FakerCity($this->faker),
            new FakerCountry($this->faker),
            new FakerCountryCode($this->faker),
            new FakerCurrencyCode($this->faker),
            new FakerDateTime($this->faker),
            new FakerDomain($this->faker),
            new FakerEmail($this->faker),
            new FakerEmoji($this->faker),
            new FakerFirstName($this->faker),
            new FakerHtml($this->faker),
            new FakerImageFile($this->faker),
            new FakerImageUrl($this->faker),
            new FakerInteger($this->faker),
            new FakerIpv4($this->faker),
            new FakerIpv6($this->faker),
            new FakerLanguageCode($this->faker),
            new FakerLastName($this->faker),
            new FakerLatitude($this->faker),
            new FakerLetter($this->faker),
            new FakerLocale($this->faker),
            new FakerLogin($this->faker),
            new FakerLongitude($this->faker),
            new FakerMacAddress($this->faker),
            new FakerMd5($this->faker),
            new FakerName($this->faker),
            new FakerPassword($this->faker),
            new FakerPersonTitle($this->faker),
            new FakerPhoneNumber($this->faker),
            new FakerPostCode($this->faker),
            new FakerRealText($this->faker),
            new FakerRegexify($this->faker),
            new FakerSafeEmail($this->faker),
            new FakerSentences($this->faker),
            new FakerSha1($this->faker),
            new FakerSha256($this->faker),
            new FakerText($this->faker),
            new FakerTimestamp($this->faker),
            new FakerTimezone($this->faker),
            new FakerUrl($this->faker),
            new FakerWords($this->faker),
        ];
    }

}