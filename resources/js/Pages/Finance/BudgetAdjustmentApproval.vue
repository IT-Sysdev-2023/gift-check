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

                        <p class="font-bold ml-2 mt-4">Checked By</p>
                        <a-select placeholder="Select Status" v-model:value="form.status" style="width: 100%"
                            :options="statusOption"></a-select>
                        <p class="font-bold ml-2 mt-4">Approved By</p>
                        <a-select placeholder="Select Status" v-model:value="form.status" style="width: 100%"
                            :options="statusOption"></a-select>
                        <p class="mt-4 font-bold ml-2">Prepared By</p>
                        <a-input :value="page.auth.user.full_name"></a-input>

                        <p class="mt-5 font-bold text-center">
                            Upload
                        </p>
                        <div class="mt-3 flex justify-center">
                            <ant-upload-image @handleChange="handleImage"></ant-upload-image>
                        </div>

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
                                request.adj_requested_at}}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Time Requested"
                                :span="3">{{ dayjs(request.adj_requested_at).format('h:mm A') }}</a-descriptions-item>
                            <a-descriptions-item style="width: 50%;" label="Adjustment Type" :span="3">{{
                                request.adj_type }}</a-descriptions-item>
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
                    <a-button class="mt-5" size="large" block ype="primary" :disabled="form.status === null" @click="submit">
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

const props = defineProps<{
    request: Request,
    id: number
}>();


const form = useForm('post', route('finance.budgetad.submit'), {
    status: null,
    file: null,
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

const handleImage = (file: any) => {
    form.file = file;
}
</script>
