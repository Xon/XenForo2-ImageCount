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

    public function getAttachmentHash(): ?string
    {
        return $this->attachmentHash;
    }

    protected function getMessagePreparer($format = true)
    {
        /** @var \SV\ImageCount\XF\Service\Message\Preparer $messagePreparer */
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
                if (\XF::options()->svMinImageAttachCount ?? false)
                {
                    $messagePreparer->svSetupAttachmentCount('post', $this->post->post_id, $this->attachmentHash);
                }
                $messagePreparer->setConstraint('minImages', $minValue);
            }

            $this->svMessagePreparer = $messagePreparer;
        }

        return $messagePreparer;
    }
}
