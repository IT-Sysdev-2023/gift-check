<template>
    <a-row class="mb-4" :gutter="[16, 16]">

        <a-col :span="8">
            <budget-statistics :title="'Current Budget'" :count="count.budgetCounts.curBudget" />
        </a-col>
        <a-col :span="8">
            <budget-statistics :title="'Special GC (Promotional)'" :count="count.budgetCounts.spgc" />
        </a-col>
        <!-- <a-col :span="8">
            <budget-statistics :title="'DTI GC Deprecated'" :count="count.budgetCounts.dti" />
        </a-col> -->
        <a-col :span="8">
            <budget-statistics title="Special GC DTI (Promotional)" :count="count.budgetCounts.dti_new" />
        </a-col>
    </a-row>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <m-card class="uppercase" title="Promo gc request" :pending="count?.penPomoCount"
                :approved="count.appPromoCount" pRoute="finance.pen.promo.request" aRoute="finance.app.promo.request" />
            <!-- <PromoGcViewCard :count="count"></PromoGCViewCard> -->

            <!-- DTI Special GC Request  -->
            <m-card class="uppercase" title="DTI SPECIAL GC REQUEST" :pending="count.dtiCounts.pending"
                pRoute="finance.pendingGc.dti.request.pending" :approved="count.dtiCounts.approved"
                aRoute="finance.request.approve" :cancelled="count.dtiCounts.cancelled"
                cRoute="finance.cancelledGc.dti_cancelled" />

        </a-col>
        <a-col :span="8">
            <f-card class="uppercase" title="special gc request" pendingLabel="Internal Pending"
                :pending="count.specialGcRequest.internal" pRoute="finance.pendingGc.pending"
                aRoute="finance.approvedGc.approved" :approved="count.specialGcRequest.approve">

                <a-badge :count="count.specialGcRequest.external" class="mb-2">
                    <a-button class="mb-2" style="width: 340px" :overflow-count="Infinity" type="primary"
                        :disabled="count.specialGcRequest.external == 0"
                        @click="() => $inertia.get(route('finance.pendingGc.pending'), { type: 'external' })" danger>
                        External Pending
                    </a-button>
                </a-badge>
                <a-badge :count="count.specialGcRequest.internal" class="mb-2">
                    <a-button class="mb-2" style="width: 340px" :overflow-count="Infinity" type="primary"
                        :disabled="count.specialGcRequest.internal == 0"
                        @click="() => $inertia.get(route('finance.pendingGc.pending'), { type: 'internal' })" danger>
                        Internal Pending
                    </a-button>
                </a-badge>
            </f-card>
        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
            <m-card title="budget request" class="uppercase" :pending="count.budgetRequest.pending"
                :approved="count.budgetRequest.approved" pRoute="finance.budget.pending"
                aRoute="finance.budget.approved" />

        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
            <!-- <budget-adjustments :count="count" /> -->
        </a-col>
    </a-row>
    <!-- {{ count }} -->
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: Authenticatedlayout,
    props: {
        count: Array,
        pendingExGcRequest: Object,
        pendingInGcRequest: Object,
        columns: Object,
        currentbudget: Object,
    }
};
</script>
