<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="12">

        </a-col>
        <a-col :span="12">
            <GcReceiving/>
        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout
}
</script>
