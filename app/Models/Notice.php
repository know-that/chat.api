<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notice extends BaseModel
{
    protected $table = 'notice';

    /**
     * 关联资源
     * @return MorphTo
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
