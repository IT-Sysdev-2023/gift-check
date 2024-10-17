<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title=" Revolving Budget Entry Form" class="mt-10">
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
                        <!-- <a-form-item
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
                        </a-form-item> -->
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
                        <a-form-item
                            label="Budget Category:"
                            name="cat"
                            :validate-status="getErrorStatus('category')"
                            :help="getErrorMessage('category')"
                        >
                            <ant-select
                                :options="[
                                    { label: 'Regular Gc', value: 'regular' },
                                    { label: 'Special Gc', value: 'special' },
                                ]"
                                @handle-change="categoryHandler"
                            />
                        </a-form-item>
                        <!-- <a-form-item label="Upload Scan Copy.:" name="name" :validate-status="getErrorStatus('file')"
                        :help="getErrorMessage('file')">
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item> -->
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
                                <a-form-item class="text-end ">
                                    <a-button type="primary" html-type="submit"
                                        >Submit</a-button
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
                                        title="Regular Gc Budget"
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
        <a-modal
            v-model:open="openIframe"
            style="width: 70%; top: 50px"
            :footer="null"
            :afterClose="closeIframe"
        >
            <iframe
                class="mt-7"
                :src="stream"
                width="100%"
                height="600px"
            ></iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import { ref } from "vue";
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
    category: string;
}

const props = defineProps<{
    title?: string;
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
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
    category: null,
});

const stream = ref(null);
const openIframe = ref(false);
const { openLeftNotification } = onProgress();
const handleChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};

const onSubmit = () => {
    formState.post(route("treasury.transactions.budgetRequestSubmission"), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                openIframe.value = true;
            }
        },
    });
};

const categoryHandler = (cat: string) => {
    formState.category = cat;
};
const closeIframe = () => {
    router.visit(route("treasury.dashboard"));
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
