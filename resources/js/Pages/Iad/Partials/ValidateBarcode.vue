<template>
    <a-modal :footer="false" :mask-closable="false" @cancel="cancel">
        <a-form class="mt-7">
            <a-descriptions size="small" class="mb-1 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Received No.">{{ recnum }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions size="small" class="mb-3 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Date">{{ date }}</a-descriptions-item>
            </a-descriptions>
            <div v-if="response.status">
                <a-alert :message="response.msg" :type="response.status" class="mt-5" show-icon />
            </div>
            <a-item>
                <a-form class="mt-3">
                    <a-form-item has-feedback :help="errors.barcode"
                        :validate-status="form.barcode?.length === 13 ? 'success' : errors.barcode ? 'error' : ''">
                        <a-input size="large" v-model:value="form.barcode" placeholder="Enter Barcode">
                            <template #prefix>
                                <PaperClipOutlined />
                            </template>
                            <template #suffix>
                                <a-tooltip title="Enter Barcode Here..">
                                    <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                                </a-tooltip>
                            </template>
                        </a-input>
                    </a-form-item>
                </a-form>
            </a-item>
        </a-form>
        <div class="flex justify-between">
            <div>
                <p class="text-end mt-4 mb-3" style="color: #3FA2F6;">
                    Validated By: <a-typography-text keyboard>{{ $page.props.auth.user.full_name
                        }}</a-typography-text>
                </p>
            </div>
            <div>
                <a-button class="mt-4 mb-3" type="primary" @click="validate" >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    Submit
                </a-button>
            </div>
        </div>
    </a-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';

export default {
    props: {
        recnum: Number,
        date: String,
        reqid: Number
    },
    data() {

        return {
            form: useForm({
                barcode: null,
                reqid: this.reqid,
                recnum: this.recnum,
            }),
            response: {},
            errors: {},
        }
    },
    methods: {
        validate() {
            this.$inertia.post(route('iad.validate.barcode'), {
                ...pickBy(this.form)
            }, {
                onSuccess: (response) => {
                    this.response = response.props.flash;
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description:
                            response.props.flash.msg,
                    });
                    if (response.props.flash.status == 'success') {
                        this.form.reset();
                    }
                    if (response.props.flash.status) {
                        this.errors = {};
                    }
                },
                onError: (errors) => {
                    this.errors = errors;
                    this.response = [];
                }
            })
        },
        cancel() {
            this.response = [];
            this.errors = {};
        }
    }
}
</script>
