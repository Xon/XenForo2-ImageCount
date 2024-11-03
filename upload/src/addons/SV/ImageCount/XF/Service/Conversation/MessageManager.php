<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ImageCount\XF\Service\Conversation;

use SV\ImageCount\XF\Entity\User as ExtendedUserEntity;

class MessageManager extends XFCP_MessageManager
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        /** @var ExtendedUserEntity $user */
        $user = \XF::visitor();
        $maxValue = $user->getConversationMessageMaxImages();
        if ($maxValue !== null)
        {
            $messagePreparer->setConstraint('maxImages', $maxValue);
        }

        return $messagePreparer;
    }
}
