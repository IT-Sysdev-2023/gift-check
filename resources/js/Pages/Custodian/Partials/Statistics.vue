<template>
    <a-card>
        <a-statistic title="Regular GC Scanned Count" :value="counts.regular" class="mb-4" />
        <a-statistic title="Special External GC Scanned Count" class="mb-4" :value="counts.special" />
        <a-statistic title="Total GC Scanned" class="mb-4" :value="counts.total" />
        <a-statistic title="Number of GC Scanned Today" class="mb-4"  :value="counts.today" />
    </a-card>
</template>
<script>
export default {
props: {
    counts: Number,
}
}
</script>
