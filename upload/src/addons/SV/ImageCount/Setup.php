<?php

namespace SV\ImageCount;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function uninstallStep1()
    {
        $this->db()->query("DELETE FROM xf_permission_entry WHERE permission_id = 'sv_MaxImageCount'");
        $this->db()->query("DELETE FROM xf_permission_content_entry WHERE permission_id = 'sv_userIgnoreDisabled'");
        $this->app->jobManager()->enqueueUnique(
            'permissionRebuild',
            'XF:PermissionRebuild',
            [],
            false
        );
    }
}
