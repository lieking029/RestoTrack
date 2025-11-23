<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UnitOfMeasurement extends Enum
{
    /*
    |--------------------------------------------------------------------------
    | Weight
    |--------------------------------------------------------------------------
    */
    const MILLIGRAM = 1;   // mg
    const GRAM = 2;        // g
    const KILOGRAM = 3;    // kg
    const OUNCE = 4;       // oz
    const POUND = 5;       // lb

    /*
    |--------------------------------------------------------------------------
    | Volume
    |--------------------------------------------------------------------------
    */
    const MILLILITER = 10; // ml
    const LITER = 11;      // l
    const CUP = 12;        // cup
    const TABLESPOON = 13; // tbsp
    const TEASPOON = 14;   // tsp

    /*
    |--------------------------------------------------------------------------
    | Count / Pieces
    |--------------------------------------------------------------------------
    */
    const PIECE = 20;      // pc
    const PIECES = 21;     // pcs
    const DOZEN = 22;      // dozen
    const PACK = 23;       // pack
    const BOX = 24;        // box

    /*
    |--------------------------------------------------------------------------
    | Size Labels
    |--------------------------------------------------------------------------
    */
    const SMALL = 30;
    const MEDIUM = 31;
    const LARGE = 32;
    const XL = 33;

    /*
    |--------------------------------------------------------------------------
    | Length
    |--------------------------------------------------------------------------
    */
    const CENTIMETER = 40; // cm
    const INCH = 41;       // inch

    /*
    |--------------------------------------------------------------------------
    | Optional readable labels
    |--------------------------------------------------------------------------
    */
    public static function labels(): array
    {
        return [
            self::MILLIGRAM => 'mg',
            self::GRAM => 'g',
            self::KILOGRAM => 'kg',
            self::OUNCE => 'oz',
            self::POUND => 'lb',

            self::MILLILITER => 'ml',
            self::LITER => 'l',
            self::CUP => 'cup',
            self::TABLESPOON => 'tbsp',
            self::TEASPOON => 'tsp',

            self::PIECE => 'pc',
            self::PIECES => 'pcs',
            self::DOZEN => 'dozen',
            self::PACK => 'pack',
            self::BOX => 'box',

            self::SMALL => 'small',
            self::MEDIUM => 'medium',
            self::LARGE => 'large',
            self::XL => 'xl',

            self::CENTIMETER => 'cm',
            self::INCH => 'inch',
        ];
    }

    /**
     * Get full descriptions for each unit
     */
    public static function descriptions(): array
    {
        return [
            self::MILLIGRAM => 'Milligram (mg)',
            self::GRAM => 'Gram (g)',
            self::KILOGRAM => 'Kilogram (kg)',
            self::OUNCE => 'Ounce (oz)',
            self::POUND => 'Pound (lb)',

            self::MILLILITER => 'Milliliter (ml)',
            self::LITER => 'Liter (l)',
            self::CUP => 'Cup',
            self::TABLESPOON => 'Tablespoon (tbsp)',
            self::TEASPOON => 'Teaspoon (tsp)',

            self::PIECE => 'Piece (pc)',
            self::PIECES => 'Pieces (pcs)',
            self::DOZEN => 'Dozen',
            self::PACK => 'Pack',
            self::BOX => 'Box',

            self::SMALL => 'Small',
            self::MEDIUM => 'Medium',
            self::LARGE => 'Large',
            self::XL => 'Extra Large (XL)',

            self::CENTIMETER => 'Centimeter (cm)',
            self::INCH => 'Inch',
        ];
    }

    /**
     * Get units grouped by category for select dropdown
     */
    public static function getGroupedUnits(): array
    {
        return [
            'Weight' => [
                self::MILLIGRAM => self::descriptions()[self::MILLIGRAM],
                self::GRAM => self::descriptions()[self::GRAM],
                self::KILOGRAM => self::descriptions()[self::KILOGRAM],
                self::OUNCE => self::descriptions()[self::OUNCE],
                self::POUND => self::descriptions()[self::POUND],
            ],
            'Volume' => [
                self::MILLILITER => self::descriptions()[self::MILLILITER],
                self::LITER => self::descriptions()[self::LITER],
                self::CUP => self::descriptions()[self::CUP],
                self::TABLESPOON => self::descriptions()[self::TABLESPOON],
                self::TEASPOON => self::descriptions()[self::TEASPOON],
            ],
            'Count/Pieces' => [
                self::PIECE => self::descriptions()[self::PIECE],
                self::PIECES => self::descriptions()[self::PIECES],
                self::DOZEN => self::descriptions()[self::DOZEN],
                self::PACK => self::descriptions()[self::PACK],
                self::BOX => self::descriptions()[self::BOX],
            ],
            'Size' => [
                self::SMALL => self::descriptions()[self::SMALL],
                self::MEDIUM => self::descriptions()[self::MEDIUM],
                self::LARGE => self::descriptions()[self::LARGE],
                self::XL => self::descriptions()[self::XL],
            ],
            'Length' => [
                self::CENTIMETER => self::descriptions()[self::CENTIMETER],
                self::INCH => self::descriptions()[self::INCH],
            ],
        ];
    }

    /**
     * Get the label for a unit value
     */
    public static function getLabel(int $value): string
    {
        return self::labels()[$value] ?? '';
    }

    /**
     * Get the full description for a unit value (e.g., "Kilogram (kg)")
     */
    public static function getFullDescription(int $value): string
    {
        return self::fullDescriptions()[$value] ?? '';
    }
}