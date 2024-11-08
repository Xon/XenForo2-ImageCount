<?php

namespace SV\ImageCount\XF\Service\Thread;

use SV\ImageCount\XF\Entity\User as ExtendedUserEntity;

/**
 * @extends \XF\Service\Thread\Creator
 * @property \SV\ImageCount\XF\Service\Post\Preparer $postPreparer
 */
class Creator extends XFCP_Creator
{
    protected function _validate()
    {
        if ($this->performValidations)
        {
            $msgPreparer = $this->postPreparer->svMessagePreparer ?? null;
            if ($msgPreparer !== null)
            {
                /** @var ExtendedUserEntity $user */
                $user = $this->post->User ?? \XF::visitor();

                $minValue = $user->getForumMessageMinImages($this->post->Thread->Forum, $this->post);
                if ($minValue !== null)
                {
                    if (\XF::options()->svMinImageAttachCount ?? false)
                    {
                        $msgPreparer->svSetupAttachmentCount('post', $this->post->post_id, $this->postPreparer->getAttachmentHash());
                    }
                    $msgPreparer->setConstraint('minImages', $minValue);
                    $error = $msgPreparer->checkMinImages();
                    if ($error !== null)
                    {
                        $this->post->error($error);
                    }
                }
            }
        }

        return parent::_validate();
    }
}