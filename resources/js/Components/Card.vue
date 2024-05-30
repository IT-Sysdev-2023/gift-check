<script setup lang="ts">
const props = defineProps<{
    title: string;
}>();
</script>

<template>
    <a-col :span="8">
        <a-card :title="title">
            <a-space direction="vertical" style="width: 100%">
                <a-badge count="5" color="orange">
                    <a-alert message="Pending Request" type="warning" show-icon style="width: 300px;" >
                        <template #action>
                            <a-button size="small" type="primary"
                                >view</a-button
                            >
                        </template>
                    </a-alert>
                </a-badge>
                <a-badge count="5" color="green">
                    <a-alert message="Approved Request" type="success" show-icon style="width: 300px;" >
                        <template #action>
                            <a-button size="small" type="primary"
                                >view</a-button
                            >
                        </template>
                    </a-alert>
                </a-badge>
                <a-badge count="5" color="volcano">
                    <a-alert message="Cancelled Request" type="error" show-icon style="width: 300px;" >
                        <template #action>
                            <a-button size="small" type="primary"
                                >view</a-button
                            >
                        </template>
                    </a-alert>
                </a-badge>
            </a-space>
        </a-card>
    </a-col>
</template>
