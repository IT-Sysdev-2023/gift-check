<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <special-external-gc-request/>
        </a-col>
        <a-col :span="8">

        </a-col>
        <a-col :span="8">

        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,

}
</script>
