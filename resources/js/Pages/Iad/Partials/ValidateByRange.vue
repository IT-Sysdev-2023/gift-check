<template>
    <a-modal title="Validate By Range" style="width: 50%;" :footer="false" :mask-closable="false" @cancel="cancel">
        <div class="mt-5 mb-4">
            <a-descriptions size="small" class="mb-1 text-center" layout="horizontal" bordered>
                <a-descriptions-item style="width: 50%" label="Received No.">{{ recnum }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions size="small" class="mb-3 text-center" layout="horizontal" bordered>
                <a-descriptions-item style="width: 50%" label="Date">{{ date }}</a-descriptions-item>
            </a-descriptions>

            <div v-if="response.status">
                <a-alert :message="response.msg" :type="response.status" class="mt-5" show-icon />
            </div>

            <a-row :gutter="[16, 16]" class="mt-7 mb-6">
                <a-col :span="12">
                    <a-input-group>
                        <a-typography-text keyboard>Barcode Start:</a-typography-text>
                        <a-form-item has-feedback :help="errors.barcodeStart"
                            :validate-status="form.barcodeStart?.length === 13 ? 'success' : errors.barcodeStart ? 'error' : ''">
                            <a-input-number @change="() => errors.barcodeStart = ''" class="p-1 pt-2 pb-2 text-3xl" style="width: 100%" v-model:value="form.barcodeStart" size="large"  @keyup.enter="validateRange"
                                placeholder="Start of Barcode here.." show-count allow-clear @keypress="handleKeyPress">
                                <template #prefix>
                                    <PaperClipOutlined />
                                </template>
                                <template #suffix>
                                    <a-tooltip title="Barcode Start Here.">
                                        <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                                    </a-tooltip>
                                </template>
                            </a-input-number>
                        </a-form-item>

                    </a-input-group>
                </a-col>
                <a-col :span="12">
                    <a-input-group>
                        <a-typography-text keyboard>Barcode End:</a-typography-text>
                        <a-form-item :help="errors.barcodeEnd" has-feedback
                            :validate-status="form.barcodeEnd?.length === 13 ? 'success' : errors.barcodeEnd ? 'error' : ''">
                            <a-input-number class="p-1 pt-2 pb-2 text-3xl" style="width: 100%" v-model:value="form.barcodeEnd" @keyup.enter="validateRange" size="large"
                                placeholder="End of Barcode here.." show-count allow-clear
                                @change="() => errors.barcodeEnd = ''"
                                :disabled="form.barcodeStart === null || form.barcodeStart.length <= 0"
                                @keypress="handleKeyPress">
                                <template #prefix>
                                    <PaperClipOutlined />
                                </template>
                                <template #suffix>
                                    <a-tooltip title="BarcodeEnd Here.">
                                        <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                                    </a-tooltip>
                                </template>
                            </a-input-number>
                        </a-form-item>
                    </a-input-group>
                </a-col>
            </a-row>

            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <p class="text-start mt-4" style="color: #3FA2F6;">
                        Validated By: <a-typography-text keyboard>{{ $page.props.auth.user.full_name
                            }}</a-typography-text>
                    </p>
                </a-col>
                <a-col :span="12">
                    <div class="mt-2 flex justify-between">
                        <a-button size="large" block type="primary" @click="validateRange" :loading="form.processing"
                            :disabled="(form.barcodeStart === null || form.barcodeEnd === null) || (form.barcodeEnd == '' || form.barcodeStart == '')">
                            <template #icon>
                                <SaveOutlined />
                            </template>
                            Submit
                        </a-button>
                    </div>

                </a-col>
            </a-row>
        </div>
    </a-modal>
</template>
<script>
import { useForm } from "@inertiajs/vue3";
import { notification } from 'ant-design-vue';
import pickBy from "lodash/pickBy";

export default {
    props: {
        recnum: Number,
        date: String,
        reqid: Number
    },
    data() {
        return {
            errors: {},
            response: {},
            form: useForm({
                barcodeStart: null,
                barcodeEnd: null,
                reqid: this.reqid,
                recnum: this.recnum,
            }),
        }
    },
    methods: {

        validateRange() {
            this.$inertia.post(route('iad.validate.range'), {
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
                },
                onError: (errors) => {
                    this.errors = errors;
                }
            });
        },
        cancel(){
            this.response = [];
            this.errors = [];
            this.form.reset();
        },
        handleKeyPress(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        }
    }
}
</script>
