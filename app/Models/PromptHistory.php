<?php

namespace App\Models;

use Database\Factories\PromptHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $job_description
 * @property string $used_prompt
 * @property string $generated_proposal
 * @property Carbon|null $created_at
 */
#[Fillable(['job_description', 'used_prompt', 'generated_proposal'])]
class PromptHistory extends Model
{
    /** @use HasFactory<PromptHistoryFactory> */
    use HasFactory;

    public const null UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prompt_history';
}
