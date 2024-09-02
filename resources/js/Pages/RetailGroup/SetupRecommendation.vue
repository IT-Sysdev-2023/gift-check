<template>
    <AuthenticatedLayout>
        <div class="mb-3 flex justify-end">
            <a-button @click="() => $inertia.get(route('retailgroup.pending'))">
                <RollbackOutlined />
                Back to the Setup
            </a-button>
        </div>
        <a-card>
            <div v-if="record.pgcreq_group_status != ''" class="mb-2">
                <a-alert message="Waiting For Approval"
                    description=" * Promo GC already recommended and waiting for Finance Department approval."
                    type="info" show-icon />
            </div>
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
                        <div v-if="record.pgcreq_group_status == ''">
                            <a-row :gutter="[16, 16]">
                                <a-col :span="12">
                                    <p class="ml-2 font-bold">Request Status</p>
                                    <a-form-item has-feedback :validate-status="error?.status ? 'error' : ''"
                                        :help="error.status">
                                        <a-select ref="select" v-model:value="form.status" style="width: 100%"
                                            @focus="focus" @change="() => error.status = null">
                                            <a-select-option value="1">Approved</a-select-option>
                                            <a-select-option value="2">Cancel</a-select-option>
                                        </a-select>
                                    </a-form-item>
                                    <p class="ml-2 mt-2 font-bold">Remarks</p>
                                    <a-form-item has-feedback :validate-status="error?.remarks ? 'error' : ''"
                                        :help="error.remarks">
                                        <a-textarea :rows="2" @change="() => error.remarks = null"
                                            v-model:value="form.remarks" placeholder="Enter Remarks" />
                                    </a-form-item>
                                </a-col>
                                <a-col :span="12">
                                    <p class="ml-2 font-bold">Date Approved/Cancel</p>
                                    <a-input v-model:value="form.today"
                                        style="color: #3795BD; letter-spacing: -.1px; font-weight: 600;" size="large"
                                        class="text-center" readonly />
                                    <p class="ml-2 mt-2 font-bold">Approved By</p>

                                    <a-input size="large"
                                        style="color: #3795BD; letter-spacing: -.1px; font-weight: 600;"
                                        :value="$page.props.auth.user.full_name" readonly class="text-center" />

                                </a-col>
                                <a-divider>
                                    <a-typography-text code>upload image</a-typography-text>
                                </a-divider>
                                <div class="flex justify-center" style="width: 100%;">
                                    <ant-upload-image class="ml-2" @handleChange="imageHandler" />
                                </div>
                            </a-row>
                        </div>

                        <div v-else>
                            <a-descriptions layout="horizontal" size="small" bordered>
                                <a-descriptions-item style="width: 50%;"
                                    label="Promo GC Status">Approved</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" bordered>
                                <a-descriptions-item style="width: 50%;" label="Date Approved">{{
                                    dayjs(approved?.reqap_date).format('MMM D YYYY') }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" bordered>
                                <a-descriptions-item style="width: 50%;" label="Time Approved">{{ approved?.time
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" bordered>
                                <a-descriptions-item style="width: 50%;" label="Approved By">{{ approved?.fullname
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" bordered>
                                <a-descriptions-item style="width: 50%;" label="Docs">
                                    <span v-if="approved.reqap_doc != ''">
                                        <a-image :src="'/storage/approveddocs/' + approved.reqap_doc">
                                        </a-image>
                                    </span>
                                    <span v-else>
                                        <a-empty>
                                            <template #description>
                                                <a-tag color="default">
                                                    <template #icon>
                                                        <PictureOutlined />
                                                    </template>
                                                    no uploaded image
                                                </a-tag>
                                            </template>
                                        </a-empty>
                                    </span>
                                </a-descriptions-item>
                            </a-descriptions>
                        </div>
                    </a-card>
                </a-col>
                <a-col :span="14">

                    <pending-gc-description :record="record" />

                    <div class="flex justify-end" v-if="record.pgcreq_group_status == ''">
                        <a-button type="primary" class="mt-4" @click="submit" :loading="form.processing">
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
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

const props = defineProps({
    record: Object,
    denom: Object,
    total: Number,
    approved: Object,
})
const form = useForm({
    today: dayjs().format('MMMM D YYYY'),
    status: null,
    remarks: null,
    id: props.record.pgcreq_id,
    total: props.record.pgcreq_total,
    file: [],
})
const error = ref({});

const imageHandler = (file) => {
    form.file = file.file;
}
const submit = () => {
    form.transform((data) => ({
        ...data
    })).post(route('retailgroup.recommendation.submit'), {
        onError: (e) => {
            error.value = e;
            notification['error']({
                message: 'Error',
                description:
                    'Something missing from the field',
            });
        },
        onSuccess: (res) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
        }
    })
}

</script>
