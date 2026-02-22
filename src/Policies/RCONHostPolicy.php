<?php

namespace Alec_016\GamePlayerManager\Policies;

use App\Policies\DefaultAdminPolicies;

class RCONHostPolicy
{
  
    use DefaultAdminPolicies;

    protected string $modelName = 'rconhost';
}