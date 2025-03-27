<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { route } from 'ziggy-js';

const page = usePage<PageWithSharedProps>().props;

const highlightRoute = ref([route().current()]);

const handleClick = (e: any) => {
    highlightRoute.value = [e.key];
};

const setActiveTab = computed(() => {
    const currentR = highlightRoute.value[0].split(".");

    const keys = [
        "transactions",
        "reports", //add more key sub-menu
    ];
    const keysToOpen = [];

    keys.forEach(function (item) {
        if (currentR.includes(item)) {
            keysToOpen.push(item);
        }
    });
    return keysToOpen;
});
</script>

<template>
    <a-menu :openKeys="setActiveTab" v-model:selectedKeys="highlightRoute" theme="dark" mode="inline"
        @click="handleClick">
        <a-menu-item key="accounting.dashboard">
            <DashboardOutlined />
            <span>
                <Link :href="route('accounting.dashboard')">Dashboard</Link>
            </span>
        </a-menu-item>
        <a-sub-menu key="transactions">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Transaction</span>
                </span>
            </template>
            <a-menu-item key="requestGC">Request GC</a-menu-item>
            <a-menu-item key="requestGCwithName">Request GC(With Names)</a-menu-item>
            <a-menu-item key="requestGCwithName">
                <Link :href="route('retail.soldGc')">
                <span>
                    Sold C
                </span>
                </Link>
            </a-menu-item>
        </a-sub-menu>
        <a-menu-item key="customerSetup">
            <Link :href="route('admin.masterfile.customer.setup')">
            <UserOutlined />
            <span>Customer Setup</span>
            </Link>
        </a-menu-item>

        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <StockOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="accounting.reports.special.gc.approved">
                <Link :href="route('accounting.reports.special.gc.approved')">
                <DollarOutlined /> SPGC Approved</Link>
            </a-menu-item>
            <a-menu-item key="accounting.reports.special.gc.released">
                <Link :href="route('accounting.reports.special.gc.released')">
                <DollarOutlined /> SPGC Released</Link>
            </a-menu-item>
            <a-menu-item key="accounting.reports.generatedReports">
                <Link :href="route('accounting.reports.generatedReports')">
                <DollarOutlined /> Generated Reports</Link>
            </a-menu-item>
        </a-sub-menu>
    </a-menu>
</template>
