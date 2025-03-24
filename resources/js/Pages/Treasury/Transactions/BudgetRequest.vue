<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 10px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title="Budget Entry Form" class="mb-1">
           
            <a-form
                :model="formState"
                :label-col="{ span: 7 }"
                :wrapper-col="{ span: 15 }"
                @finish="onSubmit"
            >
                <a-row class="mb-1"> 
                    <a-col :span="10">
                        <a-form-item label="BR No." name="name">
                            <a-input :value="br" readonly />
                        </a-form-item>
                        <a-form-item
                            class="mb-1"
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
                             class="mb-1"
                            label="Budget:"
                            name="budget"
                            :validate-status="getErrorStatus('budget')"
                            :help="getErrorMessage('budget')"
                        >
                        <span class="text-red-500 text-xs">*Required</span>
                            <a-input-number
                                style="width: 100%"
                                :formatter="currencyFormatter"
                                v-model:value="formState.budget"
                                :min="0"
                                @change="clearError('budget')"
                            />
                        </a-form-item>
                        <a-form-item
                            class="mb-1"
                            label="Budget Category:"
                            name="cat"
                            :validate-status="getErrorStatus('category')"
                            :help="getErrorMessage('category')"
                        >
                        <span class="text-red-500 text-xs">*Required</span>
                            <ant-select
                                :options="[
                                    { label: 'Regular Gc', value: 'regular' },
                                    { label: 'Special Gc', value: 'special' },
                                ]"
                                @handle-change="categoryHandler"
                            />
                        </a-form-item>
                        <a-form-item class="mb-1"label="Upload Scan Copy.:" name="name" :validate-status="getErrorStatus('file')"
                        :help="getErrorMessage('file')">
                        <span class="text-red-500 text-xs">*Required</span>
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item>
                        <a-form-item
                            class="mb-1"
                            label="Remarks"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('remarks')"
                            :help="getErrorMessage('remarks')"
                        >
                        <span class="text-red-500 text-xs">*Required</span>
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="clearError('remarks')"
                            />
                        </a-form-item>
                        <a-form-item 
                                 class="mb-1"
                                 label="Prepared By">
                            <a-input
                                :value="page.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>
                        <div>
                            <div class="flex justify-end mx-12">
                                <a-form-item class="text-end">
                                    <a-button type="primary" html-type="submit"
                                        >SUBMIT</a-button
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
import { ref } from "vue";
import dayjs from "dayjs";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { currencyFormatter, getError } from "@/Mixin/UiUtilities";
import { FlashProps, PageWithSharedProps } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";
import { BudgetRequestForm } from "@/types/treasury";
import { UploadChangeParam } from "ant-design-vue";

const props = defineProps<{
    title?: string;
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
    br: string;
}>();

const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs().format("MMM DD, YYYY");
const stream = ref<string>("");
const openIframe = ref<boolean>(false);
const { openLeftNotification } = onProgress();

const formState = useForm<BudgetRequestForm>({
    br: props.br,
    dateNeeded: null,
    budget: 0,
    file: null,
    remarks: "",
    category: "",
});

const onSubmit = () => {
    formState.post(route("treasury.transactions.budgetRequestSubmission"), {
        onSuccess: ({ props }: { props: FlashProps }) => {
            openLeftNotification(props.flash);
            if (props.flash?.success) {
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

const { getErrorMessage, getErrorStatus, clearError } =
    getError<BudgetRequestForm>(formState);

const handleChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};
// const disabledDate = (current: Dayjs) => {
//     return current && current < dayjs().startOf("day");
// };
</script>
