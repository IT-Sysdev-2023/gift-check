<template>
    <a-modal
        :open="open"
        title="Scan GC"
        style="width: 600px"
        @cancel="handleClose"
        centered
        @ok="onSubmitBarcode"
    >
        <a-descriptions class="mt-5">

            <a-descriptions-item
                label="Scan Mode"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                <a-switch
                    @change="() => (errorBarcode = null)"
                    v-model:checked="scanSwitch"
                    checked-children="Range Scan"
                    un-checked-children="Single Scan"
                />
            </a-descriptions-item>
        </a-descriptions>
        <a-form :model="formBc" layout="vertical">
            <!-- //Single Scan -->
            <a-form-item
                v-if="!scanSwitch"
                label="Barcode"

            >
                <a-input-number
                    :maxlength="13"
                    v-model:value="formBc.barcode"
                    placeholder="Enter Barcode"
                    class="w-full h-16 text-3xl pt-4"
                    @input="() => (errorBarcode = null)"
                />
            </a-form-item>

            <!-- //Range Scan -->
            <a-row :gutter="[16, 0]" class="mt-8" v-else>
                <a-col :span="12"
                    ><a-form-item
                        label="Barcode Start"

                    >
                        <a-input-number
                            :maxlength="13"
                            placeholder="Barcode Start"
                            v-model:value="formBc.startBarcode"
                            class="w-full h-16 text-3xl pt-4"
                            @input="() => (errorBarcode = null)"
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <a-form-item
                        label="Barcode End"

                    >
                        <a-input-number
                            :maxlength="13"
                            placeholder="Barcode End"
                            v-model:value="formBc.endBarcode"
                            class="w-full h-16 text-3xl pt-4"
                            @input="() => (errorBarcode = null)"
                        />
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
    console.log(props.scanData);
    const req_id = props.data.req_id;
    axios.post(route("treasury.transactions.promo.gc.releasing.scanBarcode"), {
            scanMode: scanSwitch.value,
            bstart: formBc.startBarcode,
            bend: formBc.endBarcode,
            barcode: formBc.barcode,
            reqid: req_id,
            denom_id: props.scanData.pgcreqi_denom
        })
        .then((res) => {
            page.barcodeReviewScan.promo = res.data.sessionData;

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
            emit("update:open", false);
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
