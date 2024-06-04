<script setup lang="ts">

</script>

<template>
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
        <a-menu-item key="spgcapproved"><DollarOutlined /> SPGC Approved</a-menu-item>
        <a-menu-item key="spgcreleased"><DollarOutlined /> SPGC Released</a-menu-item>
    </a-sub-menu>


</template>
