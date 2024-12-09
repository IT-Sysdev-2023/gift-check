<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card>
                        <p class="font-bold ml-2">Request Status</p>
                        <a-select placeholder="Select Status" v-model:value="form.status" style="width: 100%"
                            :options="statusOption"></a-select>
                        <p class="font-bold mt-4 ml-2">{{ form.status === '1' ? 'Date Approved' : form.status === '2' ?
                            'Date Cancelled' : 'Select Date Approved/Cancel' }}</p>
                        <a-input readonly :value="dayjs().format('MMMM, DD YYYY')"></a-input>
                        <div v-if="form.status === '1'">
                            <p class="font-bold ml-2 mt-4">Checked By</p>
                            <a-select placeholder="Assign Check By" v-model:value="form.checkby" style="width: 100%"
                                :options="optionAssign"></a-select>
                            <p class="font-bold ml-2 mt-4">Approved By</p>
                            <a-select placeholder="Assign Approved" v-model:value="form.appby" style="width: 100%"
                                :options="optionAssign"></a-select>
                            <p class="mt-5 font-bold text-center">
                                Upload
                            </p>
                            <div class="mt-3 flex justify-center">
                                <ant-upload-image @handleChange="handleImage"></ant-upload-image>
                            </div>
                            <p class="mt-5 ml-2 font-bold">
                                Remarks
                            </p>
                            <a-textarea v-model:value="form.remarks" :rows="4" val placeholder="Remarks..." />

                        </div>
                        <p class="mt-4 font-bold ml-2">{{ form.status === '1' ? 'Prepared By' : form.status === '2' ?
                            'Cancelled By' : 'Approved Or Cancelled By' }}</p>
                        <a-input :value="page.auth.user.full_name"></a-input>
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <a-card title="Budget Adjustment Details">
                        <a-descriptions size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Ajustment No" :span="3">{{ request.adj_no
                                }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Department" :span="3">{{
                                request.user.access_page.title }}</a-descriptions-item>
                            <a-descriptions-item v-if="request.adj_group !== '0' && request.adj_group !== null"
                                style="width: 50%;" label="Promo Group" :span="3">
                                Group {{ request.adj_group }}
                            </a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Date Requested" :span="3">{{
                                request.adj_requested_at }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Time Requested" :span="3">{{
                                dayjs(request.adj_requested_at).format('h:mm A') }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Adjustment Type" :span="3">{{
                                request.adjust_type }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Adjustment Requested" :span="3">{{
                                request.adj_request }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Request Document"
                                :span="3"></a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Request By" :span="3">{{
                                request.user.full_name }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Remarks" :span="3">{{ request.adj_remarks
                                }}</a-descriptions-item>
                        </a-descriptions>
                    </a-card>
                    <a-button class="mt-5" size="large" block type="primary" :danger="form.status === '2'" :disabled="form.status === null"
                        @click="submit" >
                        <template #icon>
                            <FastForwardOutlined />
                        </template>
                        {{ form.status === '1' ? 'Approved Request' : form.status === '2' ?
                            'Cancel Request' : 'Select Status' }}
                    </a-button>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { PageWithSharedProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { SelectProps } from 'ant-design-vue';
import dayjs from 'dayjs';
import { useForm } from 'laravel-precognition-vue';
import { ref } from 'vue';

const page = usePage<PageWithSharedProps>().props;

interface Request {
    adj_no: number,
    adj_request: string,
    adj_type: string,
    adjust_type: string,
    adj_group: string,
    adj_preapprovedby: string,
    adj_remarks: string,
    adj_requested_at: string,
    user: {
        full_name: string,
        access_page: {
            title: string
        }
    }
}
interface Assignatories {
    label: number,
    value: string,
}

const props = defineProps<{
    request: Request,
    assigned: Assignatories[]
    id: number
}>();


const form = useForm('post', route('finance.budgetad.submit'), {
    status: null,
    file: null,
    checkby: null,
    appby: null,
    remarks: null,
    atype: props.request.adjust_type,
    adjrequest: props.request.adj_request,
    bgroup: props.request.adj_group,
    recapp: props.request.adj_preapprovedby,
    id: props.id,
    btype: props.request.adj_type,

});

const submit = () => form.submit();

const statusOption = ref<SelectProps['options']>([
    {
        value: '1',
        label: 'Approved',
    },
    {
        value: '2',
        label: 'Cancel',
    },
]);
const optionAssign = ref<SelectProps['options']>(props.assigned);

const handleImage = (file: any) => {
    form.file = file;
}
</script>
