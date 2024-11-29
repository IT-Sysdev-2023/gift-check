<template>
    <a-card :title="title" class="mb-5">
        <a-badge :count="pending" class="mb-2">
            <a-button
                style="width: 340px"
                :overflow-count="Infinity"
                type="primary"
                :disabled="pending === 0"
                @click="() => $inertia.get(route(pRoute))"
                danger
            >
                {{pendingLabel ?? 'Pending'}} {{ pextension ? `(${pextension})` : "" }}
            </a-button>
        </a-badge>

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
                {{approvedLabel ?? 'Approved'}} {{ aextension ? `(${aextension})` : "" }}
            </a-button>
        </a-badge>
        <slot />
        <div v-if="spexgcreleased === true">
            <a-badge
                class="mb-2"
                :count="countspexgc"
                :overflow-count="Infinity"
                :number-style="{ backgroundColor: '#1677ff' }"
            >
                <a-button
                    :disabled="approved === 0"
                    style="width: 340px"
                    type="primary"
                    @click="() => $inertia.get(route('marketing.releasedspexgc.releasedspexgc'))"
                >
                    Released GC {{ aextension ? `(${aextension})` : "" }}
                </a-button>
            </a-badge>
        </div>
       
        <a-badge
            class="mb-2"
            :count="cancelled"
            :overflow-count="Infinity"
            :number-style="{ backgroundColor: '#111827' }"
        >
            <a-button
                :disabled="cancelled === 0"
                style="width: 340px"
                @click="() => $inertia.get(route(cRoute))"
                class="bg-gray-900 text-white"
            >
                {{cancelledLabel ?? 'Cancelled'}} {{ cextension ? `(${cextension})` : "" }}
            </a-button>
        </a-badge>
    </a-card>
</template>

<script setup>
import { stringType } from "ant-design-vue/es/_util/type";
import axios from "axios";
import { onMounted, ref } from "vue";

defineProps({
    title: String,
    
    //label
    pendingLabel: String,
    approvedLabel: String,
    cancelledLabel: String,

    pending: Number,
    approved: Number,
    cancelled: Number,
    pRoute: String,
    aRoute: String,
    cRoute: String,
    pextension: String,
    aextension: String,
    cextension: String,
    spexgcreleased: Boolean,
});

const countspexgc = ref("");

onMounted(async () => {
    const response = await axios.get(route("marketing.releasedspexgc.count"));
    countspexgc.value = response.data.count;
});
</script>
