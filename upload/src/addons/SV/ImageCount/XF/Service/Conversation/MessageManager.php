<?php

namespace SV\ImageCount\XF\Service\Conversation;

class MessageManager extends XFCP_MessageManager
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        /** @var \SV\ImageCount\XF\Entity\User $user */
        $user = $this->conversationMessage->User;
        $messagePreparer->setConstraint('maxImages', $user->getConversationMessageMaxImages());

        return $messagePreparer;
    }
}
