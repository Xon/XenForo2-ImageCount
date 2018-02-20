<?php

namespace SV\ImageCount\XF\Service\Post;

class Preparer extends XFCP_Preparer
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        $post = $this->getPost();
        /** @var \SV\ImageCount\XF\Entity\User $user */
        $user = \XF::visitor();
        $maxValue = $user->getForumMessageMaxImages($post->Thread->Forum);
        if ($maxValue !== false)
        {
            $messagePreparer->setConstraint('maxImages', $maxValue);
        }

        return $messagePreparer;
    }
}
