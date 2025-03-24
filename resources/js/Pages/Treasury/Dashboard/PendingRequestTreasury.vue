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
                <a-card :loading="formState.processing">
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
                            <!-- <a-col :span="10">
                                <a-form-item
                                    label="Date Needed"
                                    name="dateneeded"
                                    :label-col="{ span: 12 }"
                                    :wrapper-col="{ span: 16 }"
                                >
                                    <a-date-picker
                                    :disabled-date="disabledDate"
                                        v-model:value="formState.dateNeeded"
                                    />
                                </a-form-item>
                            </a-col> -->
                        </a-row>

                        <a-form-item label="Budget" name="budget">
                            <ant-input-number
                                v-model:amount="formState.budget"
                            />
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

                        <a-form-item
                            label="Upload Scan Copy"
                            :validate-status="
                                formState.errors.file ? 'error' : ''
                            "
                            :help="formState.errors.file"
                        >
                            <ant-upload-image @handleChange="handleChange" />
                        </a-form-item>

                        <a-form-item :wrapper-col="{ offset: 22, span: 20 }">
                            <a-button type="primary" html-type="submit"
                                >Save</a-button
                            >
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col class="gutter-row space-y-5" :span="8">
                <a-card>
                    <a-statistic
                        title="Regular GC Budget"
                        :value="props.regularBudget"
                        :precision="2"
                        style="margin-right: 50px"
                    >
                        <template #prefix>
                            <FireOutlined twoToneColor="#3f8600" />
                        </template>
                    </a-statistic>
                </a-card>
                <a-card>
                    <a-statistic
                        title="Special Gc Budget"
                        :value="props.specialBudget"
                        :precision="2"
                        style="margin-right: 50px"
                    >
                        <template #prefix>
                            <FireOutlined twoToneColor="#3f8600" />
                        </template>
                    </a-statistic>
                </a-card>
                <a-card>
                    <a-statistic
                        title="Total Balance"
                        :value="props.currentBudget"
                        :precision="2"
                        :value-style="{ color: '#3f8600' }"
                        style="margin-right: 50px"
                    >
                        <template #prefix>
                            <FireOutlined twoToneColor="#3f8600" />
                        </template>
                    </a-statistic>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import dayjs, { Dayjs } from "dayjs";
import {
    PageProps,
    FormState,
    FlashProps,
} from "@/../../resources/js/types/index";
import { onProgress } from "@/../../resources/js/Mixin/UiUtilities";

const props = defineProps<{
    title: string;
    currentBudget: string;
    specialBudget: string;
    regularBudget: string;
    data: {
        br_id: number;
        br_no: string;
        br_requested_at: Dayjs;
        br_requested_needed: Dayjs;
        br_request: string;
        br_remarks: string;
        br_file_docno: string;
        br_requested_by: string;
        br_group: number;
        user: {
            user_id: number;
            full_name: string;
        };
    };
}>();

const page = usePage<PageProps>().props;

// const fileList = ref<UploadProps["fileList"]>([]);

const formState = useForm<FormState>({
    brno: props.data.br_no,
    dateRequested: dayjs(props.data.br_requested_at),
    dateNeeded: dayjs(props.data.br_requested_needed),
    budget: props.data.br_request,
    updatedBy: page.auth.user.full_name,
    createdBy: props.data.user.full_name,
    remarks: props.data.br_remarks,
    group: props.data.br_group,
    file: null,
});

const handleChange = (info: UploadChangeParam) => {
    formState.file = info.file;
};
const { openNotification } = onProgress();
const onFinish = () => {
    formState
        .transform((data) => ({
            ...data,
            updatedById: props.data.user.user_id,
            dateRequested: data.dateRequested.format("YYYY-MM-DD HH:mm:ss"),
            // dateNeeded: data.dateNeeded.format("YYYY-MM-DD"),
            document: props.data.br_file_docno,
        }))
        .put(route("treasury.budget.request.budget.entry", props.data.br_id), {
            preserveScroll: true,
            onSuccess: (pages: { props: FlashProps }) => {
                openNotification(pages.props.flash);
                router.get(route("treasury.dashboard"));
            },
        });
};

const download = (file: string) => {
    const url = route("treasury.budget.request.download.document", {
        file: file,
    });
    location.href = url;
};
</script>
