<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title="Update Budget Adjustment" class="mt-10">
            <a-form
                :model="formState"
                :label-col="{ span: 7 }"
                :wrapper-col="{ span: 15 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item label="Adj. No." name="name">
                            <a-input :value="data.adjustmentNo" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Date Requested:"
                            name="dateRequested"
                        >
                            <a-input v-model:value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Adjustment Type:"
                            name="cat"
                            :validate-status="getErrorStatus('adjustmentType')"
                            :help="getErrorMessage('adjustmentType')"
                        >
                            <ant-select
                                :options="[
                                    {
                                        label: 'Negative Entry',
                                        value: 'negative',
                                    },
                                    {
                                        label: 'Positive Entry',
                                        value: 'positive',
                                    },
                                ]"
                                @handle-change="categoryHandler"
                                :value="formState.adjustmentType"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Budget Adjustment:"
                            name="budget"
                            :validate-status="getErrorStatus('budget')"
                            :help="getErrorMessage('budget')"
                        >
                            <a-input-number
                                style="width: 100%"
                                :formatter="currencyFormatter"
                                v-model:value="formState.budget"
                                :min="0"
                                @change="clearError('budget')"
                            />
                        </a-form-item>
                        <a-form-item label="Uploaded Image.:">
                            <ant-upload-image
                                :images="[{ url: data.file }]"
                                @handle-change="handleChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Remarks"
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
                        <a-form-item label="Prepared By">
                            <a-input
                                :value="page.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>
                        <div>
                            <div class="flex justify-end mx-9">
                                <a-form-item class="text-end">
                                    <a-button type="primary" html-type="submit"
                                        >Update</a-button
                                    >
                                </a-form-item>
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="14">
                        <a-card>
                            <a-row>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Regular GC Budget"
                                        :value="regularBudget"
                                    />
                                </a-col>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Special Gc Budget"
                                        :value="specialBudget"
                                    />
                                </a-col>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Total Budget"
                                        :value="remainingBudget"
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
import type { UploadChangeParam } from "ant-design-vue";
import { currencyFormatter } from "@/Mixin/UiUtilities";
import dayjs from "dayjs";
import { router, useForm, usePage } from "@inertiajs/vue3";
import type { UploadFile } from "ant-design-vue";
import { PageWithSharedProps } from "@/types/index";
import { getError, onProgress } from "@/Mixin/UiUtilities";
import { route } from 'ziggy-js';

interface FormStateGc {
    file: UploadFile | null;
    budget: number;
    remarks: string;
    adjustmentType: string | null;
}

const props = defineProps<{
    title?: string;
    data: {
        adjustmentNo: string;
        requestedAt: string;
        type: string;
        remarks: string;
        preparedBy: string;
        budget: number;
        file: string;
    };
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
}>();

const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs(props.data.requestedAt).format("MMM DD, YYYY");

const formState = useForm<FormStateGc>({
    budget: props.data.budget,
    file: null,
    remarks: props.data.remarks,
    adjustmentType: props.data.type,
});

const { openLeftNotification } = onProgress();
const handleChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};

const onSubmit = () => {
    formState.post(
        route("treasury.adjustment.budgetAdjustmentUpdateSubmission"),
        {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                if (props.flash?.success) {
                    router.visit(route("treasury.dashboard"));
                }
            },
        },
    );
};

const categoryHandler = (cat: string) => {
    formState.adjustmentType = cat;
};
const { getErrorMessage, getErrorStatus, clearError } = getError(formState);
</script>
