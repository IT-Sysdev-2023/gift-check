<template>
    <PromoRequestTable :record="data" :columns="columns" :title="'Pending Promo Request Table'"/>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
    },

};
</script>

