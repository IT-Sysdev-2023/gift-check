<template>
    <a-card>
        <a-card class="mb-1">
            <a-statistic title="Regular GC Scanned Count" :value="counts.regular" class="text-center" />
        </a-card >
        <a-card class="mb-1">
            <a-statistic title="Special External GC Scanned Count" class="text-center" :value="counts.special" />
        </a-card>
        <a-card class="mb-1">
            <a-statistic title="Total GC Scanned" class="text-center" :value="counts.total" />
        </a-card>
        <a-card class="mb-1">
            <a-statistic title="Number of GC Scanned Today" class=" text-center" :value="counts.today" />
        </a-card>
    </a-card>
</template>
<script>
export default {
    props: {
        counts: Number,
    }
}
</script>
