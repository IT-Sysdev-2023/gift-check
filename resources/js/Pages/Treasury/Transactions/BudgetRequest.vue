<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title="Submit a Gift Check" class="mt-10">
            <a-form
                :model="formState"
                :label-col="{ span: 7 }"
                :wrapper-col="{ span: 15 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item label="BR No." name="name">
                            <a-input :value="br" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Date Requested:"
                            name="dateRequested"
                        >
                            <a-input v-model:value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Date Needed:"
                            name="dateNeeded"
                            has-feedback
                            :validate-status="getErrorStatus('dateNeeded')"
                            :help="getErrorMessage('dateNeeded')"
                        >
                            <a-date-picker
                                :disabled-date="disabledDate"
                                v-model:value="formState.dateNeeded"
                                @change="clearError('dateNeeded')"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Budget:"
                            name="budget"
                            :validate-status="getErrorStatus('budget')"
                            :help="getErrorMessage('budget')"
                        >
                            <a-input-number
                                style="width: 100%"
                                :formatter="
                                    (value) =>
                                        `₱ ${value}`.replace(
                                            /\B(?=(\d{3})+(?!\d))/g,
                                            ','
                                        )
                                "
                                v-model:value="formState.budget"
                                :min="0"
                                @change="clearError('budget')"
                            />
                        </a-form-item>
                        <!-- <a-form-item label="Upload Scan Copy.:" name="name" :validate-status="getErrorStatus('file')"
                        :help="getErrorMessage('file')">
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item> -->
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

                        <a-form-item class="text-end mt-5">
                            <a-button type="primary" html-type="submit"
                                >Submit</a-button
                            >
                        </a-form-item>
                    </a-col>
                    <a-col :span="14">
                        <a-card>
                            <a-row>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Current Budget"
                                        :value="remainingBudget"
                                    />
                                </a-col>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Prepaired By"
                                        :value="page.auth.user.full_name"
                                    />
                                </a-col>
                            </a-row>
                        </a-card>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import dayjs, { Dayjs } from "dayjs";
import { router, useForm, usePage } from "@inertiajs/vue3";
import type { UploadFile } from "ant-design-vue";
import { PageWithSharedProps } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";
interface FormStateGc {
    file: UploadFile | null;
    br: string;
    budget: number;
    remarks: string;
    dateNeeded: null;
}

const props = defineProps<{
    title?: string;
    remainingBudget: string;
    br: string;
}>();

const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs().format("MMM DD, YYYY");
const disabledDate = (current: Dayjs) => {
    // Can not select days before today and today
    return current && current < dayjs().startOf("day");
};
const formState = useForm<FormStateGc>({
    br: props.br,
    dateNeeded: null,
    budget: 0,
    file: null,
    remarks: "",
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
        .post(route("treasury.transactions.budgetRequestSubmission"), {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                if (props.flash.success) {
                    router.visit(route("treasury.dashboard"));
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
</script>
