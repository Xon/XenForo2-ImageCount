<?php

namespace SV\ImageCount\XF\Service\Post;

class Preparer extends XFCP_Preparer
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        $post = $this->getPost();
        /** @var \SV\ImageCount\XF\Entity\User $user */
        $user = $post->User;
        $messagePreparer->setConstraint('maxImages', $user->getForumMessageMaxImages($post->Thread->Forum));

        return $messagePreparer;
    }
}
