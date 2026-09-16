<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\WhatsappTemplate;
use App\Repositories\Dashboard\Contracts\WhatsappTemplateRepositoryInterface;

class WhatsappTemplateRepository implements WhatsappTemplateRepositoryInterface
{
    public function all()
    {
        return WhatsappTemplate::orderBy('id')->get();
    }

    public function store(array $data)
    {
        return WhatsappTemplate::create($data);
    }

    public function update(array $data, $template)
    {
        $template->update($data);
        return $template;
    }

    public function destroy($template)
    {
        return $template->delete();
    }
}
