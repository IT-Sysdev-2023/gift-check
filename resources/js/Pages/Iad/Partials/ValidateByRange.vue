<template>
    <a-modal>
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-input-group compact>
                    <a-input show-count v-model:value="form.barcodeStart" style="width: calc(100% - 31px)" />
                    <a-tooltip title="copy git url">
                        <a-button>
                            <template #icon>
                                <CopyOutlined />
                            </template>
                        </a-button>
                    </a-tooltip>
                </a-input-group>
            </a-col>
            <a-col :span="12">
                <a-input-group compact>
                    <a-input show-count v-model:value="form.barcodeEnd" style="width: calc(100% - 31px)" />
                    <a-tooltip title="copy git url">
                        <a-button>
                            <template #icon>
                                <CopyOutlined />
                            </template>
                        </a-button>
                    </a-tooltip>
                </a-input-group>
            </a-col>
        </a-row>
    </a-modal>
</template>
<script>
export default {
    data() {
        return {
            form: {
                barcodeStart: null,
                barcodeEnd: null,
            }
        }
    }
}
</script>
