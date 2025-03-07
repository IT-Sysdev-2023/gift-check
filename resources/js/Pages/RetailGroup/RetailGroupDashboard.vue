<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="9">
                <m-card
                pRoute="retailgroup.pending"
                aRoute="retailgroup.approved"
                :pending="count.pending"
                :approved="count.approved"
                />
            </a-col>
            <a-col :span="15">
                <a-card>
                    <a-statistic  :value="count.promo" :precision="2"
                        :value-style="{ color: '#3f8600' }" style="margin-right: 50px">
                        <template #title>
                            <p class="font-bold text-lg">Promo GC Approved (Total Amount)</p>
                        </template>
                        <template #prefix>
                            ₱
                        </template>
                    </a-statistic>
                </a-card>
            </a-col>
            <a-col :span="8">

            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    count: Number
})

</script>
