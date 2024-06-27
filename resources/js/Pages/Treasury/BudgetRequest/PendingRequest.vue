<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-row :gutter="16">
            <a-col class="gutter-row" :span="16">
                <a-card>
                    <a-typography-title :level="3" class="text-center">{{
                        title
                    }}</a-typography-title>
                    <a-form
                        :model="formState"
                        name="basic"
                        :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 20 }"
                        autocomplete="off"
                        @finish="onFinish"
                        @finishFailed="onFinishFailed"
                    >
                        <a-form-item label="BR No." name="brno">
                            <a-input v-model:value="formState.brno" readonly />
                        </a-form-item>

                        <a-row :gutter="8">
                            <a-col :span="8">
                                <a-form-item
                                    label="Date Requested"
                                    name="daterequested"
                                    :label-col="{ span: 12 }"
                                    :wrapper-col="{ span: 16 }"
                                >
                                    <a-date-picker
                                        disabled
                                        v-model:value="formState.dateRequested"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="10">
                                <a-form-item
                                    label="Date Needed"
                                    name="dateneeded"
                                    :label-col="{ span: 12 }"
                                    :wrapper-col="{ span: 16 }"
                                >
                                    <a-date-picker
                                        v-model:value="formState.dateNeeded"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-form-item label="Budget" name="budget">
                            <a-input v-model:value="formState.budget" type="number"/>
                        </a-form-item>

                        <a-form-item label="Remarks:" name="remarks">
                            <a-textarea
                                v-model:value="formState.remarks"
                                allow-clear
                            />
                        </a-form-item>
                        <a-row :gutter="8">
                            <a-col
                                :span="12"
                                v-if="
                                    props.data.br_requested_by !=
                                    page.auth.user.user_id.toString()
                                "
                            >
                                <a-form-item
                                    label="Created By:"
                                    :label-col="{ span: 8 }"
                                    :wrapper-col="{ span: 16 }"
                                >
                                    <a-input
                                        :value="formState.createdBy"
                                        readonly
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="12">
                                <a-form-item
                                    label="Updated By:"
                                    :label-col="{ span: 8 }"
                                    :wrapper-col="{ span: 16 }"
                                >
                                    <a-input
                                        readonly
                                        :value="formState.updatedBy"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-form-item label="Uploaded Document">
                            <a-space wrap class="ml-2">
                                <a-button
                                    type="primary"
                                    v-if="props.data.br_file_docno"
                                    @click="download(props.data.br_file_docno)"
                                >
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                    Download
                                </a-button>
                                <a-tag color="error" v-else>
                                    <template #icon>
                                        <close-circle-outlined />
                                    </template>
                                    NONE
                                </a-tag>
                            </a-space>
                        </a-form-item>
                        <a-form-item label="Upload Scan Copy">
                            <a-upload-dragger
                                v-model:file-list="fileList"
                                @change="handleChange"
                                name="file"
                                :before-upload="() => false"
                                list-type="picture"
                                :max-count="1"
                            >
                                <p class="ant-upload-drag-icon">
                                    <inbox-outlined></inbox-outlined>
                                </p>
                                <p class="ant-upload-text">
                                    Click or drag image to this area to upload
                                </p>
                                <p class="ant-upload-hint">
                                    Support for a single or bulk upload.
                                    Strictly prohibit from uploading company
                                    data or other band files
                                </p>
                            </a-upload-dragger>
                        </a-form-item>

                        <a-form-item :wrapper-col="{ offset: 22, span: 20 }">
                            <a-button type="primary" html-type="submit"
                                >Save</a-button
                            >
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col class="gutter-row" :span="8">
                <a-card>
                    <a-statistic
                        title="Current Balance"
                        :value="props.currentBudget"
                        :precision="2"
                        :value-style="{ color: '#3f8600' }"
                        style="margin-right: 50px"
                    >
                        <template #prefix>
                            <arrow-up-outlined />
                        </template>
                    </a-statistic>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import type {
    UploadChangeParam,
    UploadProps,
    UploadFile,
} from "ant-design-vue";
import { usePage, useForm } from "@inertiajs/vue3";
import dayjs, { Dayjs } from "dayjs";
import { PageProps } from "@/types/index";

interface FormState {
    brno: string;
    dateRequested: Dayjs;
    createdBy: string;
    updatedBy: string;
    remarks: string;
    budget: string;
    dateNeeded: Dayjs;
    file: UploadFile | null;
}

const props = defineProps<{
    title: string;
    currentBudget: string;
    data: {
        br_no: string;
        br_requested_at: Dayjs;
        br_requested_needed: Dayjs;
        br_request: string;
        br_remarks: string;
        br_file_docno: string;
        br_requested_by: string;
        user: {
            full_name: string;
        };
    };
}>();

const page = usePage<PageProps>().props;

const fileList = ref<UploadProps["fileList"]>([]);

const formState = useForm<FormState>({
    brno: props.data.br_no,
    dateRequested: dayjs(props.data.br_requested_at),
    dateNeeded: dayjs(props.data.br_requested_needed),
    budget: props.data.br_request,
    updatedBy: page.auth.user.full_name,
    createdBy: props.data.user.full_name,
    remarks: props.data.br_remarks,
    file: null,
});

const handleChange = (info: UploadChangeParam) => {
    formState.file = info.file;
};
const onFinish = (values: any) => {
    formState.transform((data) => ({
        ...data,
        document: props.data.br_file_docno
    }))
    .post(route('treasury.budget.request.budget.entry'));
};

const onFinishFailed = (errorInfo: any) => {
    console.log("Failed:", errorInfo);
};

const download = (file: string) => {
    console.log(file);
};
</script>
