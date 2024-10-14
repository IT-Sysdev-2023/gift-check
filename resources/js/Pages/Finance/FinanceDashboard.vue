<template>
    <a-row class="mb-4" :gutter="[16, 16]">
        <a-col :span="8">
            <budget-statistics :title="'Current Budget'" :count="count.budgetCounts.curBudget"/>
        </a-col>
        <a-col :span="8">
            <budget-statistics :title="'Special Gc (Promotional)'" :count="count.budgetCounts.spgc"/>
        </a-col>
        <a-col :span="8">
            <budget-statistics :title="'DTI GC'" :count="count.budgetCounts.dti"/>
        </a-col>
    </a-row>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
             <PromoGCViewCard :count="count"></PromoGCViewCard>
        </a-col>
        <a-col :span="8">
            <special-pending-request-finance :count="count.specialGcRequest" :pendingExGcRequest :columns
                :pendingInGcRequest :currentbudget />
            <!-- <PromoGCCard/> -->
        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
            <budget-request-approval :count="count" />
        </a-col>
    </a-row>
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
