<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <GcReceiving
                    title="Special External Gc Request"
                    containerTitle1="Approved Gc For Review"
                    containerTitle2="Reviewed GC"
                    containerDesc1="For receving gift cheque"
                    containerDesc2="Received Gift Check"
                    @container1-event="approvedGcForReview"
                />
                <p>TOTal Approved GC : {{ approvedGc }}</p>
            </a-col>
            <a-col :span="12">
                <GcReceiving
                    title="Internal GC"
                    containerTitle1="GC Receiving"
                    containerTitle2="GC Received"
                    containerDesc1="For receving gift cheque"
                    containerDesc2="Received Gift Check"
                    @container1-event="gcReceiving"
                />
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";

defineProps<{
    approvedGc: number;
}>();
const approvedGcForReview = () => {
    router.get(route("iad.special.external.approvedGc"));
};
const gcReceiving = () => {
    router.get(route("iad.receiving"));
};
</script>
