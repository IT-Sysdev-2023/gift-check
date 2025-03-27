<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { route } from 'ziggy-js';
import {
    LineChartOutlined,
    GroupOutlined,
    QrcodeOutlined,
    RiseOutlined
} from "@ant-design/icons-vue";
import { usePage } from "@inertiajs/vue3";
const page = usePage<PageWithSharedProps>().props;
</script>

<template>
    <a-sub-menu v-if="page.auth.user.usertype === '1'" key="iad-side-bar">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Iad Sidebar</span>
            </span>
        </template>

        <a-menu-item key="iad">
            <QrcodeOutlined />
            <span>
                <Link :href="route('iad.dashboard')"> {{ page.auth.user.usertype == 1 ? 'Iad Dashboard' : 'Dashboard' }}
                </Link>
            </span>
        </a-menu-item>
    </a-sub-menu>

    <div v-else>
        <a-menu-item key="iad">
            <QrcodeOutlined />
            <span>
                <Link :href="route('iad.dashboard')"> {{ page.auth.user.usertype == 1 ? 'Iad Dashboard' : 'Dashboard' }}
                </Link>
            </span>
        </a-menu-item>
        <a-menu-item key="iad-audit-store">
            <QrcodeOutlined />
            <span>
                <Link :href="route('iad.audit.store')">Audit (Store)
                </Link>
            </span>
        </a-menu-item>
        <a-menu-item key="iad-audit-store">
            <QrcodeOutlined />
            <span>
                <Link :href="route('iad.versoldused.index')">Verified/Sold/Used GC
                </Link>
            </span>
        </a-menu-item>
        <a-sub-menu v-if="page.auth.user.usertype === '10'" key="iad-side-bar">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Excel Reports</span>
                </span>
            </template>

            <a-menu-item key="iad-excel-verified">
                <UnorderedListOutlined />
                <span>
                    <Link :href="route('iad.excel.verified')">Verified GC Reports</Link>
                </span>
            </a-menu-item>
            <a-menu-item key="iad-excel-verified">
                <OrderedListOutlined />
                <span>
                    <Link :href="route('iad.excel.purchased')">Store GC Purchased Reports</Link>
                </span>
            </a-menu-item>
            <a-menu-item key="iad-excel-special-gc-reviewed">
                <OrderedListOutlined />
                <span>
                    <Link :href="route('iad.excel.special')">Special GC Reviewed</Link>
                </span>
            </a-menu-item>
        </a-sub-menu>
    </div>
</template>
