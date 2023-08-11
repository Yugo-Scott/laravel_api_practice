<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationships
{
  public function loadRelationships(
    Model | QueryBuilder | EloquentBuilder|HasMany $for,
    ?array $relationships = null
  ): Model|QueryBuilder|EloquentBuilder|HasMany 
  {
    $relationships = $relationships ?? $this->relationships ?? [];
    foreach ($relationships as $relation) {
      $for->when(
        $this->shouldIncludeRelation($relation),
        fn ($query) => $for instanceof Model
          ? $for->load($relation)
          : $query->with($relation)
      );
    }
    return $for;
  }

  protected function shouldIncludeRelation(string $relation): bool
  {
    $include = request()->query('include');
    if (!$include) {
      return false;
    }
    $relations = array_map("trim", explode(',', $include));

    return in_array($relation, $relations);
  }
}