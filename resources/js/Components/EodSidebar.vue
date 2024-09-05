<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;
</script>
<template>
    <a-menu-item key="customerSetup">
        <DashboardOutlined />
        <span>Dashboard</span>
    </a-menu-item>
    <a-menu-item key="customerSetup">
        <BankOutlined />
        <span>
            <Link :href="route('eod.verified.gc')">
                Store Eod
            </Link>
        </span>
    </a-menu-item>
</template>
