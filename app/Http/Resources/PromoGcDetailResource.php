<?php

namespace App\Http\Resources;

use App\Models\ApprovedRequest;
use App\Models\LedgerBudget;
use App\Models\User;
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

        $approvedBy = ApprovedRequest::where('reqap_trid', $this->pgcreq_id)->where('reqap_approvedtype', 'promo gc preapproved')->value('reqap_preparedby');

        return [
            'pgcreq_id' => $this->pgcreq_id,
            'pgcreq_reqnum' => $this->pgcreq_reqnum,
            'pgcreq_group_status' => $this->pgcreq_group_status,
            'pgcreq_datereq' => Date::parse($this->pgcreq_datereq)->toFormattedDateString(),
            'today' => now()->toFormattedDateString(),
            'time_req' => $this->pgcreq_datereq ? Date::parse($this->pgcreq_datereq)->format('H:i:s') : null,
            'dateneeded' => Date::parse($this->pgcreq_dateneeded)->toFormattedDateString(),
            'pgcreq_group' => $this->pgcreq_group,
            'total' => $this->pgcreq_total,
            'remarks' => $this->pgcreq_remarks,
            'fullname' => $this->userReqby->full_name ?? null,
            'title' => $this->userReqby->accessPage->title ?? null,
            'current' => LedgerBudget::currentBudget(),
            'checkby' => User::select('firstname', 'lastname')->where('user_id', $approvedBy)->value('full_name'),
        ];
    }
}
