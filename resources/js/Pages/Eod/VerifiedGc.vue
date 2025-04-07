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
                <div v-if="page.auth.user.it_type == '1'">
                    <a-button size="large" :loading="isGenerating" type="dashed" block class="mb-10 mt-10"
                        @click="submit" v-if="record.data.length">
                        <template #icon>
                            <SettingOutlined />
                        </template>
                        Start Process End Of Day
                    </a-button>
                </div>
                <div v-else>
                    <a-alert class="w-full mb-3" message="Please be informed that performing end of day (EOD) will be centralized as of today. For concerns, please contact 1844/1953 and look for Norien & Claire" type="info" show-icon />
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
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import { notification } from "ant-design-vue";
import { onMounted, ref } from "vue";
import CheckBarocde from "./Partials/CheckBarocde.vue";
const props = defineProps({
    record: Object,
    columns: Array,
});

const page = usePage().props;

const response = ref({});
const activeKey = ref("0");
const progressBar = ref();
const isGenerating = ref(false);

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


</script>

<style scoped>
.scroll-container {
    max-height: 250px;
    overflow-y: auto;
}
</style>
