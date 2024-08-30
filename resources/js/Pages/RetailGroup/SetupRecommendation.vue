<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card>
                        <a-table :pagination="false" size="small" bordered :data-source="denom" :columns="[
                            {
                                title: 'Denomination',
                                dataIndex: 'denom',
                                key: 'name',
                                width: '40%',
                            },
                            {
                                title: 'Qty',
                                dataIndex: 'pgcreqi_qty',
                                key: 'name',
                            },
                            {
                                title: 'Subtotal',
                                dataIndex: 'subtotal',
                                width: '30%',
                            },

                        ]">

                        </a-table>
                    </a-card>
                    <a-card class="mt-3">
                        <a-row :gutter="[16, 16]">
                            <a-col :span="12">
                                <p class="ml-2 font-bold">Request Status</p>
                                <a-select ref="select" v-model:value="value1" style="width: 100%" @focus="focus"
                                    @change="handleChange">
                                    <a-select-option value="jack">Approved</a-select-option>
                                    <a-select-option value="lucy">Cancel</a-select-option>
                                </a-select>
                                <p class="ml-2 mt-2 font-bold">Remarks</p>
                                <a-textarea :rows="2" placeholder="Enter Remarks" />

                            </a-col>
                            <a-col :span="12">
                                <p class="ml-2 font-bold">Date Approved/Cancel</p>
                                <a-input v-model:value="form.today" class="text-center" readonly />
                                <p class="ml-2 mt-2 font-bold">Approved By</p>
                                <a-input :value="$page.props.auth.user.full_name" readonly class="text-center" />
                            </a-col>
                            <ant-upload-image class="ml-2" @handleChange="imageHandler" />

                        </a-row>
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <pending-gc-description :record="record" />
                    <div class="flex justify-end">
                        <a-button type="primary" class="mt-4">
                            <template #icon>
                                <FastForwardOutlined />
                            </template>
                            Submit Recommendation
                        </a-button>
                    </div>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
defineProps({
    record: Object,
    denom: Object,
    total: Number,
})
const form = useForm({
    today: dayjs().format('MMMM D YYYY')
})
const imageHandler = (file) => {
    console.log(file);
}
</script>
