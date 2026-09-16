<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Status;
use App\Repositories\Dashboard\Contracts\StatusRepositoryInterface;

class StatusRepository implements StatusRepositoryInterface
{
    public function allGrouped(): array
    {
        return [
            'submission' => Status::where('scope', 'submission')->orderBy('id')->get(),
            'payment' => Status::where('scope', 'payment')->orderBy('id')->get(),
        ];
    }

    public function update($status, array $data)
    {
        // slug ثابت حتى لا ينكسر الكود (الحالة تُعرف بالـ slug)
        unset($data['slug']);
        $status->update($data);
        return $status;
    }
}
