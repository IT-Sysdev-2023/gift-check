<template>
    <a-divider>
        <p style="font-size: 14px; font-weight: bold;"> Budget Form Request</p>
    </a-divider>
    <a-row :gutter="[16, 16]">
        <a-col :span="9">
            <strong class="ml-1">
                Request Status
            </strong>
            <a-form-item has-feedback :help="errors.br_select" :validate-status="errors.br_select ? 'error' : ''">
                <a-select allow-clear ref="select" v-model:value="form.br_select" style="width: 100%"
                    placeholder="Select Status" @change="() => errors.br_select = null">
                    <a-select-option value="1">Approved</a-select-option>
                    <a-select-option value="2">Cancel</a-select-option>
                </a-select>
            </a-form-item>

            <strong class="ml-1">
                Remarks
            </strong>
            <a-form-item has-feedback :help="errors.br_remarks" :validate-status="errors.br_remarks ? 'error' : ''">
                <a-textarea placeholder="Remarks" @change="() => errors.br_remarks = null" :row="4"
                    v-model:value="form.br_remarks" allow-clear show-count />
            </a-form-item>
            <div class="flex justify-center mt-5">
                <ant-upload-image @handle-change="image" />
            </div>

        </a-col>
        <a-col :span="15">
            <a-descriptions size="small" layout="horizontal" class="mb-1" bordered>
                <a-descriptions-item label="Approved/Cancel " style="width: 50%;">{{ dayjs().format('MMMM DD, YY')
                    }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions size="small" layout="horizontal" class="mb-1" bordered>
                <a-descriptions-item label="Reviewed By " style="width: 50%;">{{ $page.props.auth.user.full_name
                    }}</a-descriptions-item>
            </a-descriptions>
            <a-button type="primary" class="mt-4" block @click="submit">
                <template #icon>
                    <FastForwardOutlined />
                </template>
                Submit Budget Request
            </a-button>
        </a-col>
    </a-row>
    <!-- {{ $page.props.auth.user.user_id }} -->
    <a-modal v-model:open="open" title="Basic Modal" style="width: 70%;" :after-close="close">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 500px;"></iframe>
    </a-modal>

</template>
<script setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

const props = defineProps({
    record: Object,
    assign: Object,
});


const page = usePage();


const errors = ref({});
const open = ref(false);
const stream = ref('');

const form = useForm({
    br_category: props.record.br_category,
    br_id: props.record.br_id,
    br_select: null,
    br_checkby: props.record.br_requested_by,
    br_appby: props.record.br_checked_by,
    br_remarks: null,
    br_budtype: props.record.br_type,
    br_group: props.record.br_group,
    br_preappby: page.props.auth.user.user_id,
    br_req: props.record.br_request,
    file: null,
});

const submit = () => {
    form.transform((data) => ({
        ...data
    })).post(route('finance.budget.submit'), {
        onSuccess: (res) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });

            if (res.props.flash.status == 'success') {
                stream.value = `data:application/pdf;base64,${res.props.flash.stream}`
                open.value = true;

            }
        },
        onError: (err) => {
            errors.value = err;
        }
    })
}

const close = () => {
    router.visit(route('finance.dashboard'));
}

const image = (file) => {
    form.file = file.file;
}

</script>
