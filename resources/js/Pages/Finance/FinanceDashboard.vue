<template>

    <a-row :gutter="[16, 16]">
        <a-col :span="8">
           <PromoGCCard :count="count"/>
        </a-col>
        <a-col :span="8">
            <special-pending-request-finance :count="count.specialGcRequest" :pendingExGcRequest :columns :pendingInGcRequest :currentbudget/>
            <!-- <PromoGCCard/> -->
        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
             <budget-request-approval :count="count"/>
        </a-col>
    </a-row>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: Authenticatedlayout,
    props: {
        count: Array,
        pendingExGcRequest:Object,
        pendingInGcRequest:Object,
        columns:Object,
        currentbudget:Object,
    }
};
</script>

