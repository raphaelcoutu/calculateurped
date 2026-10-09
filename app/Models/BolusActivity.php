<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BolusActivity extends Model
{
    protected $table = 'bolus_activity';

    protected $fillable = ['bolus_id', 'user_id', 'action', 'changes'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    /** @return BelongsTo<Bolus, $this> */
    public function bolus(): BelongsTo
    {
        return $this->belongsTo(Bolus::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
