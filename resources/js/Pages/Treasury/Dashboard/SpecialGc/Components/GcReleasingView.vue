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
            <a-breadcrumb-item>{{ title }} {{ titl }} </a-breadcrumb-item>
        </a-breadcrumb>

        <a-card>
            <a-descriptions bordered :title="title + ' ' + titl">
                <a-descriptions-item label="RFSEGC #">{{
                    records.data.spexgc_num
                }}</a-descriptions-item>
                <a-descriptions-item label="Department">{{
                    records.data?.userAccessPageTitle
                }}</a-descriptions-item>
                <a-descriptions-item label="Date and Time Requested">{{
                    dayjs(records.data.spexgc_datereq).format(
                        "YYYY-MMM-DD HH:mm:ss a"
                    )
                }}</a-descriptions-item>
                <a-descriptions-item label="Date Needed">{{
                    records.data.spexgc_dateneed
                }}</a-descriptions-item>
                <a-descriptions-item label="Customer">{{
                    records.data.specialExternalCustomer?.spcus_acctname
                }}</a-descriptions-item>

                <a-descriptions-item label="Total Denomination"
                    >{{ records.data.totalDenom?.total }}
                    <a-button
                        type="primary"
                        size="small"
                        @click="viewDenominations"
                        >View</a-button
                    ></a-descriptions-item
                >
                <a-descriptions-item label="Payment Type"
                    >{{ records.data.spexgc_paymentype }}
                    <a-button
                        type="primary"
                        size="small"
                        v-if="records.data.spexgc_paymentype == 'Check'"
                        >View</a-button
                    ></a-descriptions-item
                >
                <a-descriptions-item label="Payment Amount">{{
                    records.data.spexgc_payment
                }}</a-descriptions-item>
                <a-descriptions-item label="Request Remarks">{{
                    records.data.spexgc_remarks
                }}</a-descriptions-item>
                <a-descriptions-item label="Requested By">{{
                    records.data.user
                }}</a-descriptions-item>
                <a-descriptions-item label="Date Approved">{{
                    records.data.approvedRequest.reqap_date
                }}</a-descriptions-item>
                <a-descriptions-item
                    label="Approved Document"
                    v-if="records.data.approvedRequest.reqap_doc"
                    >
                    <ant-image-preview :images=" records.data.approvedRequest.reqap_doc"/>
                    </a-descriptions-item
                >
                <a-descriptions-item label="Approved Remarks">{{
                    records.data.approvedRequest.reqap_remarks
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

            <a-card class="mt-10">
                <a-form :model="formState" @finish="onFinish">
                    <a-form-item label="Total Gc">
                        <a-input
                            :value="records.data.totalDenom?.qty"
                            readonly
                        />
                    </a-form-item>
                    <a-form-item label="Total Denomination">
                        <a-input
                            :value="records.data.totalDenom?.total"
                            readonly
                        />
                    </a-form-item>
                    <a-form-item
                        label="Checked By"
                        :validate-status="
                            formState.errors.checkedBy ? 'error' : ''
                        "
                        :help="formState.errors.checkedBy"
                    >
                        <ant-select
                            @handle-change="onCheckChange"
                            :options="checkBy"
                        />
                    </a-form-item>
                    <a-form-item
                        label="Remarks"
                        :validate-status="
                            formState.errors.remarks ? 'error' : ''
                        "
                        :help="formState.errors.remarks"
                    >
                        <a-textarea v-model:value="formState.remarks" />
                    </a-form-item>
                    <a-form-item
                        label="Received By"
                        :validate-status="
                            formState.errors.receivedBy ? 'error' : ''
                        "
                        :help="formState.errors.receivedBy"
                    >
                        <a-input v-model:value="formState.receivedBy" />
                    </a-form-item>
                    <a-form-item label="Released By">
                        <a-input
                            :value="page.auth.user.full_name"
                            readonly
                        />
                    </a-form-item>

                    <a-form-item class="float-right">
                        <a-button type="primary" html-type="submit"
                            >Submit</a-button
                        >
                    </a-form-item>
                </a-form>
            </a-card>

            <a-modal
                v-model:open="openModal"
                title="Customer Requested GC"
                width="800px"
                :footer="null"
            >
                <a-table
                    bordered
                    size="small"
                    :pagination="false"
                    :columns="[
                        { title: 'Lastname', dataIndex: 'spexgcemp_lname' },
                        { title: 'Firstname', dataIndex: 'spexgcemp_fname' },
                        { title: 'Middlename', dataIndex: 'spexgcemp_mname' },
                        { title: 'Denomination', dataIndex: 'denom' },
                    ]"
                    :data-source="modalData.data"
                >
                </a-table>
                <pagination-axios-small
                    :datarecords="modalData"
                    @onPagination="onPaginate"
                />
            </a-modal>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import axios from "axios";
import { ref } from "vue";
import { computed } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { onProgress } from "@/Mixin/UiUtilities";
import { PageWithSharedProps } from "@/types";

const page = usePage<PageWithSharedProps>().props;
const props = defineProps<{
    title: string;
    id: number;
    checkBy: {
        value: number;
        label: string;
    }[];
    records: {
        data: any;
    };
    tableRecords: any;
}>();
// const promo = ref();
const titl = computed(() => {
    return props.records.data.spexgc_promo == 0 ? 'External' : 'Internal';
})
const formState = useForm({
    promo: props.records.data.spexgc_promo,
    checkedBy: "",
    remarks: "",
    receivedBy: "",
    releasedBy: "",
});
const { openLeftNotification } = onProgress();
const onFinish = () => {
    formState.post(route("treasury.special.gc.releasingSubmission", props.id), {preserveScroll: true, onSuccess: ({props}) => {
       
        openLeftNotification(props.flash);
        router.visit(route('treasury.special.gc.gcReleasing'));
    }});
};
const openModal = ref(false);
const modalData = ref();

const viewDenominations = async () => {
    const { data } = await axios.get(
        route("treasury.special.gc.viewDenomination", props.id)
    );
    modalData.value = data;
    openModal.value = true;
};

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
