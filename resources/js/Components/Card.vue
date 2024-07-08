<script setup lang="ts">
export interface Props{
    title: string;
    card2?: string;
    useDefault?: boolean;
    pending?: number;
    approved?: number;
    cancelled?: number;
};

withDefaults(defineProps<Props>(), {
    card2: 'Approved Request'
})
</script>

<template>
    <a-col :span="8">
        <a-card :title="title" class="mb-5">
            <a-space direction="vertical" style="width: 100%" v-if="useDefault">
                <CardBadge
                    :count="pending"
                    title="Pending Request"
                    color="orange"
                    type="warning"
                    @event="$emit('pendingEvent')"
                />
                <CardBadge
                    :count="approved"
                    :title="card2"
                    @event="$emit('approvedEvent')"
                />
                <slot />
                <CardBadge
                    :count="cancelled"
                    title="Cancelled Request"
                    color="volcano"
                    type="error"
                    @event="$emit('cancelledEvent')"
                />
            </a-space>

            <a-space direction="vertical" style="width: 100%" v-else>
                <slot name="badge" />
            </a-space>
        </a-card>
    </a-col>
</template>
