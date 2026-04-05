<?php

namespace App\Livewire\Admin;

use App\Support\Admin\AdminContextResolver;
use Livewire\Component;

abstract class AdminPageComponent extends Component
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function adminLayoutData(array $data = []): array
    {
        return array_merge(app(AdminContextResolver::class)->resolve(), $data);
    }
}
