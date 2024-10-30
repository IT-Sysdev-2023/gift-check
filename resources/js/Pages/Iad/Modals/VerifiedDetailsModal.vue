<template>
    <a-modal title="Verified Details" :footer="false">
        <p>{{ record }}</p>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item label="Date / Time Verified:">{{ record.vs_date }}</a-descriptions-item>
        </a-descriptions>
    </a-modal>
</template>
<script setup>
defineProps({
    record: Object,
})
</script>
