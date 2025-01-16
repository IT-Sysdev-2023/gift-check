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
            @click="() => router.get(route(routeTo))"
        >
            {{ label }}
        </a-button>
    </a-badge>
</template>

<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import {  usePage, router } from "@inertiajs/vue3";
interface Props {
    label: string;
    routeTo: string;
    count: number | string;
    color?: string;
}
const page = usePage<PageWithSharedProps>().props;
withDefaults(defineProps<Props>(), {
    color: "#1677ff",
});
</script>
