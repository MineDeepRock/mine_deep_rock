<?php


namespace team_system;


use Closure;
use Notifier;

class TeamSystemNotifier extends Notifier
{
    private $whenTeamBecameFull;

    public function __construct(Closure $whenTeamBecameFull) {
        $this->whenTeamBecameFull = $whenTeamBecameFull;
    }

    public function teamBecameFull(){
        ($this->whenTeamBecameFull)();
    }
}