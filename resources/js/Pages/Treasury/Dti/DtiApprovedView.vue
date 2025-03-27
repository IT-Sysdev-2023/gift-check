<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';

const activeKey = ref<string>('1');

const props = defineProps<{
    data: {
        data: {
            data: DataItems[];
        }
        barcode: {
            data: Barcode[];
        }
    },
    title: string
}>();

interface Barcode {
    id: number;
    dti_barcode: number;
    dti_denom: string;
    lname: string;
    fname: string;
    mname: string | null;
    extname: string | null;
}
interface DataItems {
    dti_datereq: string;
    dti_paymenttype: string;
    dti_dateneed: string;
    dti_reqby: string;
    firstRemarks: string;
    amount: number;
    dti_doc: string;
    dti_approveddate: string;
    dti_approvedby: string;
    dti_checkby: string;
    dti_remarks: string;
    dti_preparedby: string;
    approved_doc: string;
}

const columns = ref([
    { title: 'Barcode', dataIndex: 'dti_barcode', key: 'dti_barcode' },
    { title: 'Denomination', dataIndex: 'dti_denom', key: 'dti_denom' },
    { title: 'Lastname', dataIndex: 'lname', key: 'lname' },
    { title: 'Firstname', dataIndex: 'fname', key: 'fname' },
    { title: 'Middlename', dataIndex: 'mname', key: 'mname' },
    { title: 'Ext', dataIndex: 'extname', key: 'extname' },
]);

const form = computed(() => {
    const items = (props.data.data.data[0] ?? {}) as DataItems;
    return {
        dti_datereq: items.dti_datereq ?? '',
        dti_paymenttype: items.dti_paymenttype ?? '',
        dti_dateneed: items.dti_dateneed ?? '',
        dti_reqby: items.dti_reqby ?? '',
        firstRemarks: items.firstRemarks ?? '',
        amount: items.amount ?? '',
        dti_doc: items.dti_doc ?? '',
        dti_approveddate: items.dti_approveddate ?? '',
        dti_approvedby: items.dti_approvedby ?? '',
        dti_checkby: items.dti_checkby ?? '',
        dti_remarks: items.dti_remarks ?? '',
        dti_preparedby: items.dti_preparedby ?? '',
        approved_doc: items.approved_doc ?? '',
    };
});
</script>

<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('treasury.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item><a :href="route('treasury.transactions.dti.dtiApprovedRequest')">DTI List
                    View</a></a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card class="mt-5">
            <div class="card-container">
                <a-tabs v-model:activeKey="activeKey" type="card">
                    <a-tab-pane key="1" tab="Special DTI GC Request">
                        <a-descriptions size="small" layout="vertical" bordered :labelStyle="{ fontWeight: 'bold' }">
                            <a-descriptions-item label="Date Requested">
                                {{ dayjs(form.dti_datereq).format('MMMM D, YYYY')
                                }}</a-descriptions-item>
                            <a-descriptions-item label="Date Validity">
                                {{ dayjs(form.dti_dateneed).format('dddd, MMMM D YYYY, h:mm A') }}</a-descriptions-item>
                            <a-descriptions-item label="Payment Type">{{ form.dti_paymenttype }}</a-descriptions-item>
                            <a-descriptions-item label="Requested By">{{ form.dti_reqby }}</a-descriptions-item>
                            <a-descriptions-item label="Remarks">{{ form.firstRemarks }}</a-descriptions-item>
                            <a-descriptions-item label="Amount">{{ form.amount }}</a-descriptions-item>
                            <a-descriptions-item label="Documents">
                                <a-image v-if="form.dti_doc" :src="'/storage/' + form.dti_doc"
                                    style="width: 200px; height: 100px" />
                            </a-descriptions-item>
                        </a-descriptions>

                        <a-descriptions title="Approved Details" size="small" layout="vertical" bordered class="mt-5"
                            :labelStyle="{ fontWeight: 'bold' }">
                            <a-descriptions-item label="Date Approved">
                                {{ dayjs(form.dti_approveddate).format('MMMM D, YYYY') }}</a-descriptions-item>
                            <a-descriptions-item label="Checked By">{{ form.dti_checkby }}</a-descriptions-item>
                            <a-descriptions-item label="Documents">
                                <a-image v-if="form.approved_doc" :src="'/storage/' + form.approved_doc"
                                    style="width: 200px; height: 100px" />
                            </a-descriptions-item>
                            <a-descriptions-item label="Approved By">{{ form.dti_approvedby }}</a-descriptions-item>
                            <a-descriptions-item label="Remarks">{{ form.dti_remarks }}</a-descriptions-item>
                            <a-descriptions-item label="Prepared By">{{ form.dti_preparedby }}</a-descriptions-item>
                        </a-descriptions>
                    </a-tab-pane>

                    <a-tab-pane key="2" tab="Barcodes">
                        <a-table :columns="columns" :data-source="props.data.barcode.data" :pagination="false" />
                        <pagination :datarecords="props.data.barcode" class="mt-5" />
                    </a-tab-pane>

                </a-tabs>
            </div>
        </a-card>
    </AuthenticatedLayout>
</template>
