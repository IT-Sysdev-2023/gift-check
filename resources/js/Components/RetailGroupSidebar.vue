<template>
    <a-menu-item key="retailgroup.dashboard">
        <BarcodeOutlined />
        <span>
            <Link :href="route('retailgroup.dashboard')">{{ page.auth.user.usertype == '8' ? 'Dashboard' :
                'Retail Group  Dashboard' }}</Link>
        </span>
    </a-menu-item>
    <a-menu-item key="storeLedger">
        <BarcodeOutlined />
        <span><Link :href="route('retailgroup.ledger')">Ledger</Link></span>
    </a-menu-item>
</template>
<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { route } from 'ziggy-js';

const page = usePage<PageWithSharedProps>().props;
</script>
