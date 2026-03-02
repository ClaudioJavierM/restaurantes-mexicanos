<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\CountryContext;

class CountryScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply if we're in a web context (not CLI/queue)
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        // Get current country from context
        $country = CountryContext::getCountry();

        // Apply country filter
        $builder->where($model->getTable() . '.country', $country);
    }
}
