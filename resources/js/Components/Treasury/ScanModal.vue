<template>
    <a-modal :open="open" title="Scan GC" style="width: 600px" @cancel="handleClose" centered @ok="onSubmitBarcode">
        <a-descriptions class="mt-5">
            <a-descriptions-item label="Release No" :span="2" :labelStyle="{ fontWeight: 'bold' }">{{ data.rel_num
                }}</a-descriptions-item>
            <a-descriptions-item label="Date" :labelStyle="{ fontWeight: 'bold' }">{{ dayjs().format("MMM DD, YYYY")
                }}</a-descriptions-item>
            <a-descriptions-item label="Store" :span="2" :labelStyle="{ fontWeight: 'bold' }">{{
                data.details.store.store_name }}</a-descriptions-item>
            <a-descriptions-item label="Denomination" :labelStyle="{ fontWeight: 'bold' }">
                {{ scanData.denomination }}
            </a-descriptions-item>
            <a-descriptions-item label="Validated By" :span="2" :labelStyle="{ fontWeight: 'bold' }">
                {{ page.auth.user.full_name }}
            </a-descriptions-item>
            <a-descriptions-item label="Scan Mode" :labelStyle="{ fontWeight: 'bold' }">
                <a-switch @change="() => (errorBarcode = null)" v-model:checked="scanSwitch"
                    checked-children="Range Scan" un-checked-children="Single Scan" />
            </a-descriptions-item>
        </a-descriptions>
        <a-form :model="formBc" layout="vertical">
            <!-- //Single Scan -->
            <a-form-item v-if="!scanSwitch" label="Barcode" :validate-status="errorBarcode?.barcode ? 'error' : ''"
                :help="errorBarcode?.barcode">
                <a-input-number :maxlength="13" @keyup.enter="onSubmitBarcode" v-model:value="formBc.barcode"
                    placeholder="Enter Barcode" class="w-full h-16 text-3xl pt-4"
                    @input="() => (errorBarcode = null)" />
            </a-form-item>

            <!-- //Range Scan -->
            <a-row :gutter="[16, 0]" class="mt-8" v-else>
                <a-col :span="12"><a-form-item label="Barcode Start"
                        :validate-status="errorBarcode?.bstart ? 'error' : ''" :help="errorBarcode?.bstart">
                        <a-input-number :maxlength="13" placeholder="Barcode Start" v-model:value="formBc.startBarcode"
                            class="w-full h-16 text-3xl pt-4" @input="() => (errorBarcode = null)" />
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <a-form-item label="Barcode End" :validate-status="errorBarcode?.bend ? 'error' : ''"
                        :help="errorBarcode?.bend">
                        <a-input-number @keyup.enter="onSubmitBarcode" :maxlength="13" placeholder="Barcode End"
                            v-model:value="formBc.endBarcode" class="w-full h-16 text-3xl pt-4"
                            @input="() => (errorBarcode = null)" />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
    </a-modal>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, watch, reactive } from "vue";
import { usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/../../resources/js/types";
import { notification } from "ant-design-vue";
import dayjs from 'dayjs'

const props = defineProps<{
    open: boolean;
    data: any;
    scanData: any;
}>();
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();
const page = usePage<PageWithSharedProps>().props;
const formBc = reactive({
    barcode: null,
    startBarcode: null,
    endBarcode: null,
});

const scanSwitch = ref(false);
const errorBarcode = ref(null);
const onSubmitBarcode = async () => {
    axios
        .post(route("treasury.store.gc.scanBarcode"), {
            scanMode: scanSwitch.value,
            bstart: formBc.startBarcode,
            bend: formBc.endBarcode,
            barcode: formBc.barcode,
            relno: props.data.rel_num,
            denid: props.scanData.sri_items_denomination,
            store_id: props.data.details.store.store_id,
            reqid: props.data.details.sgc_id,
        })
        .then((res) => {
            page.barcodeReviewScan.allocation = res.data.sessionData;

            for (let bc of res.data.barcodes) {
                if (bc.status === 200) {
                    notification.success({
                        message: "Scan Success",
                        description: bc.message,
                    });
                } else {
                    notification.error({
                        message: "Scan Failed",
                        description: bc.message,
                    });
                }
            }
            formBc.startBarcode = null;
            formBc.endBarcode = null;
            formBc.barcode = null;
            // emit("update:open", false);
        })
        .catch((err) => {
            if (err.response.status === 400) {
                notification.error({
                    message: "Scan Failed",
                    description: err.response.data,
                });
            } else {
                errorBarcode.value = err.response.data.errors;
            }
        });
};
const handleClose = () => {
    emit("update:open", false);
};
</script>
