<script setup lang="ts">
</script>

<template>
    <a-menu-item key="dashboard">
        <GroupOutlined />
        <span> <Link :href="route('budget.ledger')"> Dashboard</Link></span>
    </a-menu-item>
    <a-menu-item key="budgetLedger">
        <GroupOutlined />
        <span> <Link :href="route('budget.ledger')"> Budget Ledger</Link></span>
    </a-menu-item>
    <a-menu-item key="spgcLedger">
        <GroupOutlined />
        <span> <Link :href="route('spgc.ledger')"> SPGC Ledger(Promotional)</Link></span>
    </a-menu-item>

    <a-sub-menu key="reports">
        <template #title>
            <span>
                <LineChartOutlined />
                <span>Reports</span>
            </span>
        </template>
        <a-menu-item key="verifiedGCReports"><Link :href="(route('spgc.approved.reports'))"></Link>SPGC Approved</a-menu-item>
        <a-menu-item key="beamAndGoGoReport">SPGC Released</a-menu-item>
    </a-sub-menu>
</template>
