<template>
    <a-float-button-group
        trigger="click"
        :style="{ right: '24px' }"
        v-model:open="openGeneratedReport"
    >
        <template #icon>
            <a-badge dot :offset="[0, -12]">
                <ExclamationCircleOutlined style="font-size: 20px" />
            </a-badge>
        </template>
        <a-card class="card-admin-style">
            <template #title>
                <span>Generated Report</span>
            </template>
            <a-space direction="vertical" style="width: 100%">
                <a-card>
                    <span>Generating Report, pls wait...</span>
                    <div>
                        <a-progress
                            :stroke-color="{
                                '0%': '#108ee9',
                                '100%': '#87d068',
                            }"
                            :percent="items.percentage"
                        />
                    </div>
                    <div class="flex justify-end">
                        <span
                            >{{ items.data.store }} -
                            {{ items.data.info }}</span
                        >
                    </div>
                </a-card>
            </a-space>
        </a-card>
    </a-float-button-group>
</template>

<script lang="ts" setup>
import { onMounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types/index";

const page = usePage<PageWithSharedProps>().props;

const openGeneratedReport = ref(false);

const items = ref<{
    percentage: number;
    data: {
        active: number;
        store: string;
        isDone: boolean;
        info: string;
    };
}>({
    percentage: 0,
    data: {
        active: 0,
        store: "",
        isDone: false,
        info: "Loading Please wait!",
    },
});

onMounted(() => {
    window.Echo.private(`treasury-report.${page.auth.user.user_id}`).listen(
        "TreasuryReportEvent",
        (e) => {
            openGeneratedReport.value = true;
            items.value = e;
        }
    );
});
</script>

<style lang="scss" scoped></style>
