<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <GcReceiving
                    :countPending="count.approvedgc"
                    :recrev="count.reviewedCount"
                    title="Special External Gc Request"
                    containerTitle1="Approved Gc"
                    containerTitle2="Reviewed GC"
                    containerDesc1="For receving gift cheque"
                    containerDesc2="Received Gift Check"
                    @container1-event="approvedGcForReview"
                    @container2-event="gcReviewed"
                />
            </a-col>
            <a-col :span="8">
                <GcReceiving
                    :countPending="count.countReceiving"
                    :recrev="count.receivedcount"
                    title="Internal GC"
                    containerTitle1="GC Receiving"
                    containerTitle2="GC Received"
                    containerDesc1="For receving gift cheque"
                    containerDesc2="Received Gift Check"
                    @container1-event="gcReceiving"
                    @container2-event="gcReceived"
                />
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";

defineProps<{
    count: any;
}>();
const approvedGcForReview = () => {
    router.get(route("iad.special.external.approvedGc"));
};
const gcReceiving = () => {
    router.get(route("iad.receiving"));
};

const gcReviewed = () => {
    router.get(route("iad.reviewed.gc.special.review"));
};
const gcReceived = () => {
    router.get(route("iad.view.received"));
};
</script>
