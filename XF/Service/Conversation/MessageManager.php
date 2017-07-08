<?php

namespace SV\ImageCount\XF\Service\Conversation;

class MessageManager extends XFCP_MessageManager
{
	protected function getMessagePreparer($format = true)
	{
		$messagePreparer = parent::getMessagePreparer($format);
		$messagePreparer->setConstraint('maxImages', $this->conversationMessage->User->getConversationMessageMaxImages());

		return $messagePreparer;
	}
}