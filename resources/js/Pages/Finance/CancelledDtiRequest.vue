<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const activeKey = ref('1');

const props = defineProps({
    columns: Object,
    data: Object
})

const openCancelModal = ref(false);

const form = ref({
    dateRequested: '',
    dti_paymenttype: '',
    bcredit_amt: '',
    dti_cancelled_date: '',
    dti_checkby: '',
    prepared_by: '',
    dti_cancelled_remarks: '',
    dti_cancelled_by: ''
});

// ['dti_num', 'date_requested', 'date_requested', 'dti_customer', 'dti_cancelled_date', 'dti_cancelled_by', 'View']

const selectedRecord = async (data) => {

    form.value = {
        dateRequested: data.date_requested || '',
        dti_paymenttype: data.dti_paymenttype || '',
        bcredit_amt: data.bcredit_amt || '',
        dti_cancelled_date: data.dti_cancelled_date || '',
        dti_checkby: data.dti_checkby || '',
        prepared_by: data.prepared_by || '',
        dti_cancelled_remarks: data.dti_cancelled_remarks || '',
        dti_cancelled_by: data.dti_cancelled_by || ''
    }
    await axios.get(route('finance.cancelledGc.dti_cancelled'), {
        params: {
            id: data.dti_num
        }
    })
    openCancelModal.value = true;
}
</script>
<template>
    <AuthenticatedLayout>
        <a-card>
            <a-card>
                <div class="flex items-center">
                    <p class="text-lg">Cancelled DTI Request</p>
                    <Link :href="route('finance.dashboard')" class="ml-auto text-black text-blue-700">
                    <RollbackOutlined /> Back to Dashboard
                    </Link>
                </div>
            </a-card>
            <div class="mt-5">
                <a-table :columns="props.columns" :data-source="data">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'View'">
                            <a-button type="primary" @click="selectedRecord(record)">
                                <PicLeftOutlined /> View
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </div>
        </a-card>
        <a-modal v-model:open="openCancelModal" width="70%" :footer="false">
            <a-tabs v-model:activeKey="activeKey" type="card">
                <a-tab-pane tab="Cancelled DTI Request" key="1">
                    <div class="flex direction-columns gap-5">
                        <a-card>
                            <a-form-item label="Date Requested">
                                <a-input v-model:value="form.dateRequested" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Date Validity">
                                <a-input v-model:value="form.dateRequested" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Payment Type">
                                <a-input v-model:value="form.dti_paymenttype" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Amount">
                                <a-input v-model:value="form.bcredit_amt" readonly></a-input>
                            </a-form-item>
                        </a-card>
                        <a-card>
                            <a-form-item label="Date Cancelled">
                                <a-input v-model:value="form.dti_cancelled_date" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Checked By">
                                <a-input v-model:value="form.dti_checkby" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Prepared By">
                                <a-input v-model:value="form.prepared_by" readonly></a-input>
                            </a-form-item>
                        </a-card>
                        <a-card>
                            <a-form-item label="Remarks">
                                <a-input v-model:value="form.dti_cancelled_remarks" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Cancelled By">
                                <a-input v-model:value="form.dti_cancelled_by" readonly></a-input>
                            </a-form-item>
                        </a-card>
                    </div>
                </a-tab-pane>
                <a-tab-pane tab="Barcode" key="2">
                    <p>KUPAL KABA BOSS ? KASI ANG LAKI MONG TANGA

                    </p>
                </a-tab-pane>
            </a-tabs>
        </a-modal>
        {{ data }}

    </AuthenticatedLayout>
</template>
