<template>
    <a-card>
        <a-statistic :title="title" :value="count" :precision="2" class="demo-class"
            :value-style="{ color: '#181C14' }">
            <template #prefix>
                <p>₱</p>
            </template>
        </a-statistic>
    </a-card>
</template>
<script setup>
defineProps({
    count: Number,
    title: String,
})
</script>
