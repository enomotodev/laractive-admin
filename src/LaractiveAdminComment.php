<?php

namespace Enomotodev\LaractiveAdmin;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LaractiveAdminComment extends Authenticatable
{
    /**
     * @var array
     */
    protected $fillable = [
        'body',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
