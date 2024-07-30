<template>
    <PromoRequestTable
    :denomination="denomination"
    :details="details"
    :activeKey="activeKey"
    :record="data"
    :columns="columns"
    :reqid="reqid"
    :title="'Pending Promo Request Table'"
    />
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
        activeKey: String,
        details: Object,
        denomination: Object,
        reqid: Number,
    },

};
</script>

