<script setup lang="ts">
import Badge from '@/Components/Badge.vue';

defineProps<{
    title: string;
    useDefault?: boolean;
    pending?: number;
    approved?: number;
    cancelled?: number;
}>();

</script>

<template>
    <a-col :span="8">
        <a-card :title="title" class="mb-5">
            <a-space direction="vertical" style="width: 100%" v-if="useDefault">
                <Badge :count="pending" title="Pending Request" color="orange" type="warning"/>
                <Badge :count="approved" title="Approved Request"/>
                <slot/>
                <Badge :count="cancelled" title="Cancelled Request" color="volcano" type="error"/>
            </a-space>
            
            <a-space direction="vertical" style="width: 100%" v-else>
               <slot name="badge"/>
            </a-space>
        </a-card>
    </a-col>
</template>
