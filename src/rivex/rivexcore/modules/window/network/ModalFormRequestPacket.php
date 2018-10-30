<?php

namespace rivex\rivexcore\modules\window\network;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\handler\SessionHandler;
use pocketmine\network\mcpe\protocol\DataPacket;

class ModalFormRequestPacket extends DataPacket
{

    const NETWORK_ID = ProtocolInfo::MODAL_FORM_REQUEST_PACKET;

    /** @var int */
    public $formId;
    /** @var string */
    public $formData; //json

    public function decodePayload(): void
    {
        $this->formId = $this->getUnsignedVarInt();
        $this->formData = $this->getString();
    }

    public function encodePayload(): void
    {
        $this->putUnsignedVarInt($this->formId);
        $this->putString($this->formData);
    }

    public function handle(SessionHandler $session): bool
    {
        return true;
    }
}
