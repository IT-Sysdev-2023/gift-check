<template>
    <a-descriptions-item label="label" :labelStyle="{ fontWeight: 'bold' }">{{ props.desc }}</a-descriptions-item>
</template>

<script setup lang="ts">
const props = defineProps<{
    label: string;
    desc: string;
}>();
</script>
