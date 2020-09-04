<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\GunSystem;
use gun_system\pmmp\event\BulletHitEvent;
use gun_system\pmmp\event\BulletHitNearEvent;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\skill\assault_soldier\DontGiveUp;
use mine_deep_rock\model\skill\engineer\StopProgress;
use mine_deep_rock\model\skill\normal\Cover;
use mine_deep_rock\model\skill\normal\QuickRunAway;
use mine_deep_rock\model\skill\scout\LuminescentBullet;
use mine_deep_rock\model\skill\scout\SavingBullet;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\entity\NPCBase;
use mine_deep_rock\pmmp\service\SpotEnemyPMMPService;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use team_game_system\TeamGameSystem;

class GunListener implements Listener
{
    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onBulletHit(BulletHitEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();
        $damage = $event->getDamage();

        if ($victim instanceof Player && $victim->getLevel()->getName() === "lobby") return;
        if ($victim instanceof NPCBase) return;
        if ($victim instanceof CadaverEntity) return;

        if ($attacker instanceof Player && $victim instanceof Player) {
            $attackerData = TeamGameSystem::getPlayerData($attacker);
            $victimData = TeamGameSystem::getPlayerData($victim);
            if ($attackerData->getTeamId() === null || $victimData->getTeamId() === null) return;
            if (!$attackerData->getTeamId()->equals($victimData->getTeamId())) {
                $attackerEquipments = PlayerEquipmentsDAO::get($attacker->getName());

                if ($attacker->getHealth() <= 4) {
                    if ($attackerEquipments->isSelectedSkill(new DontGiveUp())) {
                        $damage += ($damage / 10);
                    }
                }

                if ($attackerEquipments->isSelectedSkill(new LuminescentBullet())) {
                    if ($attacker->distance($victim) >= 20) {
                        SpotEnemyPMMPService::execute($attacker, $victim, 2 * 20, $this->scheduler);
                    }
                }

                if ($attackerEquipments->isSelectedSkill(new SavingBullet())) {
                    if (rand(0, 6) === 1) {
                        GunSystem::giveAmmo($attacker, $attacker->getInventory()->getHeldItemIndex(), 1);
                    }
                }

                $source = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageEvent::CAUSE_CONTACT, $damage, [], 0);
                $source->call();
                $victim->setLastDamageCause($source);

                $victim->setHealth($victim->getHealth() - $damage);
            }
        } else {
            $source = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageEvent::CAUSE_CONTACT, $damage, [], 0);
            $source->call();
            $victim->setLastDamageCause($source);

            $victim->setHealth($victim->getHealth() - $damage);
        }
    }

    public function onBulletHitNear(BulletHitNearEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();

        $attackerData = TeamGameSystem::getPlayerData($attacker);
        $victimData = TeamGameSystem::getPlayerData($victim);
        if ($attackerData->getTeamId() === null || $victimData->getTeamId() === null) return;
        if (!$attackerData->getTeamId()->equals($victimData->getTeamId())) {
            $attackerEquipments = PlayerEquipmentsDAO::get($attacker->getName());
            $victimEquipments = PlayerEquipmentsDAO::get($attacker->getName());
            $second = 5 * 20;
            $level = 1;

            if ($victimEquipments->isSelectedSkill(new Cover())) $second -= 2;

            if ($victimEquipments->isSelectedSkill(new QuickRunAway())) {
                $level = $victimEquipments->getMilitaryDepartment()->getName() === MilitaryDepartment::AssaultSoldier ?
                    1 : 0;
                $victim->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 3 * 20, $level, false));
            }

            if ($attackerEquipments->isSelectedSkill(new StopProgress())) {
                $level += 3;
            }

            GunSystem::giveScare($victim, $second * 20, $level);
        }
    }
}