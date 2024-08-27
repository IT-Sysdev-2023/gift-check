<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <store-gc-request-card/>
            </a-col>
            <a-col :span="8">

            </a-col>
            <a-col :span="8">

            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';



</script>
