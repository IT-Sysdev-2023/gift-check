<template>
    <Head title="Dashboard" />
    <!-- sample of  -->
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <i-card class="uppercase" title="special external gc request" :approved="count.approvedgc"
                    :recrev="count.reviewedCountSpecial" aRoute="iad.special.external.approvedGc"
                    rRoute="iad.reviewed.gc.special.review" />
            </a-col>
            <a-col :span="8">
                <i-card class="uppercase" revrecLabel="Gc Received" rRoute="iad.view.received" aRoute="iad.receiving"
                    approvedLabel="Gc Receiving" title="internal gc" :approved="count.countReceiving"
                    :recrev="count.reviewedCount" />
            </a-col>
            <a-col :span="8">
            </a-col>
            <a-col :span="8">
                <i-card class="uppercase" revrecLabel="Gc Received" rRoute="iad.special.dti.dtiGcReviewed"
                    aRoute="iad.special.dti.viewDtiGc" title="special dti gc request" :approved="count.dtiApprovedCount"
                    :recrev="count.dtiReceivedCount" />
            </a-col>
        </a-row>
        <template #title>
            <div>Review Budget Request</div>
        </template>
        <a-float-button :disabled="apreq == 0" @click="handleClick" :badge="{ count: apreq, color: 'blue' }">
            <template #tooltip>
                Review Budget Request?
            </template>
        </a-float-button>
<!-- mao ni akong gi utro -->
        <a-modal class="text-center" title="Budget Request Reviewing" :width="600" style="top: 40px;"  v-model:open="modal" :footer="null">
            <for-approval-pending :budget="budgetrequest" @close="closeModal"/>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref } from "vue";

const props = defineProps<{
    count: any;
    budgetrequest: any
}>();
const modal = ref<boolean>(false);

const handleClick = () => {
    modal.value = true;
};
const apreq = computed(() => {
    return props.budgetrequest === null ? 0 : 1;
});

const closeModal = () => {
    modal.value = false;
}
</script>
