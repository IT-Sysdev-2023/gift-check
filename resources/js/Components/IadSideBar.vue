<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
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
    </div>
</template>
