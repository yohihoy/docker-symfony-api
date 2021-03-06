<?php
declare(strict_types = 1);
/**
 * /src/Doctrine/DBAL/Types/EnumLanguageType.php
 */

namespace App\Doctrine\DBAL\Types;

/**
 * Class EnumLanguageType
 *
 * @package App\Doctrine\DBAL\Types
 */
class EnumLanguageType extends EnumType
{
    public const LANGUAGE_EN = 'en';
    public const LANGUAGE_RU = 'ru';

    protected static string $name = 'EnumLanguage';
    protected static array $values = [
        self::LANGUAGE_EN,
        self::LANGUAGE_RU,
    ];
}
