<?php
namespace App\Traits;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    /**
     * Get translatable attributes array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }
        return $attributes;
    }

    /**
     * Parse value to json and unescape unicode.
     *
     * @return string
     */
    protected function asJson($value): string
    {
        return json_encode($value,JSON_UNESCAPED_UNICODE);
    }
}
