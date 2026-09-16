<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\WhatsappTemplateService;

class WhatsappTemplateController extends Controller
{
    protected $service;

    public function __construct(WhatsappTemplateService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function update(Request $request, WhatsappTemplate $template)
    {
        return $this->service->update($request, $template);
    }

    public function destroy(Request $request, WhatsappTemplate $template)
    {
        return $this->service->destroy($request, $template);
    }
}
