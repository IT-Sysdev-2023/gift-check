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
                        <a-input v-model:value="formState.brno" disabled />
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
                                style="width: 280px;"
                                    v-model:value="formState.dateRequested"
                                    disabled
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :span="10">
                            <a-form-item
                                label="Date Needed"
                                name="dateneeded"
                                :label-col="{ span: 18 }"
                                :wrapper-col="{ span: 16 }"
                            >
                                <a-date-picker
                                style="width: 300px;"
                                    v-model:value="formState.dateNeeded"
                                    disabled
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-form-item label="Budget" name="budget">
                        <a-input v-model:value="formState.budget" />
                    </a-form-item>

                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea
                            v-model:value="formState.remarks"
                            allow-clear
                        />
                    </a-form-item>
                    <a-row :gutter="8">
                        <a-col :span="12">
                            <a-form-item
                                label="Created By:"
                                :label-col="{ span: 8 }"
                                :wrapper-col="{ span: 16 }"
                            >
                                <a-input
                                    :value="formState.createdBy"
                                    disabled
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
                                    :value="formState.updatedBy"
                                    disabled
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-form-item label="Uploaded Document">
                        <a-tag color="red">NONE</a-tag>
                    </a-form-item>
                    <a-form-item label="Upload Scan Copy">
                        <a-upload-dragger
                            v-model:fileList="fileList"
                            name="file"
                            list-type="picture"
                            :max-count="1"
                            action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
                            
                        >
                            <p class="ant-upload-drag-icon">
                                <inbox-outlined></inbox-outlined>
                            </p>
                            <p class="ant-upload-text">
                                Click or drag image to this area to upload
                            </p>
                            <p class="ant-upload-hint">
                                Support for a single or bulk upload. Strictly
                                prohibit from uploading company data or other
                                band files
                            </p>
                        </a-upload-dragger>
                        <!-- @change="handleChange"
                            @drop="handleDrop" -->
                        <!-- <a-upload
                            v-model:file-list="fileList"
                            list-type="picture"
                            :max-count="1"
                            action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
                        >
                            <a-button>
                                <upload-outlined></upload-outlined>
                                Upload (Max: 1)
                            </a-button>
                        </a-upload> -->
                    </a-form-item>

                    <a-form-item :wrapper-col="{ offset: 22, span: 20 }">
                        <a-button type="primary" html-type="submit"
                            >Save</a-button
                        >
                    </a-form-item>
                </a-form>
            </a-col>
            <a-col class="gutter-row" :span="8">
                <a-card>
                    <a-statistic
                        title="Current Balance"
                        :value="11.28"
                        :precision="2"
                        suffix="%"
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
import { reactive } from "vue";
import type { UploadProps } from "ant-design-vue";

const fileList = ref<UploadProps["fileList"]>([]);

interface FormState {
    brno: string;
    dateRequested: string;
    createdBy: string;
    updatedBy: string;
    remarks: string;
    budget: string;
    dateNeeded: string;
    remember: boolean;
}

const formState = reactive<FormState>({
    brno: "",
    updatedBy: "",
    createdBy: "",
    budget: "",
    remarks: "",
    dateRequested: "",
    dateNeeded: "",
    remember: true,
});

defineProps<{
    title: String;
    data: Object;
}>();

const onFinish = (values: any) => {
    console.log("Success:", values);
};

const onFinishFailed = (errorInfo: any) => {
    console.log("Failed:", errorInfo);
};
</script>

<!-- 
<template>
    <Head :title="title" />
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route('treasury.dashboard')">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-form>
        <a-row :gutter="[16, 16]" title="Something">
            <a-col :span="12">
                <a-card>
                    <a-form-item label="BR No:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="rfprom" disabled />
                    </a-form-item>
                    <a-form-item label="Date Requested" name="daterequested">
                        <a-date-picker v-model:value="daterequested" disabled />
                    </a-form-item>
                    <a-form-item label="Date Needed" name="dateneeded">
                        <a-date-picker v-model:value="dateneeded" />
                    </a-form-item>
                    <a-form-item label="Budget:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="rfprom" />
                    </a-form-item>
                    <a-form-item label="Uploaded Document:" name="remarks">
                        <span>NONE</span>
                    </a-form-item>
                    <a-form-item label="Upload Scan Copy:" name="file">
                        <a-upload v-model:file-list="fileList" name="file" action="" :headers="headers"
                            @change="handleChange">
                            <a-button>
                                <upload-outlined></upload-outlined>
                                Click to Upload
                            </a-button>
                        </a-upload>
                    </a-form-item>
                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea v-model:value="remarks" allow-clear />
                    </a-form-item>
                    <a-form-item label="BR No:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="rfprom" disabled />
                    </a-form-item>
                   
                   
                    

                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-card title="Total Promo GC Request" :bordered="false">
                        <a-input :value="'₱ ' + totalValue" readonly style="font-size: xx-large;"></a-input>
                    </a-card>

                </a-card>
            </a-col>
        </a-row>
    </a-form>
</template>

<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props:{
      title: String,
      data: Object
    },
    components: {
        PlusOutlined,
        BarcodeOutlined
    },
    data() {
        return {
            rfprom: '',
            daterequested: '',
            dateneeded: '',
            fileList: [],
            headers: {},
            remarks: '',
            value1: '',
            quantities: ['', '', '', '', ''],
            denominations: [100, 500, 1000, 2000, 5000],
            totalValue: 0
        };
    },
    methods: {
        handleChange() {
            this.calculateTotal();
        },
        calculateTotal() {
            this.totalValue = this.quantities.reduce((acc, qty, index) => {
                // Ensure qty is treated as a number
                return acc + (Number(qty) * this.denominations[index]);
            }, 0);
        }
    },
    watch: {
        quantities: {
            handler: 'calculateTotal',
            deep: true
        }
    }
};
</script> -->
