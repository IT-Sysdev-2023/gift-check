<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="12">
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
                Check By
            </strong>
            <a-form-item has-feedback :help="errors.br_checkby" :validate-status="errors.br_checkby ? 'error' : ''">
                <a-select allow-clear ref="select" v-model:value="form.br_checkby" style="width: 100%"
                    placeholder="Assign" @change="() => errors.br_checkby = null">
                    <a-select-option v-for="item in assign" v-model:value="item.assig_name">{{ item.assig_name
                        }}</a-select-option>
                </a-select>
            </a-form-item>
            <strong class="ml-1">
                Approved By
            </strong>
            <a-form-item has-feedback :help="errors.br_appby" :validate-status="errors.br_appby ? 'error' : ''">
                <a-select allow-clear ref="select" v-model:value="form.br_appby" style="width: 100%"
                    placeholder="Assign" @change="() => errors.br_appby = null">
                    <a-select-option v-for="item in assign" v-model:value="item.assig_name">{{ item.assig_name
                        }}</a-select-option>
                </a-select>
            </a-form-item>
            <strong class="ml-1">
                Remarks
            </strong>
            <a-form-item has-feedback :help="errors.br_remarks" :validate-status="errors.br_remarks ? 'error' : ''">
                <a-textarea placeholder="Remarks" @change="() => errors.br_remarks = null" :row="3"
                    v-model:value="form.br_remarks" allow-clear show-count />
            </a-form-item>

        </a-col>
        <a-col :span="12">
            <strong class="ml-1">
                Date Approved/Cancel
            </strong>
            <a-form-item>
                <a-input class="text-center" style="font-weight: 600; letter-spacing: -.1px;" readonly
                    :value="dayjs().format('MMMM DD YY')"></a-input>
            </a-form-item>
            <strong class="ml-1">
                Prepare By
            </strong>
            <a-form-item>
                <a-input class="text-center" style="font-weight: 600; letter-spacing: -.1px;" readonly
                    :value="$page.props.auth.user.full_name"></a-input>
            </a-form-item>
            <div class="flex justify-center">
                <ant-upload-image @handle-change="image" />
            </div>
            <a-button type="primary" class="mt-4" block @click="submit">
                <template #icon>
                    <FastForwardOutlined />
                </template>
                Submit Budget Request
            </a-button>
        </a-col>
    </a-row>
</template>
<script setup>
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

const props = defineProps({
    record: Object,
    assign: Object,
});

const errors = ref({});

const form = useForm({
    br_id: props.record.br_id,
    br_select: null,
    br_checkby: null,
    br_appby: null,
    br_remarks: null,
    br_budtype: props.record.br_type,
    br_group: props.record.br_group,
    br_preappby: props.record.br_preapprovedby,
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

            if(res.props.flash.status == 'success'){
                router.visit(route('finance.dashboard'))
            }
        },
        onError: (err) => {
            errors.value = err;
        }
    })
}

const image = (file) => {
    form.file = file.file;
}

</script>
