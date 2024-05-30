<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message">
        <p class="ml-4 text-red-600" style="font-size: 12px;">
            {{ message }}
        </p>
    </div>
</template>
