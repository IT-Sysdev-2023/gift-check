<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import dayjs from 'dayjs';
const activeKey = ref('1');
const props = defineProps({
    data: Object,
    columns: Array,
    title: String
})
const openModal = ref(false);

const ViewDetails = (record) => {
    openModal.value = true;
    form.value = { ...record };
}

const form = ref({
    dti_datereq: '',
    dti_dateneed: '',
    reqBy: '',
    dti_paymenttype: '',
    totalDenom: '',
    dti_remarks: '',
    dti_doc: '',
    checkBy: '',
    dti_approvedby: '',
    dti_date: '',
    remarks: '',
    dti_documents: '',
    dti_barcodes: ''
})

const column = ref([
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode'
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom'
    },
    {
        title: 'Lastname',
        dataIndex: 'lname'
    },
    {
        title: 'Firstname',
        dataIndex: 'fname'
    },
    {
        title: 'Middlename',
        dataIndex: 'mname'
    },
    {
        title: 'Extname',
        dataIndex: 'extname'
    }
])
</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <a :href="route('iad.dashboard')">Home</a>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                {{ title }}
            </a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <a-table :data-source="props.data.data" :columns="props.columns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button type="primary" @click="ViewDetails(record)">
                            <FileSearchOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </a-card>
        <a-modal v-model:open="openModal" width="100%" :footer="false">
            <a-card>
                <a-tabs v-model:activeKey="activeKey" type="card">
                    <a-tab-pane key="1"><template #tab>
                            <FileSearchOutlined /> Reviewed Details
                        </template>
                        <div class="flex direction-columns gap-5">
                            <a-card class="w-full">
                                <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" bordered
                                    size="small">
                                    <a-descriptions-item label="Date Requested">
                                        {{ dayjs(form.dti_datereq).format('YYYY-MMMM-DD') }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="Date Needed">{{
                                        dayjs(form.dti_dateneed).format('YYYY-MMMM-DD')
                                        }}</a-descriptions-item>
                                    <a-descriptions-item label="Requested By">{{ form.reqBy }}</a-descriptions-item>
                                    <a-descriptions-item label="Payment Type">{{ form.dti_paymenttype
                                        }}</a-descriptions-item>
                                    <a-descriptions-item label="Amount">{{ form.totalDenom }}</a-descriptions-item>
                                    <a-descriptions-item label="Remarks">{{ form.dti_remarks }}</a-descriptions-item>
                                    <a-descriptions-item label="Documents">
                                        <template v-if="form.dti_documents">
                                            <a-image :src="'/storage/' + form.dti_documents"
                                                style="height: 100px; width: 200px;" />
                                        </template>
                                        <template v-else>
                                            <a-empty />
                                        </template>
                                    </a-descriptions-item>
                                </a-descriptions>
                            </a-card>
                            <a-card class="w-full">
                                <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" bordered
                                    size="small">
                                    <a-descriptions-item label="Checked By">{{ form.checkBy }}</a-descriptions-item>
                                    <a-descriptions-item label="Prepared By">{{ form.checkBy }}</a-descriptions-item>
                                    <a-descriptions-item label="Approved By"> {{ form.dti_approvedby
                                        }}</a-descriptions-item>
                                    <a-descriptions-item label="Approved Date">{{
                                        dayjs(form.dti_date).format('YYYY-MMMM-DD')
                                        }}</a-descriptions-item>
                                    <a-descriptions-item label="Remarks">{{ form.remarks }}</a-descriptions-item>
                                </a-descriptions>
                                <a-descriptions :labelStyle="{ fontWeight: 'bold' }" bordered layout="vertical">
                                    <a-descriptions-item label="Documents">
                                        <template v-if="form.dti_doc">
                                            <a-image :src="'/storage/' + form.dti_doc"
                                                style="height: 100px; width: 200px;" />
                                        </template>
                                        <template v-else>
                                            <a-empty />
                                        </template>
                                    </a-descriptions-item>
                                </a-descriptions>
                            </a-card>
                        </div>
                    </a-tab-pane>
                    <a-tab-pane key="2">
                        <template #tab>
                            <BarcodeOutlined /> Barcodes
                        </template>
                        <a-card>
                            <a-table :columns="column" :data-source="form.dti_barcodes.data" :pagination="false"
                                size="small"></a-table>
                            <pagination :datarecords="form.dti_barcodes" class="mt-5" />
                        </a-card>
                    </a-tab-pane>
                </a-tabs>
            </a-card>
        </a-modal>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
