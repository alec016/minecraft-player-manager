<?php

namespace Alec_016\GamePlayerManager\Models;

use Carbon\Carbon;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $host
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class RconHost extends Model
{
  protected $fillable = [
    'host',
  ];

  public function shouldDisplay(Panel $panel): bool
  {
    return true;
  }
}
