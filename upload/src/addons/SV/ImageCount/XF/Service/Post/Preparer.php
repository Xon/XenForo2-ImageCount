<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ImageCount\XF\Service\Post;

use SV\ImageCount\XF\Entity\User;

class Preparer extends XFCP_Preparer
{
    /** @var \SV\ImageCount\XF\Service\Message\Preparer|null  */
    public $svMessagePreparer = null;

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

            $minValue = $user->getForumMessageMinImages($forum, $post);
            if ($minValue !== null)
            {
                $messagePreparer->setConstraint('minImages', $minValue);
            }

            $this->svMessagePreparer = $messagePreparer;
        }

        return $messagePreparer;
    }
}
