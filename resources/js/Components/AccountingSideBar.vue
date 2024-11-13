<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;
</script>

<template>
    <a-sub-menu v-if="page.auth.user.usertype === '1'" key="accounting-side-bar">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Accounting Sidebar</span>
            </span>
        </template>
        <a-sub-menu key="menu-sub-masterfile">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Transaction</span>
                </span>
            </template>
            <a-menu-item key="requestGC">Request GC</a-menu-item>
            <a-menu-item key="requestGCwithName">Request GC(With Names)</a-menu-item>
        </a-sub-menu>
        <a-menu-item key="customerSetup">
            <UserOutlined />
            <span>Customer Setup</span>
        </a-menu-item>

        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <StockOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="spgcapproved">
                <DollarOutlined /> SPGC Approved
            </a-menu-item>
            <a-menu-item key="spgcreleased">
                <DollarOutlined /> SPGC Released
            </a-menu-item>
        </a-sub-menu>
    </a-sub-menu>

    <div v-else>
        <a-menu-item key="accounting-dashboard">
            <DashboardOutlined />
            <span><Link :href="route('accounting.dashboard')">Dashboard</Link></span>
        </a-menu-item>
        <a-sub-menu key="menu-sub-masterfile">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Transaction</span>
                </span>
            </template>
            <a-menu-item key="requestGC">Request GC</a-menu-item>
            <a-menu-item key="requestGCwithName">Request GC(With Names)</a-menu-item>
        </a-sub-menu>
        <a-menu-item key="customerSetup">
            <UserOutlined />
            <span>Customer Setup</span>
        </a-menu-item>

        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <StockOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="spgcapproved">
                <Link :href="route('accounting.reports.special.gc.approved')"><DollarOutlined /> SPGC Approved</Link>
            </a-menu-item>
            <a-menu-item key="spgcreleased">
                <Link :href="route('accounting.reports.special.gc.released')"><DollarOutlined /> SPGC Released</Link>
            </a-menu-item>
        </a-sub-menu>
    </div>



</template>
