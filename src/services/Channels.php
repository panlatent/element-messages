<?php

namespace panlatent\elementmessages\services;

use craft\helpers\ArrayHelper;
use panlatent\elementmessages\models\Channel;
use yii\base\Component;

class Channels extends Component
{
    /**
     * @var array|null
     */
    private ?array $channels;

    public function getAllChannels(): array
    {
        if ($this->channels === null) {
            $this->channels = [];
        }

        return $this->channels;
    }

    public function getChannelById(int $id): ?Channel
    {
        return ArrayHelper::firstWhere($this->getAllChannels(), 'id', $id);
    }

    public function getChannelByHandle(string $handle): ?Channel
    {
        return ArrayHelper::firstWhere($this->getAllChannels(), 'handle', $handle);
    }


}