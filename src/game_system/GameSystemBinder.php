<?php


namespace game_system;


use game_system\listener\TwoTeamGameListener;
use game_system\listener\UsersListener;
use game_system\listener\WeaponListener;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\scheduler\TaskScheduler;

class GameSystemBinder
{
    //TODO:あんまり良くないと思う
    private static $instance;

    private $gameListener;
    private $usersListener;
    private $weaponListener;
    private $usersService;
    private $weaponsService;
    private $gameScoresService;

    public function __construct(TaskScheduler $scheduler) {
        $this->usersService = new UsersService();
        $this->weaponsService = new WeaponsService();
        $this->gameScoresService = new GameScoresService();

        $this->gameListener = new TwoTeamGameListener(
            $this->usersService,
            $this->weaponsService,
            $this->gameScoresService,
            $scheduler
        );
        $this->usersListener = new UsersListener(
            $this->usersService,
            $this->weaponsService
        );
        $this->weaponListener = new WeaponListener(
            $this->usersService,
            $this->weaponsService
        );

        self::$instance = $this;
    }

    public static function getInstance(): GameSystemBinder {
        return self::$instance;
    }

    /**
     * @return TwoTeamGameListener
     */
    public function getGameListener(): TwoTeamGameListener {
        return $this->gameListener;
    }

    /**
     * @return UsersListener
     */
    public function getUsersListener(): UsersListener {
        return $this->usersListener;
    }

    /**
     * @return WeaponListener
     */
    public function getWeaponListener(): WeaponListener {
        return $this->weaponListener;
    }
}