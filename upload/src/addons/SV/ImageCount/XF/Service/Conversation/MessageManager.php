<?php

namespace SV\ImageCount\XF\Service\Conversation;

class MessageManager extends XFCP_MessageManager
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        /** @var \SV\ImageCount\XF\Entity\User $user */
        $user = \XF::visitor();
        $maxValue = $user->getConversationMessageMaxImages();
        if ($maxValue !== false)
        {
            $messagePreparer->setConstraint('maxImages', $maxValue);
        }

        return $messagePreparer;
    }
}
