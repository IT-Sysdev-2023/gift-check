<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <promo-gc-request-card :counts="count"/>
            </a-col>
            <a-col :span="8">
              

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
