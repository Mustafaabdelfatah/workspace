<?php
declare(strict_types=1);
namespace Modules\Core\Traits;

trait ValidationMessageTraits
{
    public function validationMessages($lang_key = "ar") {
        return [
            "required" => "you must provide the :attribute",
            "unique" => ":attribute is already taken",
            "max" => ":attribute must be below valid length",
            "date" => ":attribute must be valid Date",
            "date_format" => ":attribute must be valid Date Format (Y-m-d)",
            "after" => ":attribute must be Above Today and valid Date",
        ];
    }
}

