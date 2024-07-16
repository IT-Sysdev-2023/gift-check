<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
           <PromoGCCard/>
        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
        </a-col>
        <a-col :span="8">
            <!-- <PromoGCCard/> -->
        </a-col>
    </a-row>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: Authenticatedlayout,
};
</script>

