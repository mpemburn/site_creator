<?php

namespace App\Services\Populators;
class OptionsPopulator extends Populator
{
    protected array $replace = [
        'siteurl' => '',
        'home' => '',
        'blogname' => '',
        'blogdescription' => '',
        'admin_email' => '',
        'template' => '',
        'stylesheet' => '',
    ];

    protected function doReplacements(): void
    {
        $this->replace['siteurl'] = $this->infoService->destUrl;
        $this->replace['home'] = $this->infoService->destUrl;
        $this->replace['template'] = $this->infoService->theme;
    }
}
