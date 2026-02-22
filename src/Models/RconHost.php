<?php

namespace Alec_016\GamePlayerManager\Models;

use App\Models\Node;
use Carbon\Carbon;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $host
 * @property string[] $nodes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class RconHost extends Model
{
  protected $fillable = [
    'host',
    'nodes'
  ];

  protected $attributes = [
    'nodes' => '[]'
  ];

  protected function casts(): array
  {
    return [
      'nodes' => 'array'
    ];
  }

  public function shouldDisplay(Panel $panel): bool
  {
    return true;
  }

  public function containsNode(string $nodeId):bool
  {
    return in_array($nodeId, $this->nodes);
  }

  public function nodesArray(): array
  {
    return array_map(fn ($id) => Node::find($id), $this->nodes);
  }
}
