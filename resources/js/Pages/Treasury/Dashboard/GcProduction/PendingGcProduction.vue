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
                        title="Prepared By"
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
            <a-form
                ref="formRef"
                :model="formState"
                :label-col="{ span: 8 }"
                :wrapper-col="{ span: 12 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item ref="name" label="PR No." name="name">
                            <a-input :value="record.data.pe_num" readonly />
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
                        <!-- <a-form-item
                            label="Upload Scan Copy.:"
                            name="name"
                            :validate-status="getErrorStatus('file')"
                            :help="getErrorMessage('file')"
                        >
                            <ant-upload-image @handle-change="handleChange" />
                        </a-form-item> -->
                        <a-form-item
                            label="Uploaded image preview:"
                            v-if="record.data.pe_file_docno"
                        >
                            <ant-image-preview :images="imagePreview" />
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
                                        :value="item.denomination_format"
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
                            <div
                                class="mt-5 text-red-500 text-center"
                                v-if="formState.errors.denom"
                            >
                                {{ formState.errors.denom }}
                            </div>
                        </a-card>

                        <a-form-item class="text-end mt-5">
                            <a-button type="primary" html-type="submit"
                                >Submit</a-button
                            >
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { router, useForm } from "@inertiajs/vue3";
import { getError, onProgress } from "@/Mixin/UiUtilities";
import { ProductionRequest } from "@/types/treasury";

const props = defineProps<{
    title?: string;
    record: { data: ProductionRequest };
    denomination: {
        data: {
            id: number;
            denomination: number;
            denomination_format: string;
            qty: number;
        }[];
    };
    remainingBudget: string;
}>();
const currentDate = dayjs().format("MMM DD, YYYY");
const formRef = ref();

const formState = useForm({
    reqid: props.record.data.pe_id,
    denom: [...props.denomination.data],
    file: null,
    remarks: props.record.data.pe_remarks,
    dateNeeded: props.record.data.pe_date_needed,
});

const imagePreview = [
    {
        uid: props.record.data.pe_id,
        name: props.record.data.pe_file_docno,
        url:
            "/storage/productionRequestFile/" + props.record.data.pe_file_docno,
    },
];
const { openLeftNotification } = onProgress();
// const handleChange = (file: UploadChangeParam) => {
//     formState.file = file.file;
// };

const onSubmit = () => {
    formState.post(route("treasury.production.request.pendingSubmission"), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash?.success) {
                router.visit(route("treasury.dashboard"));
            }
        },
    });
};

const { getErrorMessage, getErrorStatus, clearError } = getError(formState);
</script>
