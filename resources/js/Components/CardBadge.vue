<script setup lang="ts">
export interface Props {
    count: number | undefined | string;
    title: string;
    color?: string;
    type?: string;
}

withDefaults(defineProps<Props>(), {
    color: "green",
    type: "success",
});
</script>

<template>
    <a-badge :count="count" :color="color" :show-zero="false">
        <a-alert :type="type" show-icon style="width: 300px">
            <template #message>
                <a-typography-text :delete="!count || count == null || count === '0'" :disabled="!count || count == null || count === '0'">{{
                    title
                }}</a-typography-text>
            </template>
            <template #action>
                <a-button
                    size="small"
                    type="primary"
                    :disabled="count === 0 || count == null || count === '0' "
                    :title="count?.toString()"
                    @click="$emit('event')"
                >
                    view
                </a-button>
            </template>
        </a-alert>
    </a-badge>
</template>
<!-- norien caren -->
