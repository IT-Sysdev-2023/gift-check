<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $approvedBy = ApprovedRequest::where('reqap_trid', $this->pgcreq_id)->where('reqap_approvedtype','promo gc approved')->value('reqap_approvedby');

        return [
            'req_no' => $this->pgcreq_reqnum,
            'date_req' => $this->pgcreq_datereq->toDayDateTimeString(),
            'req_id' => $this->pgcreq_id,
            'date_needed' => $this->pgcreq_dateneeded->toFormattedDateString(),
            'total' => NumberHelper::currency($this->pgcreq_total),
            'status' => $this->pgcreq_relstatus,
            'user' => $this->whenLoaded('userReqby', fn ($name)=> $name->fullname),
            'approved_by' => $this->approved_by,

            'approved_by_type' => $approvedBy,
            'approved_request_user' => $this->whenLoaded('approvedReq')
        ];
    }
}

