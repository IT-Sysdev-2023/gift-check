<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <m-card
            title="GC Production Request"
            :pending="gcProductionRequest.pendingRequest"
            :approved="gcProductionRequest.approvedRequest"
            :cancelled="gcProductionRequest.cancelledRequest"
            pRoute="marketing.pendingRequest.checker.pending.request"
            aRoute="marketing.approvedRequest.approved.request"
            cRoute="marketing.promoGcRequest.cancelled.list"
            />
        </a-col>
        <a-col :span="8">
           
        </a-col>
        <a-col :span="8">
            <div class="mb-2">
                <a-card title="Current Budget">
                    <h2>₱ {{ currentBudget }}</h2>
                </a-card>
            </div>
        </a-col>
    </a-row>
</template>

<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import GcProductionReq from "./Card/GcProductionReq.vue";



export default {
    layout: Authenticatedlayout,

    props: {
        currentBudget: String,
        gcProductionRequest: Object,
    },
};
</script>
