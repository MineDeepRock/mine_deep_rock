<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use mine_deep_rock\service\AddCorePvPMapDataService;
use pocketmine\Player;

class AddCorePvPMapDataForm extends CustomForm
{

    private $name;

    public function __construct() {
        $this->name = new Input("", "", "");
        parent::__construct("CorePvPMapを登録", [
            $this->name,
        ]);
    }

    function onSubmit(Player $player): void {
        AddCorePvPMapDataService::execute($this->name->getResult());
        $player->sendForm(new CorePvPMapDataListForm());
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CorePvPMapDataListForm());
    }
}
