<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('treasury.special.gc.gcReleasing')"
                    >Reviewed GC For Releasing</Link
                >
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" :tab="title">
                <a-card>
                    <a-descriptions bordered :title="title + ' ' + titl">
                        <a-descriptions-item label="Date Requested">{{
                            records.data.spexgc_datereq
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Requested By">{{
                            records.data?.user
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Documents"
                            ><ant-image-preview :images="records.data.document"
                        /></a-descriptions-item>
                        <a-descriptions-item label="Date Validity">{{
                            records.data.spexgc_dateneed
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Remarks">{{
                            records.data.approvedRequest.reqap_remarks
                        }}</a-descriptions-item>

                        <a-descriptions-item label="AR #"
                            >{{ records.data.spexgc_payment_arnum }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Payment Type"
                            >{{ records.data.paymentTypeFormat }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Amount">{{
                            records.data?.spexgc_payment
                        }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions bordered class="mt-10">
                        <template #title>
                            <a-typography-title :level="5"
                                >Approved Details</a-typography-title
                            >
                        </template>
                        <a-descriptions-item label="Date Approved">{{
                            records.data.spexgc_datereq
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Documents">
                            <ant-image-preview
                                :images="
                                    records.data.approvedRequest?.reqap_doc
                                "
                            />
                        </a-descriptions-item>
                        <a-descriptions-item label="Remarks">{{
                            records.data.spexgc_remarks
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Checked By">{{
                            records.data.approvedRequest.reqap_checkedby
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Approved By">{{
                            records.data.approvedRequest.reqap_approvedby
                        }}</a-descriptions-item>

                        <a-descriptions-item label="Prepared By">{{
                            records.data.approvedRequest.user?.full_name
                        }}</a-descriptions-item>
                    </a-descriptions>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Barcodes" force-render
                >
                <a-table :data-source="barcodes.data" bordered :columns="[
                    {
                        title: 'Barcode',
                        dataIndex: 'spexgcemp_barcode'
                    },
                    {
                        title: 'Denomination',
                        dataIndex: 'spexgcemp_denom'
                    },
                    {
                        title: 'Lastname',
                        dataIndex: 'spexgcemp_lname'
                    },
                    {
                        title: 'Firstname',
                        dataIndex: 'spexgcemp_fname'
                    },
                    {
                        title: 'Middle Name',
                        dataIndex: 'spexgcemp_mname'
                    },
                    {
                        title: 'Ext.',
                        dataIndex: 'spexgcemp_extname'
                    }
                ]">

                </a-table>

                </a-tab-pane
            >
        </a-tabs>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import axios from "axios";
import { ref } from "vue";
import { computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { onProgress } from "@/Mixin/UiUtilities";
import { route } from 'ziggy-js';
const props = defineProps<{
    title: string;
    records: any;
    barcodes: any;
}>();
// const promo = ref();
const titl = computed(() => {
    return props.records.data.spexgc_promo == 0 ? "External" : "Internal";
});
const formState = useForm({
    promo: props.records.data.spexgc_promo,
    checkedBy: "",
    remarks: "",
    receivedBy: "",
    releasedBy: "",
});
const activeKey = ref("1");
const { openLeftNotification } = onProgress();
// const onFinish = () => {
//     formState.post(route("treasury.special.gc.releasingSubmission", props.id), {
//         preserveScroll: true,
//         onSuccess: ({ props }) => {
//             openLeftNotification(props.flash);
//             router.visit(route("treasury.special.gc.gcReleasing"));
//         },
//     });
// };
const openModal = ref(false);
const modalData = ref();

// const viewDenominations = async () => {
//     const { data } = await axios.get(
//         route("treasury.special.gc.viewDenomination", props.id)
//     );
//     modalData.value = data;
//     openModal.value = true;
// };

const onPaginate = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        modalData.value = data;
    }
};

const onCheckChange = (val) => {
    formState.checkedBy = val;
};
</script>

<style lang="scss" scoped></style>
