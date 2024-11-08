<template>
    <a-modal title="Verified Details" :footer="false">
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;"  label="Date Verified:">{{ record.date }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;"  label="Time Verified">{{ record.time }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions class="mt-5" layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;"  label="Verfied By ">{{ record.user.full_name }}</a-descriptions-item>
        </a-descriptions>
    </a-modal>
</template>
<script setup>
defineProps({
    record: Object,
})
</script>
