<?php

namespace SV\ImageCount\XF\Service\Post;

class Preparer extends XFCP_Preparer
{
	protected function getMessagePreparer($format = true)
	{
		$messagePreparer = parent::getMessagePreparer($format);

		$post = $this->getPost();

		$messagePreparer->setConstraint('maxImages', $post->User->getForumMessageMaxImages($post->Thread->Forum));

		return $messagePreparer;
	}
}