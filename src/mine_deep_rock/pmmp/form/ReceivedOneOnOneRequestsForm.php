<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\service\AcceptOneOnOneRequestService;
use mine_deep_rock\store\OneOnOneRequestsStore;
use pocketmine\Player;

class ReceivedOneOnOneRequestsForm extends SimpleForm
{

    public function __construct(Player $player) {
        $buttons = [];

        foreach (OneOnOneRequestsStore::findByReceiverName($player->getName()) as $request) {
            $buttons[] = new SimpleFormButton(
                "from " . $request->get,
                null,
                function (Player $player) use ($request) {
                    AcceptOneOnOneRequestService::execute($request);
                }
            );
        }
        parent::__construct("1on1リクエスト一覧", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        // TODO: Implement onClickCloseButton() method.
    }
}