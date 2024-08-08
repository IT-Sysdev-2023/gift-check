<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;
</script>

<template>
    <a-menu-item key="dashboard">
        <GroupOutlined />
        <span> <Link :href="route('finance.dashboard')">  {{ page.auth.user.usertype == '1' ? 'Finance Dashboard' : 'Dashboard'}}</Link></span>
    </a-menu-item>
    <a-menu-item key="budgetLedger">
        <GroupOutlined />
        <span> <Link :href="route('finance.budget.ledger')"> Budget Ledger</Link></span>
    </a-menu-item>
    <a-menu-item key="spgcLedger">
        <GroupOutlined />
        <span> <Link :href="route('finance.spgc.ledger')"> SPGC Ledger(Promotional)</Link></span>
    </a-menu-item>
    <a-menu-item key="reports">
        <GroupOutlined />
        <span> <Link :href="route('finance.approved.released.reports')"> Reports</Link></span>
    </a-menu-item>
</template>
