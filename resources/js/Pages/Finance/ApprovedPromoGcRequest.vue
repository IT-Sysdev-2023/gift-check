<template>
        <ApprovedPromoGCTable :records="data" :columns="columns"/>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
    }
};
</script>
