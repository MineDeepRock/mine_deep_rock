<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\simple_form_elements\SimpleFormImage;
use form_builder\models\simple_form_elements\SimpleFormImageType;
use form_builder\models\SimpleForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GunForSaleListForm extends SimpleForm
{
    private $scheduler;

    public function __construct(Player $player, GunType $gunType, TaskScheduler $taskScheduler) {
        $this->scheduler = $taskScheduler;

        $ownGunsName = [];
        foreach (GunRecordDAO::getOwn($player->getName()) as $gunRecord) {
            $ownGunsName[] = $gunRecord->getName();
        }

        $buttons = [];
        /** @var Gun $gun */
        foreach (GunSystem::loadAllGuns() as $gun) {
            if ($gun->getType()->equals($gunType) && !in_array($gun->getName(), $ownGunsName)) {
                if ($gun->getName() === "MG0815") continue;

                $buttons[] = new SimpleFormButton(
                    $gun->getName(),
                    new SimpleFormImage(
                        SimpleFormImageType::Path(),
                        "textures/effective_ranges/" . $gun->getName()
                    ),
                    function (Player $player) use ($gun, $taskScheduler) {
                        $player->sendForm(new GunForSaleDetailForm($gun, $taskScheduler));
                    }
                );
            }
        }

        parent::__construct($gunType->getTypeText(), "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new GunTypeListForSaleForm($this->scheduler));
    }
}