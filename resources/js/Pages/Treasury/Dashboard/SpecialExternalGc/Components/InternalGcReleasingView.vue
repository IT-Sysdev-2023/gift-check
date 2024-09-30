<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('treasury.special.gc.releasingInternal')"
                    >Reviewed GC For Releasing(Internal)</Link
                >
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card>
            <a-descriptions bordered :title="title">
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
                    <a-button type="primary" size="small"
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
                    >{{
                        records.data.approvedRequest.reqap_doc
                    }}</a-descriptions-item
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

            <a-table
                class="mt-10"
                bordered
                :columns="[
                    { title: 'Lastname', dataIndex: 'lname' },
                    { title: 'Firstname', dataIndex: 'fname' },
                    { title: 'Middlename', dataIndex: 'mname' },
                    { title: 'Denomination', dataIndex: 'denom' },
                    { title: 'Barcode', dataIndex: 'bcode' },
                ]"
            >
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";

defineProps<{
    title: string;
    records: {
        data: any;
    };
}>();
</script>

<style lang="scss" scoped></style>
