<?php

namespace panlatent\elementmessages\helpers;

use craft\base\ElementInterface;
use craft\elements\User;

abstract class ElementHelper
{
    public static function getFriendlyName(ElementInterface $element)
    {
        if ($element::hasTitles()) {
            return $element->title;
        }

        if ($element instanceof User) {
            return $element->getFriendlyName();
        }

        return $element::displayName();
    }
}