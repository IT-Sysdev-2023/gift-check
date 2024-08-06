<template>
    <a-modal title="Validate By Range" style="width: 45%;" :footer="false" :mask-closable="false">
        <div class="mt-5 mb-4">
            <a-descriptions size="small" class="mb-1 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Received No.">{{ recnum }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions size="small" class="mb-3 text-center" layout="horizontal" bordered>
                <a-descriptions-item label="Date">{{ date }}</a-descriptions-item>
            </a-descriptions>
            <a-row :gutter="[16, 16]" class="mt-7 mb-7">
                <a-col :span="12">
                    <a-input-group compact>
                        <a-typography-text keyboard>Barcode Start:</a-typography-text>
                        <a-input size="large" show-count v-model:value="form.barcodeStart"
                            style="width: calc(100% - 39px);" />
                        <a-tooltip title="Barcode Start Here.">

                            <a-button size="large" disabled>
                                <template #icon>
                                    <LogoutOutlined />
                                </template>
                            </a-button>
                        </a-tooltip>
                    </a-input-group>
                </a-col>
                <a-col :span="12">
                    <a-input-group compact>
                        <a-typography-text keyboard>Barcode End:</a-typography-text>
                        <a-input size="large" show-count v-model:value="form.barcodeEnd"
                            style="width: calc(100% - 39px)" />
                        <a-tooltip title="Barcode Ends Here.">
                            <a-button size="large" disabled>
                                <template #icon>
                                    <LoginOutlined />
                                </template>
                            </a-button>
                        </a-tooltip>
                    </a-input-group>
                </a-col>
            </a-row>

            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <div class="mt-2 flex justify-between">
                        <a-button block type="primary" ghost @click="validateRange">
                            <template #icon>
                                <SaveOutlined />
                            </template>
                            Submit
                        </a-button>
                        <a-button block danger>
                            <template #icon>
                                <ClearOutlined />
                            </template>
                            Clear
                        </a-button>
                    </div>
                </a-col>
                <a-col :span="12">
                    <p class="text-end mt-4" style="color: #3FA2F6;">
                        Validated By: <a-typography-text keyboard>{{ $page.props.auth.user.full_name
                            }}</a-typography-text>
                    </p>
                </a-col>
            </a-row>
        </div>
    </a-modal>
</template>
<script>
import pickBy from "lodash/pickBy";

export default {
    props: {
        recnum: Number,
        date: String,
    },
    data() {
        return {
            form: {
                barcodeStart: null,
                barcodeEnd: null,
                recnum: this.recnum
            },
        }
    },
    methods: {
        validateRange() {
            this.$inertia.post(route('iad.validate.range'), {
                ...pickBy(this.form)
            });
        }
    }
}
</script>
