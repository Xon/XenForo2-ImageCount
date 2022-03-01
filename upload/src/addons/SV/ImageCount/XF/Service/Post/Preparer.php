<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ImageCount\XF\Service\Post;

use SV\ImageCount\XF\Entity\User;

class Preparer extends XFCP_Preparer
{
    protected function getMessagePreparer($format = true)
    {
        $messagePreparer = parent::getMessagePreparer($format);

        $post = $this->getPost();
        $forum = $post->Thread->Forum ?? null;
        if ($forum !== null)
        {
            /** @var User $user */
            $user = \XF::visitor();
            $maxValue = $user->getForumMessageMaxImages($forum);
            if ($maxValue !== null)
            {
                $messagePreparer->setConstraint('maxImages', $maxValue);
            }
        }

        return $messagePreparer;
    }
}
