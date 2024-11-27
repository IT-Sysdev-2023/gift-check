<template>
    <a-float-button-group
        v-if="state.isFloatButtonVisible"
        trigger="click"
        :style="{ right: '24px' }"
        v-model:open="state.isFloatOpen"
    >
        <!-- YOURE VISITING THIS PAGE.., THIS MEANS YOU HAVE REACH THE END OF PROGRAMMER -->

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
                <a-space
                    direction="vertical"
                    style="width: 100%"
                    class="flex; flex-col-reverse"
                >
                    <a-skeleton
                        :loading="Object.keys(reportProgress).length === 0"
                    />
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
                                @click="routeToLocation"
                                :disabled="progress.percentage < 100"
                            >
                                <template #icon>
                                    <FolderOutlined />
                                </template>
                                Locate
                            </a-button>

                            <span
                                >{{ progress.data?.store }} -
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
import { onMounted, reactive, computed, watch, onBeforeUnmount } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types/index";
import { useQueueState } from "@/stores/queue-state";
import { UserType } from "@/userType";

const page = usePage<PageWithSharedProps>().props;

interface ProgressData {
    currentRow: number;
    name: string;
    totalRow: number;
    store?: string;
    info: string;
    isDone?: boolean
}

interface ReportProgress {
    reportType: string;
    percentage: number;
    data: ProgressData;
}

const {
    treasury,
    retail,
    admin,
    finance,
    accounting,
    iad,
    custodian,
    marketing,
    retailgroup,
    eod,
    storeaccounting,
} = UserType();

const reportProgress = reactive<Record<string, ReportProgress>>({});
const state = useQueueState();
onMounted(() => {
    const listenTo = whichShouldListenTo.value;

    window.Echo.private(`${listenTo.channel}${page.auth.user.user_id}`).listen(
        listenTo.listen,
        (e) => {
            state.setGenerateButton(false);
            reportProgress[e.reportId] = {
                reportType: e.reportType,
                percentage: e.percentage,
                data: e.data,
            };
        }
    );
});

const whichShouldListenTo = computed(() => {
    if (treasury.value) {
        return {
            channel: "treasury-report.",
            listen: "TreasuryReportEvent",
            route: "treasury.reports.generatedReports",
        };
    } else if (accounting.value) {
        return {
            channel: "accounting-report.",
            listen: "AccountingReportEvent",
            route: "accounting.reports.generatedReports",
        };
    } else {
        return {
            channel: "",
            listen: "",
        };
    }
});
onBeforeUnmount(() => {
    isProgressFinish();
});

const isProgressFinish = () => {
    if (
        Object.values(reportProgress).every(
            (report) => report.percentage >= 100
        )
    ) {
        state.setFloatButton(false);
    }
};

const routeToLocation = () => {
    isProgressFinish();
    router.visit(route(whichShouldListenTo.value.route));
};
</script>

<style lang="scss" scoped></style>
