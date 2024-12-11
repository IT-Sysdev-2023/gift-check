<template>
    <a-breadcrumb class="mb-3">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Received Gift Check</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
                <a-input-search allow-clear enter-button v-model:value="receivedSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>

        <a-table size="small" :data-source="record.data" :columns="columns" :pagination="false" style="margin-top:10px"/>
        <pagination-resource :datarecords="record" />
    </a-card>
    <!-- {{ record }} -->
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        columns: Array
    },
   data(){
    return {
        receivedSearch: ''
    }
   },
    watch:{
        receivedSearch(search){
            console.log(search);
            this.$inertia.get(route('custodian.received.gc'),{
                search:search
            },{
                preserveState: true
            });
        }
    }

}
</script>
