<?php

namespace App\Http\Resources;

use App\Models\LedgerBudget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class PromoGcDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {

        return [
            'pgcreq_reqnum' => $this->pgcreq_reqnum,
            'pgcreq_datereq' => Date::parse($this->pgcreq_datereq)->toFormattedDateString(),
            'today' => now()->toFormattedDateString(),
            'time_req' => $this->pgcreq_datereq ? Date::parse($this->pgcreq_datereq)->format('H:i:s') : null,
            'dateneeded' => Date::parse($this->pgcreq_dateneeded)->toFormattedDateString(),
            'pgcreq_group' => $this->pgcreq_group,
            'total' => $this->pgcreq_total,
            'remarks' => $this->pgcreq_remarks,
            'fullname' => $this->userReqby->full_name ?? null,
            'title' => $this->userReqby->accessPage->title ?? null,
            'current' => LedgerBudget::currentBudget()
        ];
    }
}
