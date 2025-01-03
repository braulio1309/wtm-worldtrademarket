<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\StatusIdFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\UserFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TransactionFilter extends FilterBuilder
{
    use StatusIdFilter, UserFilterTrait;

    public function description($description = null)
    {
        $this->whereClause('description', "%{$description}%", 'LIKE');
    }

    public function type($type = null)
    {
        $this->whereClause('type', "%{$type}%", 'LIKE');
    }

    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('description', 'LIKE', "%$search%");
        });
    }


}
