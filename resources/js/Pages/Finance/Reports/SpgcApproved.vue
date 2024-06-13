<template>
    <a-card>
        <div class="flex justify-between">
            <div>
                <a-range-picker v-model:value="value1" class="mb-5" />
            </div>
            <div>
                <a-switch v-model:checked="state.checked1" :disabled="isSwitch" checked-children="PDF Selected" un-checked-children="Select PDF" @change="handlePdfChange" class="mr-3"/>
                <a-switch v-model:checked="state.checked2" :disabled="isSwitch" checked-children="Excel Selected" un-checked-children="Select Excel" @change="handleExcelChange" class="mr-3"/>

                <a-button type="primary">
                    <template #icon v-if="isSwitch == true">
                        <FileExcelOutlined />
                    </template>
                    <template #icon v-else>
                        <FilePdfOutlined />
                    </template>
                    {{ isSwitch == true ? 'Generate to Pdf' : "Generate to Excel" }}
                </a-button>
            </div>
        </div>
        <a-table></a-table>
    </a-card>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            isSwitch: null,
            state: {
                checked1: null,
                checked2: null
            }
        }
    },
    methods: {
        handlePdfChange(boolean) {
           this.isSwitch = !this.isSwitch;
        },
        handleExcelChange(boolean){
           this.isSwitch = !this.isSwitch;
        }
    }
}
</script>
