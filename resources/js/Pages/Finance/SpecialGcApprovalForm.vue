<template>
    <div>
        <a-card :title="type == 'external' ? 'Special External GC Request Approval Form' : 'Special Internal GC Request Approval Form'">
            <a-row :gutter="[16, 16]">
                <a-col :span="12"><a-card>{{ type }}</a-card></a-col>
                <a-col :span="12"><a-card>asdasd</a-card></a-col>
            </a-row>
        </a-card>
    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        type: Array
    },



}
</script>
