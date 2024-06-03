<template>
    <a-table :data-source="data.data" :columns="columns" bordered size="small" :pagination="false">
    </a-table>
    
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
    }
}
</script>
