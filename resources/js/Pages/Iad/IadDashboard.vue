<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="12">

        </a-col>
        <a-col :span="12">
            <GcReceiving :counts="counts"/>
        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        counts: Object,
    }
}
</script>
