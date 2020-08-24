<?php


namespace mine_deep_rock\model;


class OneOnOneQue
{
    private $ownerName;
    private $receiverName;

    public function __construct(string $ownerName, string $receiverName) {
        $this->ownerName = $ownerName;
        $this->receiverName = $receiverName;
    }

    static function create(string $ownerName, string $receiverName): ?OneOnOneQue {
        if ($ownerName === $receiverName) return null;
        return new OneOnOneQue($ownerName, $receiverName);
    }

    /**
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string {
        return $this->receiverName;
    }
}