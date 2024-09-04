<template>
    <a-modal
        :open="open"
        title="Scan Gc"
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
                :validate-status="formBc.errors.barcode ? 'error' : ''"
                :help="formBc.errors.barcode"
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
                        :validate-status="
                            formBc.errors.startBarcode ? 'error' : ''
                        "
                        :help="formBc.errors.startBarcode"
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
                        :validate-status="
                            formBc.errors.endBarcode ? 'error' : ''
                        "
                        :help="formBc.errors.endBarcode"
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
import { usePage, useForm } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types/index";
import { notification } from "ant-design-vue";
import { onProgress } from "@/Mixin/UiUtilities";

const props = defineProps<{
    open: boolean;
}>();
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();
const page = usePage<PageWithSharedProps>().props;
const formBc = useForm({
    barcode: null,
    startBarcode: null,
    endBarcode: null,
});

const scanSwitch = ref(false);
const errorBarcode = ref(null);
const { openLeftNotification } = onProgress();
const onSubmitBarcode = async () => {
    formBc
        .transform((data) => ({
            ...data,
            switch: scanSwitch.value,
        }))
        .get(route("treasury.transactions.institution.gc.sales.scan"), {
            preserveState: true,
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
            },
        });
};
const handleClose = () => {
    emit("update:open", false);
};
</script>
