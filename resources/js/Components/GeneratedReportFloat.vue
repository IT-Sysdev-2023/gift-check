<template>
    <a-float-button-group
        v-if="openFloat"
        trigger="click"
        :style="{ right: '24px' }"
        v-model:open="openGeneratedReport"
    >
        <!-- YOURE VISITING THIS PAGE.., THIS MEANS YOU HAVE REACH THE LEVEL OF A SENIOR PROGRAMMER -->

        <template #icon>
            <a-badge dot :offset="[0, -12]">
                <ExclamationCircleOutlined style="font-size: 20px" />
            </a-badge>
        </template>
        <a-card class="card-admin-style" style="height: 250px">
            <template #title>
                <span>Queue Reports</span>
            </template>
            <div style="height: 200px; overflow-y: auto; padding-right: 8px">
                <a-space direction="vertical" style="width: 100%;" class="flex; flex-col-reverse">
                    <a-card
                        v-for="(progress, reportId) in reportProgress"
                        :key="reportId"
                    >
                        <span
                            >Generating {{ progress.data.name }}, pls
                            wait...</span
                        >
                        <div>
                            <a-progress
                                :stroke-color="{
                                    '0%': '#108ee9',
                                    '100%': '#87d068',
                                }"
                                :percent="progress.percentage"
                            />
                        </div>
                        <div class="flex justify-between">
                            <a-button
                                type="primary"
                                size="small"
                                @click="fileLocation"
                                :disabled="progress.percentage !== 100"
                            >
                                <template #icon>
                                    <FolderOutlined />
                                </template>
                                Locate
                            </a-button>

                            <span
                                >{{ progress.data.store }} -
                                {{ progress.data.info }}</span
                            >
                        </div>
                    </a-card>
                </a-space>
            </div>
        </a-card>
    </a-float-button-group>
</template>

<script lang="ts" setup>
import { onMounted, reactive, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types/index";

const page = usePage<PageWithSharedProps>().props;

interface ProgressData {
    currentRow: number;
    name: string;
    totalRow: number;
    store: string;
    info: string;
}

interface ReportProgress {
    reportType: string;
    percentage: number;
    data: ProgressData;
}

const openGeneratedReport = ref(false);
const openFloat = ref(false);
const reportProgress = reactive<Record<string, ReportProgress>>({});

const handleOpenFloat = (e) => {
    console.log(e);
};
onMounted(() => {
    window.Echo.private(`treasury-report.${page.auth.user.user_id}`).listen(
        "TreasuryReportEvent",
        (e) => {
            openFloat.value = true;
            openGeneratedReport.value = true;

            reportProgress[e.reportId] = {
                reportType: e.reportType,
                percentage: e.percentage,
                data: e.data,
            };
        }
    );
});

const fileLocation = () => {
    router.visit(route("treasury.reports.generatedReports"));
};
</script>

<style lang="scss" scoped></style>
