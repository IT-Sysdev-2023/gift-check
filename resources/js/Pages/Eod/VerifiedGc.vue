<template>
    <AuthenticatedLayout>
        <a-card>
            <div v-if="response.length" class="mb-4">
                <div>
                    <InfoCircleOutlined style="color: red" />
                    <span style="color: red"> Oppss barcode not found </span>
                </div>
                <a-collapse v-model:activeKey="activeKey" ghost>
                    <a-collapse-panel
                        key="1"
                        :header="
                            activeKey == '0'
                                ? 'Expand this to see the not found barcodes'
                                : 'Click here to hide the barcodes'
                        "
                        style="color: white"
                    >
                        <a-card>
                            <a-divider style="font-size: 14px; color: red"
                                >not found barcodes</a-divider
                            >
                            <div class="scroll-container">
                                <a-timeline class="mt-7">
                                    <a-timeline-item
                                        v-for="res in response"
                                        :key="res"
                                        color="red"
                                        >{{ res }}</a-timeline-item
                                    >
                                </a-timeline>
                            </div>
                        </a-card>
                    </a-collapse-panel>
                </a-collapse>
            </div>
            <div class="flex justify-end">
                <a-button
                    class="mb-3"
                    @click="submit"
                    v-if="record.data.length"
                >
                    <template #icon>
                        <SettingOutlined />
                    </template>
                    Start Process End Of Day
                </a-button>
            </div>
            <a-table
                size="small"
                :pagination="false"
                :data-source="record.data"
                :columns="columns"
                bordered
            >
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
import { notification } from "ant-design-vue";
import { ref } from "vue";
const props = defineProps({
    record: Object,
    columns: Array,
});

const response = ref({});
const activeKey = ref("0");

const submit = () => {
    router.get(
        route("eod.process"),
        {},
        {
            onSuccess: (res) => {
                notification[res.props.flash.status]({
                    message: res.props.flash.title,
                    description: res.props.flash.msg,
                });
                if (res.props.flash.data) {
                    response.value = res.props.flash.data;
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
