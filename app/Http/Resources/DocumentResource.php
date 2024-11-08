<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $filename = str_replace('//', '/', $this->doc_fullpath);
        return [
            'uid' => $this->doc_id,
            'name' => basename($this->doc_fullpath),
            // 'doc_type' => $this->doc_type,
            'url' => "/storage/{$filename}",
        ];
    }
}
