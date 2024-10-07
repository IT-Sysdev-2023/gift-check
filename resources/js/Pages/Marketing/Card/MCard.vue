<template>
    <a-card :title="title">
        <a-badge :count="1">
            <a-button style="width: 340px; margin-top: 13px ;" type="primary" @click="() => $inertia.get(route(pendingRoute))" danger>
                Pending
            </a-button>
        </a-badge>
        <a-badge :count="5" :number-style="{ backgroundColor: '#1677ff' }">
            <a-button style="width: 340px; margin-top: 13px ;" type="primary" @click="() => $inertia.get(route(approvedRoute))">
                Approved
            </a-button>
        </a-badge>
        <a-badge :count="5" :number-style="{ backgroundColor: '#111827' }">
            <a-button style="width: 340px; margin-top: 13px ;" @click="() => $inertia.get(route(cancelRoute))" class="bg-gray-900 text-white">
                Cancelled
            </a-button>
        </a-badge>
    </a-card>
</template>

<script>
export default {
    props: {
        title: String,
        pending: Number,
        approved: Number,
        cancelled: Number,
        pendingRoute: String,
        approvedRoute: String,
        cancelRoute: String
    }
}
</script>