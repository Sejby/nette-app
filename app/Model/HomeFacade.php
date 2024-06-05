<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Utils\Image;
use Nette\Utils\ImageColor;
use Nette\Utils\ImageType;

final class HomeFacade
{
    private $data;

    public function __construct()
    {
        $this->data = $this->getJson("https://www.digilabs.cz/hiring/data.php");
    }

    // Získá json z URL a dekóduje jej na asociativní pole
    private function getJson(string $url): array
    {
        $json_data = file_get_contents($url);

        if ($json_data == false) {
            throw new \Exception("Chyba při načítání dat z $url");
        }

        $data = json_decode($json_data, true);

        return $data;
    }


    // Vybere random joke o Chuckovi, určitě by to šlo vyřešit i lépe a jinak, ale napadlo mne použití rekurze
    private function getRandomNorrisJoke(): ?string
    {
        $random_number = rand(0, count($this->data)  - 1);
        $joke = $this->data[$random_number]['joke'];

        if (strlen($joke) <= 120) {
            return $joke;
        } else {
            return $this->getRandomNorrisJoke();
        }
    }


    // Rozdělí string Joke na dvě poloviny podle nejbližší mezery od prostředku
    private function splitJoke(string $joke, bool $upper_half = true): string
    {
        $length = strlen($joke);
        $half = intdiv($length, 2);

        $space = strpos($joke, ' ', $half);
        if ($space === false) {
            $space = $half;
        }

        $space_back = strrpos(substr($joke, 0, $half), ' ');
        if ($space_back !== false && ($half - $space_back) < ($space - $half)) {
            $space = $space_back;
        }

        if ($upper_half) {
            return substr($joke, 0, $space);
        } else {
            return substr($joke, $space + 1);
        }
    }

    // Vezme obrázek z URL a vykreslí nad ním text, který generuje funkce getRandomNorrisJoke()
    public function drawJokeOnImage()
    {
        $font_path = '../fonts/font.ttf';
        $font_size = 35;

        $imageData = file_get_contents('https://www.digilabs.cz/hiring/chuck.jpg');

        $image = Image::fromString($imageData);

        $height = intval($image->getHeight());
        $half_height = intdiv($height, 2);

        // Generace random vtipu
        $joke = $this->getRandomNorrisJoke();

        $upper_joke_part = $this->splitJoke($joke, true);
        $lower_joke_part = $this->splitJoke($joke, false);

        $color = ImageColor::rgb(255, 255, 255);;

        $image->ttfText($font_size, 0, 10, (intdiv($half_height, 2)) + $font_size, $color, $font_path, $upper_joke_part);

        $image->ttfText($font_size, 0, 10, $half_height + (intdiv($half_height, 2)) + $font_size, $color, $font_path, $lower_joke_part);

        return $image->toString(ImageType::JPEG, 80); 
    }

    // Vrátí vyfiltrovaná data se stejnými iniciály
    public function filterInitials()
    {
        $final_array = [];

        foreach ($this->data as $data_piece) {
            $pos = strrpos($data_piece['name'], ' ');

            $name_first_letter = $data_piece['name'][0];
            $surname_first_letter = $data_piece['name'][$pos + 1];

            if ($name_first_letter == $surname_first_letter) {
                array_push($final_array, $data_piece);
            }
        }

        return $final_array;
    }

    // Vrátí vyfiltrovaná data, která splňují podmínky výpočtu
    public function filterCalculations()
    {
        $final_array = [];

        foreach ($this->data as $data_piece) {
            $first_value = $data_piece['firstNumber'];
            $second_value = $data_piece['secondNumber'];
            $third_value = $data_piece['thirdNumber'];

            if ($first_value / $second_value == $third_value && $first_value % 2 == 0) {
                array_push($final_array, $data_piece);
            }
        }
        return $final_array;
    }

    // Vrátí vyfiltrovaná data, která byla vytvořena v posledním měsíci nebo následujícím
    public function filterDates()
    {
        $final_array = [];
        $current_date = time();

        foreach ($this->data as $data_piece) {
            $date = strtotime($data_piece['createdAt']);

            if ($date >= strtotime('-1 month', $current_date) && $date <= strtotime('+1 month', $current_date)) {
                array_push($final_array, $data_piece);
            }
        }
        return $final_array;
    }

    // Vrátí vyfiltrovaná data, která splňují podmínky výpočtu bez použití eval()
    public function filterWithoutEval()
    {
        $final_array = [];

        foreach ($this->data as $data_piece) {
            $equation = $data_piece['calculation'];

            if ($this->evaluate($equation)) {
                array_push($final_array, $data_piece);
            }
        }
        return $final_array;
    }

    // Porovná obě strany rovnice a vrátí true, pokud jsou stejné
    private function evaluate($equation)
    {
        if (!preg_match('/^(.*?)=(.*?)$/', $equation, $matches)) {
            return false;
        }

        $left_side = $matches[1];
        $right_side = $matches[2];

        $evaluate_side = function ($side) {
            $side = str_replace(['+', '-'], [' +', ' -'], $side);

            $parts = explode(' ', trim($side));

            $result = 0;
            $operator = '+';

            foreach ($parts as $part) {
                if ($part === '+' || $part === '-') {
                    $operator = $part;
                } else {
                    $result = ($operator === '+') ? ($result + (float)$part) : ($result - (float)$part);
                }
            }

            return $result;
        };

        // Porovná levou a pravou stranu rovnice
        return $evaluate_side($left_side) == $evaluate_side($right_side);
    }
}
