<?php


namespace mine_deep_rock\model;


class OneOnOneRequest
{
    private $ownerName;
    private $receiverName;

    public function __construct(string $ownerName, string $receiverName) {
        $this->ownerName = $ownerName;
        $this->receiverName = $receiverName;
    }

    static function create(string $ownerName, string $receiverName): ?OneOnOneRequest {
        if ($ownerName === $receiverName) return null;
        return new OneOnOneRequest($ownerName, $receiverName);
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