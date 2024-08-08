<script setup lang="ts">
import {
    LineChartOutlined,
    GroupOutlined,
    QrcodeOutlined,
    RiseOutlined
} from "@ant-design/icons-vue";

</script>

<template>
    <a-menu-item key="custodian">
        <QrcodeOutlined />
        <span> <Link :href="route('custodian.dashboard')"> Dashboard</Link></span>
    </a-menu-item>
    <a-menu-item key="barcodeChecker">
        <QrcodeOutlined />
        <span> <Link :href="route('custodian.barcode.checker')"> Barcode Checker</Link></span>
    </a-menu-item>
    <a-menu-item key="recievedGC">
        <GroupOutlined />
        <span>Recieved GC</span>
    </a-menu-item>
    <a-menu-item key="gcTracking">
        <RiseOutlined />
        <span>GC Tracking</span>
    </a-menu-item>

</template>
