<script setup lang="ts">
import {
    FileOutlined,
    SwapOutlined,
    DollarOutlined,
    LineChartOutlined,
    BarcodeOutlined,
    GroupOutlined
} from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <a-menu-item key="budgetLedger">
        <GroupOutlined />
        <span> <Link :href="route('budget.ledger')"> Budget Ledger</Link></span>
    </a-menu-item>
    <a-menu-item key="spgcLedger">
        <GroupOutlined />
        <span> <Link :href="route('spgc.ledger')"> SPGC Ledger(Promotional)</Link></span>
    </a-menu-item>

    <a-sub-menu key="reports">
        <template #title>
            <span>
                <LineChartOutlined />
                <span>Reports</span>
            </span>
        </template>
        <a-menu-item key="verifiedGCReports">SPGC Approved</a-menu-item>
        <a-menu-item key="beamAndGoGoReport">SPGC Released</a-menu-item>
    </a-sub-menu>
</template>
