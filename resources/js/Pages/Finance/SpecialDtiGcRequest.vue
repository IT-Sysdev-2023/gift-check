<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import dayjs from 'dayjs';
import { router } from '@inertiajs/core';
import { defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';

const page = usePage();
const activeKey = ref('1')

const props = defineProps({
    columns: Array,
    data: Object,
    type: String,
    currentBudget: Number,
    gcHolder: Object
})


const millionFormat = (value) => {
    if (!value) return "0";
    return parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

const submitButton = () => {
    form.value.errors = {};
    if (form.value.status === "1" && !form.value.approvedRemarks) {
        form.value.errors.approvedRemarks = 'Remarks is required for approval.';
        return;
    }

    if (form.value.status === "2" && !form.value.cancelledRemarks) {
        form.value.errors.cancelledRemarks = 'Remarks is required for cancellation.';
        return;
    }

    if (form.value.file == null && form.value.status === "1") {
        notification.error({
            description: 'Upload Document is required',
        });
        return;
    }


    if (file.value) {
        const fileType = file.value.type;
        if (fileType !== 'image/jpeg' && fileType !== 'image/jpg' && fileType !== 'image/png') {
            notification.error({
                description: 'Only JPG, JPEG, and PNG files are allowed.',
            });
            return;
        }
    }

    router.post(route('finance.pendingGc.dti.approval'), {
        ...form.value,
        file: file.value

    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                notification.success({
                    message: page.props.flash.message || 'Success',
                    description: page.props.flash.description || '',
                });

            }
        }, onError: (page) => {
            if (page.props.flash.error) {
                notification.error({
                    message: page.props.flash.message || 'Error',
                    description: page.props.flash.description || '',
                });

            }
        }
    });
}
const dateRequestedFormat = (value) => {
    return dayjs(value).format('YYYY-MMMM-DD');
}

const file = ref(null);

const handleChange = (info) => {
    file.value = info.file.originFileObj;
}

const form = ref({
    data: props.data,
    type: props.type,
    currentBudget: props.currentBudget,
    gcHolder: props.gcHolder,
    file: file,
    status: '1',
    dateApproved: dayjs(),
    dateCancelled: dayjs().format('YYYY-MM-DD'),
    cancelledRemarks: '',
    approvedRemarks: '',
    checkedBy: props.data[0].checkby ?? '',
    approvedBy: page.props.auth.user.full_name ?? '',
    cancelledBy: page.props.auth.user.full_name ?? '',
    rfsegc: props.data[0].dti_num ?? '',
    department: props.data[0].title ?? '',
    dateRequested: dateRequestedFormat(props.data[0].spexgc_datereq) ?? '',
    dateNeeded: dateRequestedFormat(props.data[0].spexgc_datereq) ?? '',
    customer: props.data[0].spcus_companyname ?? '',
    preparedBy: props.data[0].prepby ?? '',
    totalDenomination: props.data[0].total ?? '',
    paymentType: props.data[0].dti_paymenttype ?? '',
    remarks: props.data[0].dti_remarks ?? '',
    errors: {}

})
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <a-tabs v-model:activeKey="activeKey" type="card">
                <a-tab-pane key="1" tab="Special DTI GC Request Approved Form">
                    <a-card>
                        <div class="text-lg flex justify-end mr-15">
                            <span class="bg-gray-900 text-white p-3 rounded shadow">
                                Current Budget: {{ millionFormat(currentBudget) }}
                            </span>
                        </div>
                        <div class="flex direction-columns gap-5 mt-5">
                            <a-card>
                                <div v-if="form.status == 1">
                                    <a-form-item label="Request Status">
                                        <a-select v-model:value="form.status">
                                            <a-select-option value="1">
                                                Approved
                                            </a-select-option>
                                            <a-select-option value="2">
                                                Cancelled
                                            </a-select-option>
                                        </a-select>
                                    </a-form-item>
                                    <a-form-item label="Date Approved">
                                        <a-input v-model:value="form.dateApproved" type="text" readonly>
                                        </a-input>
                                    </a-form-item>
                                    <a-form-item label="Remarks"
                                        :validate-status="form.errors.approvedRemarks ? 'error' : ''"
                                        :help="form.errors.approvedRemarks">
                                        <a-textarea v-model:value="form.approvedRemarks">

                                        </a-textarea>
                                    </a-form-item>
                                    <a-form-item label="Checked By">
                                        <a-input readonly v-model:value="form.checkedBy" type="text">

                                        </a-input>
                                    </a-form-item>
                                    <a-form-item label="Approved By">
                                        <a-input readonly v-model:value="form.approvedBy" type="text">

                                        </a-input>
                                    </a-form-item>
                                    <a-form-item label="Upload Document">
                                        <a-upload-dragger name="file" :multiple="false" file="file"
                                            action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
                                            @change="handleChange">
                                            <p class="ant-upload-drag-icon">
                                                <inbox-outlined></inbox-outlined>
                                            </p>
                                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                                            <p class="ant-upload-hint">
                                                JPG, JPEG, PNG only
                                            </p>
                                        </a-upload-dragger>
                                    </a-form-item>
                                </div>
                                <div v-else>
                                    <a-form-item label="Request Status">
                                        <a-select v-model:value="form.status">
                                            <a-select-option value="1">
                                                Approved
                                            </a-select-option>
                                            <a-select-option value="2">
                                                Cancelled
                                            </a-select-option>
                                        </a-select>
                                    </a-form-item>
                                    <a-form-item label="Date Cancelled">
                                        <a-input readonly v-model:value="form.dateCancelled">
                                        </a-input>
                                    </a-form-item>
                                    <a-form-item label="Remarks"
                                        :validate-status="form.errors.cancelledRemarks ? 'error' : ''"
                                        :help="form.errors.cancelledRemarks">
                                        <a-textarea v-model:value="form.cancelledRemarks">
                                        </a-textarea>
                                    </a-form-item>
                                    <a-form-item label="Checked By">
                                        <a-input readonly v-model:value="form.checkedBy">
                                        </a-input>
                                    </a-form-item>
                                    <a-form-item label="Cancelled By">
                                        <a-input readonly v-model:value="form.cancelledBy">
                                        </a-input>
                                    </a-form-item>
                                </div>
                            </a-card>
                            <a-card title="DTI Special GC Request Approval Details">
                                <div class="flex direction-columns gap-5">
                                    <div>
                                        <a-form-item label="RFSEGC #">
                                            <a-input readonly v-model:value="form.rfsegc"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Department">
                                            <a-input readonly v-model:value="form.department"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Date Requested">
                                            <a-input readonly v-model:value="form.dateRequested"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Date Needed">
                                            <a-input readonly v-model:value="form.dateNeeded"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Customer">
                                            <a-textarea readonly v-model:value="form.customer">
                                            </a-textarea>
                                        </a-form-item>
                                        <a-form-item label="Prepared By">
                                            <a-input readonly v-model:value="form.preparedBy"></a-input>
                                        </a-form-item>
                                    </div>
                                    <div>
                                        <a-form-item label="Total Denomination">
                                            <a-input readonly v-model:value="form.totalDenomination"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Payment Type">
                                            <a-input readonly v-model:value="form.paymentType"></a-input>
                                        </a-form-item>
                                        <a-form-item label="Remarks">
                                            <a-textarea readonly v-model:value="form.remarks">
                                            </a-textarea>
                                        </a-form-item>
                                    </div>
                                </div>
                            </a-card>
                        </div>
                        <div class="flex justify-end mt-5 mr-20">
                            <a-button @click="submitButton" type="primary">
                                Submit
                            </a-button>
                        </div>
                    </a-card>
                </a-tab-pane>
                <a-tab-pane key="2" tab="GC Holder Details" force-render>
                    <a-card>
                        <a-table :columns="props.columns" :data-source="props.gcHolder.data" :pagination="false"
                            :size="small">

                        </a-table>
                        <pagination :datarecords="props.gcHolder" class="mt-5" />
                    </a-card>
                </a-tab-pane>
            </a-tabs>
        </div>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
