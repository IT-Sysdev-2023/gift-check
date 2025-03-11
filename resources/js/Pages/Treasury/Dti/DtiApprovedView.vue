<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';

const activeKey = ref<string>('1');

const props = defineProps<{
    data: {
        data: DataItems[];
    }
}>();

interface DataItems {
    dti_datereq: string,
    dti_paymenttype: string,
    dti_dateneed: string,
    dti_reqby: string,
    firstRemarks: string,
    amount: number,
    dti_doc: string,
    dti_approveddate: string,
    dti_approvedby: string,
    dti_checkby: string,
    dti_remarks: string,
    dti_preparedby: string,
    approved_doc: string,
    barcode: []
}
const columns = ref([
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denomination'
    },
    {
        title: 'Lastname',
        dataIndex: 'dti_lastname'
    },
    {
        title: 'Firstname',
        dataIndex: 'dti_firstname'
    },
    {
        title: 'Middlename',
        dataIndex: 'dti_middlename'
    },
    {
        title: 'Ext',
        dataIndex: 'dti_extname'
    },
])
const form = computed(() => {
    const items = props.data.data[0] || {} as DataItems;
    return {
        dti_datereq: items.dti_datereq ?? '',
        dti_paymenttype: items.dti_paymenttype ?? '',
        dti_dateneed: items.dti_dateneed ?? '',
        dti_reqby: items.dti_reqby,
        firstRemarks: items.firstRemarks ?? '',
        amount: items.amount ?? '',
        dti_doc: items.dti_doc ?? '',
        dti_approveddate: items.dti_approveddate ?? '',
        dti_approvedby: items.dti_approvedby ?? '',
        dti_checkby: items.dti_checkby ?? '',
        dti_remarks: items.dti_remarks ?? '',
        dti_preparedby: items.dti_preparedby ?? '',
        approved_doc: items.approved_doc ?? '',

    }
});
const barcodeList = computed(() => {
    return props.data.data[0].barcode;
})
</script>
<template>
    <AuthenticatedLayout>
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('treasury.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item><a :href="route('treasury.transactions.dti.dtiApprovedRequest')">DTI List
                    View</a></a-breadcrumb-item>
            <a-breadcrumb-item><a :href="route('treasury.transactions.dti.dtiApprovedView')">DTI Approved
                    View</a></a-breadcrumb-item>

        </a-breadcrumb>
        <a-card class="mt-5">
            <a-tabs v-model:activeKey="activeKey" type="card">
                <a-tab-pane key="1" tab="Special DTI GC Request">
                    <a-descriptions size="small" layout="vertical" bordered :labelStyle="{ fontWeight: 'bold' }">
                        <a-descriptions-item label="Date Requested">
                            {{ dayjs(form.dti_datereq).format('MMMM D, YYYY') }}</a-descriptions-item>
                        <a-descriptions-item label="Date Validity">
                            {{ dayjs(form.dti_dateneed).format('dddd, MMMM D YYYY, h:mm A') }}</a-descriptions-item>
                        <a-descriptions-item label="Payment Type">{{ form.dti_paymenttype }}</a-descriptions-item>
                        <a-descriptions-item label="Requested By">{{ form.dti_reqby }}</a-descriptions-item>
                        <a-descriptions-item label="Remarks">{{ form.firstRemarks }}</a-descriptions-item>
                        <a-descriptions-item label="Amount">{{ form.amount }}</a-descriptions-item>
                        <a-descriptions-item label="Documents">
                            <img :src="'/storage/' + form.dti_doc" style="width: 100px; height: 100px" />
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions title="Approved Details" size="small" layout="vertical" bordered class="mt-5"
                        :labelStyle="{ fontWeight: 'bold' }">
                        <a-descriptions-item label="Date Approved">
                            {{ dayjs(form.dti_approveddate).format('MMMM D, YYYY') }}</a-descriptions-item>
                        <a-descriptions-item label="Checked By">{{ form.dti_checkby }}</a-descriptions-item>
                        <a-descriptions-item label="Documents">
                            <img :src="'/storage/' + form.approved_doc" style="width: 100px; height: 100px" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Approved By">{{ form.dti_approvedby }}</a-descriptions-item>
                        <a-descriptions-item label="Remarks">{{ form.dti_remarks }}</a-descriptions-item>
                        <a-descriptions-item label="Prepared By">{{ form.dti_preparedby }}</a-descriptions-item>
                    </a-descriptions>
                </a-tab-pane>
                <a-tab-pane key="2" tab="Barcodes">
                    <a-card>
                        <a-table :columns="columns" :data-source="barcodeList" :pagination="false" />
                        <pagination :datarecords="props.data" class="mt-5" />
                    </a-card>
                </a-tab-pane>
            </a-tabs>
        </a-card>
        {{ data }}
    </AuthenticatedLayout>
</template>
