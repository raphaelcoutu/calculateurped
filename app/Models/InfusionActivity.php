<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfusionActivity extends Model
{
    protected $table = 'infusion_activity';

    protected $fillable = ['infusion_drug_id', 'user_id', 'action', 'changes'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    /** @return BelongsTo<InfusionDrug, $this> */
    public function drug(): BelongsTo
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
