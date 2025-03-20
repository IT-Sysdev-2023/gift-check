<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-range-picker style="width: 100%;" @change="handleDateRangeChange" />
                    <a-button block class="mt-4" @click="generate" style="background-color:#1890ff;color:white">
                        Generate Special Reviewed Excel
                    </a-button>
                </a-col>
                <a-col :span="14">

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

const handleDateRangeChange = (obj: object, str: any) => {
    dateRange.value = str;
}

const generate = () => {
    window.location.href = route('iad.excel.generate.special', {
        date: dateRange.value
    })
}
</script>
