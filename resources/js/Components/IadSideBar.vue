<script setup lang="ts">
import {
    LineChartOutlined,
    GroupOutlined,
    QrcodeOutlined,
    RiseOutlined
} from "@ant-design/icons-vue";

</script>

<template>
    <a-menu-item key="iad">
        <QrcodeOutlined />
        <span> <Link :href="route('iad.dashboard')"> {{ $page.props.auth.user.usertype == 1 ? 'Iad Dashboard' : 'Dashboard'}}</Link></span>
    </a-menu-item>
</template>
