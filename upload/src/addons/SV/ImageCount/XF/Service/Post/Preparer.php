<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ImageCount\XF\Service\Post;

use SV\ImageCount\XF\Entity\User as ExtendedUserEntity;
use SV\ImageCount\XF\Service\Message\Preparer as ExtendedPreparerService;

class Preparer extends XFCP_Preparer
{
    /** @var ExtendedPreparerService|null  */
    public $svMessagePreparer = null;

    public function getAttachmentHash(): ?string
    {
        return $this->attachmentHash;
    }

    protected function getMessagePreparer($format = true)
    {
        /** @var ExtendedPreparerService $messagePreparer */
        $messagePreparer = parent::getMessagePreparer($format);

        $post = $this->getPost();
        $forum = $post->Thread->Forum ?? null;
        if ($forum !== null)
        {
            /** @var ExtendedUserEntity $user */
            $user = $post->User ?? \XF::visitor();
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
