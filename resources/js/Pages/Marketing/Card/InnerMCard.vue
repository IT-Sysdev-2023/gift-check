<template>
    <a-badge
        class="mb-2"
        :count="count"
        :overflow-count="Infinity"
        :number-style="{
            backgroundColor: color,
        }"
    >
        <a-button
            :disabled="count === 0"
            style="width: 340px"
            type="primary"
            @click="() => $inertia.get(route(routeTo))"
        >
            {{ label }}
        </a-button>
    </a-badge>
</template>

<script setup lang="ts">

interface Props {
    label: string;
    routeTo: string;
    count: number;
    color?: string;
}

withDefaults(defineProps<Props>(), {
    color: "#1677ff",
});
</script>
