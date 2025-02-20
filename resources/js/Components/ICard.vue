<template>
    <a-card :title="title" class="mb-5">
        <a-badge
            class="mb-2"
            :count="approved"
            :overflow-count="Infinity"
            :number-style="{ backgroundColor: '#1677ff' }"
        >
            <a-button
                :disabled="approved === 0"
                style="width: 340px"
                type="primary"
                @click="() => $inertia.get(route(aRoute))"
            >
                {{ approvedLabel ?? "Approved Gc" }}
                {{ aextension ? `(${aextension})` : "" }}
            </a-button>
        </a-badge>
        <a-badge
            class="mb-2"
            :count="recrev"
            :overflow-count="Infinity"
            :number-style="{ backgroundColor: '#1677ff' }"
        >
            <a-button
                :disabled="recrev === 0"
                style="width: 340px"
                type="primary"
                @click="() => $inertia.get(route(rRoute))"
            >
                {{ revrecLabel ?? "Reviewed Gc" }}
                {{ aextension ? `(${aextension})` : "" }}
            </a-button>
        </a-badge>
        <slot />
    </a-card>
</template>

<script setup>
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

defineProps({
    title: String,

    //label
    revrecLabel: String,
    approvedLabel: String,
    cancelledLabel: String,

    recrev: [Number, String],
    approved: [Number, String],
    pRoute: String,
    aRoute: String,
    rRoute: String,
    pextension: String,
    aextension: String,
    cextension: String,
    spexgcreleased: Boolean,
});

const page = usePage().props;

const countspexgc = ref("");

onMounted(async () => {
    if (page.auth.user.user_id == "6") {
        const response = await axios.get(
            route("marketing.releasedspexgc.count"),
        );
        countspexgc.value = response.data.count;
    }
});
</script>
