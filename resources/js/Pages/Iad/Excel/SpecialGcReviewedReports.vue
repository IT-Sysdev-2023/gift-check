<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-range-picker style="width: 100%;" v-model:value="dateRange" />
                    <a-button block class="mt-4" @click="generate">
                        Generate Special Reviewed Excel
                    </a-button>
                </a-col>
                <a-col :span="14">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, distinctio dolorum ad veniam accusamus
                    nulla laboriosam aliquid accusantium nisi? Quis, itaque! Vero voluptatum voluptatibus voluptatem
                    provident voluptas praesentium necessitatibus distinctio.
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/core';
import { Dayjs } from 'dayjs';
import { ref } from 'vue';


const dateRange = ref<Dayjs>();

const generate = () => {
    router.get(route('iad.generate.s'))
}
</script>
