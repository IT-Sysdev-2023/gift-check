<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { route } from 'ziggy-js';
const page = usePage<PageWithSharedProps>().props;
import {
    LineChartOutlined,
    GroupOutlined,
    QrcodeOutlined,
    RiseOutlined
} from "@ant-design/icons-vue";

</script>

<template>
    <!-- <a-sub-menu v-if="page.auth.user.usertype  === '1'" key="fad-side-bar">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Fad Sidebar</span>
            </span>
        </template>

        <a-menu-item key="custodian">
            <DashboardOutlined />
            <span>
                <Link :href="route('custodian.dashboard')"> {{ page.auth.user.usertype == '1' ? 'Custodian Dashboard' :
                    'Dashboard' }}</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="barcodeChecker">
            <QrcodeOutlined />
            <span>
                <Link :href="route('custodian.barcode.checker')"> Barcode Checker</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="text-file">
            <CloudUploadOutlined />
            <span>
                <Link :href="route('custodian.textfile.uploader')"> Textfile Uploader</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="recievedGC">
            <GroupOutlined />
            <span>
                <Link :href="route('custodian.received.gc')"> Received Gc</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="gcTracking">
            <RiseOutlined />
            <span>GC Tracking</span>
        </a-menu-item>
    </a-sub-menu> -->
    <div>
        <a-menu-item key="custodian">
            <DashboardOutlined />
            <span>
                <Link :href="route('custodian.dashboard')"> {{ page.auth.user.usertype == '1' ? 'Custodian Dashboard' :
                    'Dashboard' }}</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="barcodeChecker">
            <QrcodeOutlined />
            <span>
                <Link :href="route('custodian.barcode.checker')">Barcode Checker</Link>
            </span>
        </a-menu-item>
        <!-- <a-menu-item key="textfileUploader">
            <CloudUploadOutlined />
            <span>
                <Link :href="route('custodian.textfile.uploader')">Textfile Uploader</Link>
            </span>
        </a-menu-item> -->

        <a-menu-item key="recievedGC">
            <GroupOutlined />
            <span>
                <Link :href="route('custodian.received.gc')">Received Gc</Link>
            </span>
        </a-menu-item>
        <a-menu-item key="gcTracking">
            <RiseOutlined />
            <span><Link :href="route('custodian.tracking.gctracking')"></Link>GC Tracking</span>
        </a-menu-item>
    </div>


</template>
