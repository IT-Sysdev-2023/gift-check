<script setup lang="ts">
import {
    FileTextOutlined,
    FileOutlined,
    SwapOutlined,
    DollarOutlined,
    LineChartOutlined,
} from "@ant-design/icons-vue";
import { ref } from "vue";

const highlightRoute = ref([route().current()]);

const handleClick = (e: any) => {
    highlightRoute.value = [e.key];
};
</script>

<template>
    <a-menu
        v-model:selectedKeys="highlightRoute"
        theme="dark"
        mode="inline"
        @click="handleClick"
    >
        <a-menu-item key="treasury.dashboard">
            <file-outlined />
            <span>
                <Link :href="route('treasury.dashboard')">
                    Dashboard</Link
                ></span
            >
        </a-menu-item>
        <a-sub-menu key="menu-sub-masterfile">
            <template #title>
                <span>
                    <FileTextOutlined />
                    <span>Masterfile</span>
                </span>
            </template>
            <a-menu-item key="menu-item-customer">Customer</a-menu-item>
            <a-menu-item key="menu-item-special-external-setup"
                >Special External Setup</a-menu-item
            >
            <a-menu-item key="menu-item-payment-fund-setup"
                >Payment Fund Setup</a-menu-item
            >
        </a-sub-menu>

        <a-sub-menu key="menu-sub-transactions">
            <template #title>
                <span>
                    <DollarOutlined />
                    <span>Transactions</span>
                </span>
            </template>

            <a-menu-item key="menu-item-budget-request"
                >Budget Request</a-menu-item
            >
            <a-sub-menu key="menu-sub-adjustments-production">
                <template #title>
                    <span>
                        <span>Production Request</span>
                    </span>
                </template>
                <a-menu-item key="menu-item-gift-check">Gift Check</a-menu-item>
                <a-menu-item key="menu-item-envelope">Envelope</a-menu-item>
            </a-sub-menu>
            <a-menu-item key="menu-item-gc-allocation"
                >GC Allocation</a-menu-item
            >
            <a-menu-item key="menu-item-gc-releasing-retail-store"
                >GC Releasing (Retail Store)</a-menu-item
            >
            <a-menu-item key="menu-item-promo-gc-releasing"
                >Promo GC Releasing</a-menu-item
            >
            <a-menu-item key="menu-item-institution-gc-sales"
                >Institution GC Sales</a-menu-item
            >
            <a-menu-item key="menu-item-institution-gc-refund"
                >Institution GC Refund</a-menu-item
            >
            <a-menu-item key="menu-item-special-gc-payment"
                >Special GC Payment</a-menu-item
            >
            <a-menu-item key="menu-item-gc-sales-report-eod"
                >GC Sales Report (EOD)</a-menu-item
            >
        </a-sub-menu>

        <a-sub-menu key="menu-sub-adjustments">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Adjustments</span>
                </span>
            </template>
            <a-menu-item key="menu-item-budget-adjustments"
                >Budget Adjustments</a-menu-item
            >
            <a-menu-item key="menu-item-allocation">Allocation</a-menu-item>
        </a-sub-menu>

        <a-menu-item key="treasury.budget.ledger">
            <file-outlined />
            <span>
                <Link :href="route('treasury.budget.ledger')">
                    Budget Ledger</Link
                ></span
            >
        </a-menu-item>

        <a-menu-item key="treasury.gc.ledger">
            <file-outlined />
            <span
                ><Link :href="route('treasury.gc.ledger')">
                    GC Ledger</Link
                ></span
            >
        </a-menu-item>

        <a-sub-menu key="menu-sub-reports">
            <template #title>
                <span>
                    <LineChartOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="menu-item-gc-report">GC Report</a-menu-item>
        </a-sub-menu>
    </a-menu>
</template>
