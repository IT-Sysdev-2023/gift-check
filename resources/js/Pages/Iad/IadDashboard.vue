<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <i-card class="uppercase" title="special external gc request" :approved="count.approvedgc"
                    :recrev="count.reviewedCount" aRoute="iad.special.external.approvedGc"
                    rRoute="iad.reviewed.gc.special.review" />
            </a-col>
            <a-col :span="8">
                <i-card class="uppercase" revrecLabel="Gc Received" rRoute="iad.view.received" aRoute="iad.receiving"
                    approvedLabel="Gc Receiving" title="internal gc" :approved="count.approvedgc"
                    :recrev="count.reviewedCount" />
            </a-col>
            <a-col :span="8">
                <for-approval-pending :budget="budgetrequest" />
            </a-col>
            <a-col :span="8">
                <i-card class="uppercase" revrecLabel="Gc Received" rRoute="iad.view.received"
                    aRoute="iad.special.dti.viewDtiGc" title="special dti gc request" :approved="count.dtiApprovedCount" :recrev="2" />
            </a-col>
        </a-row>

    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps<{
    count: any;
    budgetrequest: any
}>();
</script>
