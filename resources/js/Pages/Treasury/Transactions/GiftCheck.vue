<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-row>
                <a-col :span="12">
                    <a-statistic
                        title="Prepaired By"
                        :value="$page.props.auth.user.full_name"
                    />
                </a-col>
                <a-col :span="12">
                    <a-statistic
                        title="Current Budget"
                        :value="remainingBudget"
                    />
                </a-col>
            </a-row>
        </a-card>
        <a-card title="Submit a Gift Check" class="mt-10">
            <a-row>
                <a-col :span="10">
                    <a-form
                        ref="formRef"
                        :model="formState"
                        :label-col="{ span: 8 }"
                        :wrapper-col="{ span: 12 }"
                    >
                        <a-form-item ref="name" label="PR No." name="name">
                            <a-input :value="formState.prNo" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested:" name="name">
                            <a-input v-model:value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Date Needed:"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('dateNeeded')"
                            :help="getErrorMessage('dateNeeded')"
                        >
                            <a-date-picker
                                v-model:value="formState.dateNeeded"
                                @change="clearError('dateNeeded')"
                            />
                        </a-form-item>
                        <a-form-item label="Upload Scan Copy.:" name="name">
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item>
                        <a-form-item
                            label="Remarks:."
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('remarks')"
                            :help="getErrorMessage('remarks')"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="clearError('remarks')"
                            />
                        </a-form-item>
                    </a-form>
                </a-col>
                <a-col :span="14">
                    <a-card>
                        <a-row :gutter="16" class="text-center">
                            <a-col :span="12">
                                <span>Denomination</span>
                            </a-col>
                            <a-col :span="12">
                                <span>Quantity</span>
                            </a-col>
                        </a-row>
                        <a-row
                            :gutter="16"
                            class="mt-5"
                            v-for="(item, index) of formState.denom"
                            :key="index"
                        >
                            <a-col :span="12">
                                <a-input
                                    :value="item.denomination"
                                    readonly
                                    class="text-end"
                                />
                            </a-col>
                            <a-col :span="12" style="text-align: center">
                                <a-input-number
                                    id="inputNumber"
                                    v-model:value="item.qty"
                                    placeholder="0"
                                    :min="0"
                                    has-feedback
                                    :validate-status="getErrorStatus('denom')"
                                    :help="getErrorMessage('denom')"
                                >
                                    <template #upIcon>
                                        <ArrowUpOutlined />
                                    </template>
                                    <template #downIcon>
                                        <ArrowDownOutlined />
                                    </template>
                                </a-input-number>
                            </a-col>
                        </a-row>
                        <div v-if="formState.errors.denom" style="color: red">
                            {{ formState.errors.denom }}
                        </div>
                    </a-card>

                    <a-form-item class="text-end mt-5">
                        <a-button type="primary" @click="onSubmit"
                            >Submit</a-button
                        >
                    </a-form-item>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import dayjs from "dayjs";
import { useForm } from "@inertiajs/vue3";
import { FormStateGc } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";

const props = defineProps<{
    title?: string;
    prNo: string;
    denomination: {
        data: any[];
    };
    remainingBudget: string;
}>();

const currentDate = dayjs().format("MMM DD, YYYY");
const formRef = ref();

const formState = useForm<FormStateGc>({
    denom: [...props.denomination.data],
    prNo: props.prNo,
    file: null,
    remarks: "",
    dateNeeded: null,
});

const { openLeftNotification } = onProgress();
const handleChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};
const onSubmit = () => {
    formState
        .transform((data) => ({
            ...data,
            dateNeeded: dayjs(data.dateNeeded).format("YYYY-MM-DD"),
        }))
        .post(route("treasury.transactions.production.gcSubmit"), {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
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
</script>
