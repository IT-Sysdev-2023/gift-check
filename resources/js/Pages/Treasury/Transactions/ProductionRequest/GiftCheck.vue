<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title="Create a Production Request" class="mt-10">
            <a-form ref="formRef" :model="formState" :label-col="{ span: 8 }" :wrapper-col="{ span: 12 }"
                @finish="onSubmit">
                <a-row :gutter="[16, 16]">
                    <a-col :span="10">
                        <a-card>
                            <a-form-item ref="name" label="PR No." name="name">
                                <a-input :value="formState.prNo" readonly />
                            </a-form-item>
                            <a-form-item label="Date Requested:" name="name">
                                <a-input v-model:value="currentDate" readonly />
                            </a-form-item>
                            <!-- <a-form-item
                            label="Date Needed:"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('dateNeeded')"
                            :help="getErrorMessage('dateNeeded')"
                        >
                            <a-date-picker
                                :disabled-date="disabledDate"
                                v-model:value="formState.dateNeeded"
                                @change="clearError('dateNeeded')"
                            />
                        </a-form-item> -->
                            <!-- <a-form-item label="Upload Scan Copy.:" name="name" :validate-status="getErrorStatus('file')"
                            :help="getErrorMessage('file')">
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item> -->
                            <a-form-item label="Remarks:" name="name" has-feedback
                                :validate-status="getErrorStatus('remarks')" :help="getErrorMessage('remarks')">
                                <a-textarea v-model:value="formState.remarks" @input="clearError('remarks')" />
                            </a-form-item>
                            <a-form-item label="Prepared By">
                                <a-input :value="page.auth.user.full_name" readonly />
                            </a-form-item>
                            <div>
                                <div>
                                    <a-button block type="primary" html-type="submit">
                                        <FastForwardOutlined />Submit
                                    </a-button>
                                </div>
                            </div>
                        </a-card>
                    </a-col>
                    <a-col :span="14">
                        <a-card>

                            <a-row :gutter="16" class="text-center">
                                <a-col :span="8">
                                    <span>Denomination</span>
                                </a-col>
                                <a-col :span="8">
                                    <span>Quantity</span>
                                </a-col>
                                <a-col :span="8">
                                    <span>Pc's left</span>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16" class="mt-5" v-for="(item, index) of formState.denom" :key="index">
                                <a-col :span="8">
                                    <a-input :value="item.denomination_format" readonly class="text-end" />
                                </a-col>
                                <a-col :span="8" style="text-align: center">
                                    <a-input-number id="inputNumber" v-model:value="item.qty" placeholder="0" :min="0"
                                        @change="
                                            quantityChange(
                                                item.qty,
                                                item.denomination,
                                                item
                                            )
                                            ">
                                        <template #upIcon>
                                            <ArrowUpOutlined />
                                        </template>
                                        <template #downIcon>
                                            <ArrowDownOutlined />
                                        </template>
                                    </a-input-number>
                                </a-col>
                                <a-col :span="8">

                                    <div style="text-align: center">
                                        <a-input-number :value="Math.floor(
                                            bud / item.denomination
                                        )
                                            " readonly />
                                    </div>
                                </a-col>
                            </a-row>
                            <div class="mt-5 text-red-500 text-center" v-if="formState.errors.denom">
                                {{ formState.errors.denom }}
                            </div>
                            <div>
                                <div>
                                    <p style="font-size: 20px; text-align:left; margin-top: 15px;padding-left: 2%; ">
                                        Total Amount: ₱ {{
                                            totalAmount.toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            })
                                        }}
                                    </p>
                                </div>
                            </div>
                        </a-card>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
        <a-modal v-model:open="openIframe" style="width: 70%; top: 50px" :footer="null" :afterClose="closeIframe">
            <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { computed, ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { FormStateGc } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";
import { PageWithSharedProps } from "@/types";

const page = usePage<PageWithSharedProps>().props;

const props = defineProps<{
    title?: string;
    prNo: string;
    denomination: {
        data: any[];
    };
    remainingBudget: number;
}>();

const stream = ref(null);
const bud = ref(props.remainingBudget);
const openIframe = ref(false);
const currentDate = dayjs().format("MMM DD, YYYY");
const formRef = ref();

const formState = useForm<FormStateGc>({
    denom: [...props.denomination.data],
    prNo: props.prNo,
    file: null,
    remarks: "",
    dateNeeded: null,
    total: null,
});



const { openLeftNotification } = onProgress();
// const handleChange = (file: UploadChangeParam) => {
//     formState.file = file.file;
// };

const closeIframe = () => {
    router.visit(route("treasury.dashboard"));
};
const previousQuantities = ref({}); // Store previous quantity for each item

const quantityChange = (qty, denom, item) => {
    const prevQty = previousQuantities.value[item.id] || 0; // Get previous qty for this item
    const numPrev = prevQty * denom; // Previous cost
    const numNew = qty * denom; // New cost

    // Adjust the budget
    bud.value = bud.value + numPrev - numNew;

    // Update the previous quantity for this item
    previousQuantities.value[item.id] = qty;
};

const totalAmount = computed(() => {
    return formState.denom.reduce((sum, item) => sum + (item.qty || 0) * item.denomination, 0);
});

watch(totalAmount, (newTotal) => {
    formState.total = newTotal;
});

const updateTotalAmount = () => {
    totalAmount.value;
};

const onSubmit = () => {
    formState.post(route("treasury.transactions.production.gcSubmit"), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                openIframe.value = true;
            }
        },
    });
};
const getErrorStatus = (field: string) => {
    return formState.errors[field] ? "error" : "";
};
const getErrorMessage = (field: string) => {
    return formState.errors[field];
};
const clearError = (field: string) => {
    formState.errors[field] = null;
};

// const disabledDate = (current) => {
//     return current && current < new Date().setHours(0, 0, 0, 0);
// };
</script>
