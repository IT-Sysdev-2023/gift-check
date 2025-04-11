<template>
    <AuthenticatedLayout>

        <a-card>
            <div v-if="isGenerating">
                <div class="flex justify-between">
                    <div>
                        <a-typography-text keyboard>{{ progressBar?.message }}</a-typography-text>
                    </div>
                    <div>
                        {{ progressBar?.currentRow }} to {{ progressBar?.totalRows }}
                    </div>
                </div>
                <a-progress :stroke-color="{
                    from: '#108ee9',
                    to: '#87d068',
                }" :percent="progressBar?.percentage" status="done" :size="[10, 20]" />

            </div>
            <div v-if="response.length" class="mb-4">
                <div>
                    <InfoCircleOutlined style="color: red" />
                    <span style="color: red"> Oppss barcode not found </span>
                </div>
                <a-collapse v-model:activeKey="activeKey" ghost>
                    <a-collapse-panel key="1" :header="activeKey == '0'
                        ? 'Expand this to see the not found barcodes'
                        : 'Click here to hide the barcodes'
                        " style="color: white">
                        <a-divider style="font-size: 14px; color: red">There are {{ response.length }} Barcodes not
                            found!</a-divider>
                        <div class="scroll-container">
                            <a-timeline class="mt-7">
                                <a-timeline-item v-for="res in response" :key="res" color="red">{{ res
                                    }}</a-timeline-item>
                            </a-timeline>
                        </div>
                    </a-collapse-panel>
                </a-collapse>
            </div>
            <div>
                <div v-if="[13, 8].includes(page.auth.user.store_assigned)" class="mb-5">
                    <div v-if="canBeEod">
                        <a-button size="large" class="mt-5 mb-3" block type="primary" @click="processEod">
                            Process Eod
                        </a-button>
                        <a-alert message="All barcodes have been verified and are good."
                            description="All barcodes have been verified and are correct. The data is now ready for end-of-day (EOD) processing."
                            type="success" show-icon />
                    </div>
                    <div v-else>
                        <a-upload-dragger name="file" :multiple="true" :before-upload="() => false"
                            @change="uploadFileList" @drop="handleDrop">
                            <p class="ant-upload-drag-icon">
                                <inbox-outlined></inbox-outlined>
                            </p>
                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                            <p class="ant-upload-hint">
                                Support for a single or bulk upload. Strictly prohibit from uploading company data or
                                other
                                band files
                            </p>
                        </a-upload-dragger>
                    </div>
                </div>
                <div v-else>
                    <a-button size="large" :loading="isGenerating" type="dashed" block class="mb-10 mt-10"
                        @click="submit" v-if="record.data.length">
                        <template #icon>
                            <SettingOutlined />
                        </template>
                        Start Process End Of Day
                    </a-button>
                </div>
            </div>
            <a-table size="small" :pagination="false" :data-source="record.data" :columns="columns" bordered>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'status'">
                        <a-tag color="green">{{ record.status }}</a-tag>
                    </template>
                </template>
            </a-table>

            <pagination :datarecords="record" class="mt-5" />

            <a-card class="mt-5" v-if="notFound" title="Not Found Barcodes">
                <a-timeline class="mt-6">
                    <a-timeline-item color="red" v-for="(item, key) in notFound" :key="key" class="text-red-500">{{
                        item.barcode
                        }}</a-timeline-item>
                </a-timeline>
            </a-card>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import { notification } from "ant-design-vue";
import { onMounted, ref } from "vue";

const props = defineProps({
    record: Object,
    columns: Array,
});

const fileListUploaded = ref([]);

const page = usePage().props;

const response = ref({});
const activeKey = ref("0");
const progressBar = ref();
const isGenerating = ref(false);
const notFound = ref();
const canBeEod = ref(false);

const uploadFileList = (uploaded) => {
    fileListUploaded.value = uploaded.fileList;
    router.post(route('eod.upload.file'),
        {
            file: uploaded.fileList,
            data: props.record
        },
        {
            onSuccess: (res) => {
                if (res.props.flash.status == 'error') {
                    notFound.value = res.props.flash.data;
                }
                if (res.props.flash.status == 'success') {
                    canBeEod.value = true;
                }
                notification[res.props.flash.status]({
                    message: res.props.flash.title,
                    description: res.props.flash.msg,
                });


            },
            preserveState: true,
        }
    );
}

const submit = () => {
    progressBar.value = [];
    response.value = [];
    router.get(
        route("eod.process"),
        {},
        {
            onStart: () => {
                window.Echo.private(`process-eod.${page.auth.user.user_id}`)
                    .listen(".start-processing-eod", (e) => {
                        progressBar.value = e;
                    });
                isGenerating.value = true;
            },
            onSuccess: (res) => {
                notification[res.props.flash.status]({
                    message: res.props.flash.title,
                    description: res.props.flash.msg,
                });
                if (res.props.flash.data) {
                    response.value = res.props.flash.data;
                    isGenerating.value = false;
                }
            },
            preserveState: true,
        },
    );
};

const processEod = () => {
    // console.log(fileListUploaded.value);
    router.post(route('eod.process.eod.altmo'), {
        file: fileListUploaded.value,
        data: props.record
    })
}

</script>

<style scoped>
.scroll-container {
    max-height: 250px;
    overflow-y: auto;
}
</style>
