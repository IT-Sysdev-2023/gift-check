<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const activeKey = ref('1');
const props = defineProps({
    data: Object,
    title: String
});

const dateFormat = (date) => {
    return dayjs(date).format('YYYY-MMMM-DD');

};
const form = ref({
    dti_num: props.data[0].dti_num,
    date_validity: dateFormat(props.data[0].dti_dateneed),
    id: props.data[0].id,
    request_remarks: props.data[0].dti_remarks,
    dti_doc: props.data[0].dti_doc,
    dti_approvedby: props.data[0].dti_approvedby,
    dti_department: props.data[0].dti_department,
    dti_customer: props.data[0].dti_customer,
    dti_paymenttype: props.data[0].dti_paymenttype,
    reqby: props.data[0].reqby,
    dti_approved_remarks: props.data[0].dti_approved_remarks,
    dti_doc_second: props.data[0].dti_fullpath,
    dti_datereq: props.data[0].dti_datereq,
    total_denomination: props.data[0].total,
    total_payment: props.data[0].total,
    dti_approveddate: props.data[0].dti_approveddate,
    dti_checkby: props.data[0].dti_checkby,
    dti_preparedBy: props.data[0].dti_approvedby,
    dti_barcode: props.data[0].dti_barcodes[0].dti_barcode ?? '',
    completename: props.data[0].dti_barcodes[0].completename ?? '',
    dti_denom: props.data[0].dti_barcodes[0].dti_denom ?? '',
    voucher: props.data[0].dti_barcodes[0]?.voucher ?? '',
    address: props.data[0].dti_barcodes[0].address ?? '',
    final_remarks: '',
    barcode: '',
    errors: {},
    total_gcScan: '',
    final_total_denomination: '',
    totalBarcode: props.data[0].totalBarcode

});

const openScanModal = ref(false);


const SubmitBtn = async () => {
    form.value.errors = {};
    try {
        if (Number(form.value.total_gcScan) < Number(form.value.totalBarcode)) {
            notification.warning({
                message: 'Oops',
                description: 'Please scan all barcodes first before submitting'
            });
            return;
        }

        const response = await axios.post(route('iad.special.dti.dti.review'), {

            id: props.data[0]?.dti_num,
            remarks: form.value.final_remarks,
            finalDenomination: form.value.final_total_denomination,
            totalGcScan: form.value.total_gcScan
        })

        if (response.data.success) {
            notification.success({
                message: response.data.message,
                description: response.data.description
            });
            router.visit(response.data.redirect);
        }
        else if (response.data.error) {
            notification.error({
                message: response.data.message,
                description: response.data.description
            });
        }
    } catch (error) {
        console.error('Failed', error);
        if (error.response && error.response.status) {
            const errors = error.response.data.errors;
            if (errors.remarks) {
                form.value.errors.final_remarks = errors.remarks[0];
            }
        }
    }
}

const scanBarcodeBtn = async () => {
    form.value.errors = {};
    try {
        const response = await axios.post(route('iad.special.dti.dti_scan_barcode'), {
            id: props.data[0].dti_num,
            barcode: form.value.barcode
        })
        if (response.data.success) {
            form.value.total_gcScan = response.data.countSession;
            form.value.final_total_denomination = response.data.denominationSession;
            notification.success({
                message: response.data.message,
                description: response.data.description
            });
            openScanModal.value = false;

        } else if (response.data.error) {
            notification.error({
                message: response.data.message,
                description: response.data.description
            });
        }

    } catch (error) {
        console.error('Failed', error)
        if (error.response && error.response.status) {
            form.value.errors.barcode = error.response.data.errors.barcode[0];
        }
    }
}

const columns = ref([
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom'
    },
    {
        title: 'Voucher',
        dataIndex: 'voucher'
    },
    {
        title: 'Complete Name',
        dataIndex: 'completename'
    },
    {
        title: 'Address',
        dataIndex: 'address'
    },
])

const reprintGc = () => {
    alert('No function yet...');
}

</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link :href="route('iad.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('iad.special.dti.viewDtiGc')">
                Dti Gc Request List</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <a-tabs v-model:activeKey="activeKey" type="card">
                <a-tab-pane key="1" tab="Special DTI GC Details">
                    <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" bordered size="small">
                        <a-descriptions-item label="RFSEGC #" v-model:value="form.dti_num">{{ form.dti_num
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Date Validity" v-model:value="form.date_validity">{{
                            form.date_validity }}</a-descriptions-item>
                        <a-descriptions-item label="AR #" v-model:value="form.id">{{ form.id }}</a-descriptions-item>
                        <a-descriptions-item label="Request Remarks" v-model:value="form.request_remarks">{{
                            form.request_remarks }}</a-descriptions-item>
                        <a-descriptions-item label="Documents" v-model:value="form.dti_doc">
                            <img :src="'/storage/' + form.dti_doc" class="w-40 h-auto rounded shadow-md" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Approved By" v-model:value="form.dti_approvedby">{{
                            form.dti_approvedby }}</a-descriptions-item>
                        <a-descriptions-item label="Department" v-model:value="form.dti_department">{{
                            form.dti_department }}</a-descriptions-item>
                        <a-descriptions-item label="Customer" v-model:value="form.dti_customer">{{ form.dti_customer
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Payment Type" v-model:value="form.dti_paymenttype">{{
                            form.dti_paymenttype }}</a-descriptions-item>
                        <a-descriptions-item label="Requested By" v-model:value="form.reqby">{{ form.reqby
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Approved Remarks" v-model:value="form.dti_approved_remarks">{{
                            form.dti_approved_remarks
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Documents" v-model:value="form.dti_doc_second">
                            <img :src="'/storage/' + form.dti_doc_second" class="w-40 h-auto rounded shadow-md" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Date Requested" v-model:value="form.dti_datereq">{{ form.dti_datereq
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Total Denomination" v-model:value="form.total_denomination">{{
                            form.total_denomination
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Payment Amount" v-model:value="form.total_payment">{{
                            form.total_payment }}</a-descriptions-item>
                        <a-descriptions-item label="Date Approved" v-model:value="form.dti_approveddate">{{
                            form.dti_approveddate }}</a-descriptions-item>
                        <a-descriptions-item label="Checked By" v-model:value="form.dti_checkby">{{ form.dti_checkby
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Prepared By" v-model:value="form.dti_preparedBy">{{
                            form.dti_preparedBy }}</a-descriptions-item>
                    </a-descriptions>

                    <div class="flex direction-col gap-5 mt-5">
                        <a-card class="w-full flex justify-center">
                            <a-button @click="() => openScanModal = true" class="w-full" type="primary">
                                <ScanOutlined /> Scan GC
                            </a-button>
                            <a-modal v-model:open="openScanModal" :footer="false">
                                <p class="text-center text-lg">Scan Gc</p>
                                <div class="w-full mt-8">
                                    <a-form-item :validate-status="form.errors.barcode ? 'error' : ''"
                                        :help="form.errors.barcode">
                                        <span>Scan Barcode:</span>
                                        <a-input v-model:value="form.barcode" placeholder="Scan Barcode">
                                        </a-input>
                                    </a-form-item>
                                </div>
                                <a-button @click="scanBarcodeBtn" type="primary" class="w-full">
                                    <ScanOutlined /> Scan Barcode
                                </a-button>
                            </a-modal>
                            <a-button @click="reprintGc" type="primary" class="w-full mt-5">
                                <PrinterOutlined /> Reprint GC
                            </a-button>
                        </a-card>
                        <a-card class="w-full">
                            <a-form-item :validate-status="form.errors.final_remarks ? 'error' : ''"
                                :help="form.errors.final_remarks" label="Remarks">
                                <a-textarea v-model:value="form.final_remarks">
                                </a-textarea>
                            </a-form-item>
                            <a-form-item label="Total GC Scanned">
                                <a-input readonly v-model:value="form.total_gcScan"></a-input>
                            </a-form-item>
                            <a-form-item label="Total Denomination">
                                <a-input readonly v-model:value="form.final_total_denomination"></a-input>
                            </a-form-item>
                            <a-button @click="SubmitBtn" type="primary" class="w-full">
                                <SendOutlined /> Submit
                            </a-button>

                        </a-card>
                    </div>
                </a-tab-pane>
                <a-tab-pane key="2" tab="GC Holder">
                    <a-table :data-source="props.data[0]?.dti_barcodes ?? []" :columns="columns">
                    </a-table>
                </a-tab-pane>
            </a-tabs>
        </a-card>
        <!-- {{ props.data }} -->
    </AuthenticatedLayout>
</template>
