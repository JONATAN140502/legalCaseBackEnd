<?php

namespace App\Traits;

trait UpperCaseStrings
{
    protected static function bootUpperCaseStrings()
    {
        foreach (static::getUpperCaseFields() as $field) {
            static::saving(function ($model) use ($field) {
                if (is_string($model->{$field})) {
                    $model->{$field} = strtoupper($model->{$field});
                }
            });
        }
    }

    protected static function getUpperCaseFields()
    {
        return collect((new static)->fillable ?? [])
            ->filter(function ($field) {
                return (new static)->isStringField($field);
            })->toArray();
    }

    protected function isStringField($field)
    {
        $model = new static;
        return $model->hasCast($field, 'string') ||
               in_array($field, $model->getDates());
    }
}
