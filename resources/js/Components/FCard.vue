<template>
    <a-card :title="title" class="mb-5">

        <slot />
        
        <a-badge class="mb-2" :count="approved" :overflow-count="Infinity"
            :number-style="{ backgroundColor: '#1677ff' }">
            <a-button :disabled="approved === 0" style="width: 340px" type="primary"
                @click="() => $inertia.get(route(aRoute))">
                {{ approvedLabel ?? "Approved" }}
                {{ aextension ? `(${aextension})` : "" }}
            </a-button>
        </a-badge>

        <div v-if="spexgcreleased === true">
            <a-badge class="mb-2" :count="countspexgc" :overflow-count="Infinity"
                :number-style="{ backgroundColor: '#1677ff' }">
                <a-button :disabled="approved === 0" style="width: 340px" type="primary" @click="
                    () =>
                        $inertia.get(
                            route(
                                'marketing.releasedspexgc.releasedspexgc',
                            ),
                        )
                ">
                    Released GC {{ aextension ? `(${aextension})` : "" }}
                </a-button>
            </a-badge>
        </div>

        <a-badge class="mb-2" :count="cancelled" :overflow-count="Infinity"
            :number-style="{ backgroundColor: '#111827' }">
            <a-button :disabled="cancelled === 0" style="width: 340px" @click="() => $inertia.get(route(cRoute))"
                class="bg-gray-900 text-white">
                {{ cancelledLabel ?? "Cancelled" }}
                {{ cextension ? `(${cextension})` : "" }}
            </a-button>
        </a-badge>
    </a-card>
</template>

<script setup>
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

defineProps({
    title: String,

    //label
    pendingLabel: String,
    approvedLabel: String,
    cancelledLabel: String,

    pending: [Number, String],
    approved: [Number, String],
    cancelled: [Number, String],
    pRoute: String,
    aRoute: String,
    cRoute: String,
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
