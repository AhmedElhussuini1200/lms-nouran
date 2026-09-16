<?php

namespace App\Repositories\Dashboard\Contracts;

interface WhatsappTemplateRepositoryInterface
{
    public function all();
    public function store(array $data);
    public function update(array $data, $template);
    public function destroy($template);
}
