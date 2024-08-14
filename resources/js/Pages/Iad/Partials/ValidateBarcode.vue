<template>
    <a-modal :footer="false" :mask-closable="false">
        <a-form class="mt-7">
            <a-descriptions size="small" class="mb-1 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Received No.">{{ recnum }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions size="small" class="mb-3 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Date">{{ date }}</a-descriptions-item>
            </a-descriptions>
            <a-item>
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
                <a-button class="mt-4 mb-3" type="primary">
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

export default {
    props: {
        recnum: Number,
        date: String,
        reqid: Number
    },
    data() {
        return {
            form: useForm({
                barcode: null
            })
        }
    }
}
</script>
