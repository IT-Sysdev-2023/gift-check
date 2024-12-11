<template>
    <a-breadcrumb class="mb-4">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Fad Requsition Details</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
                <a-input-search allow-clear enter-button v-model:value="receivingIndexSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>

        <a-table bordered size="small" :data-source="record.data" :columns="columns" :pagination="false" style="margin-top:10px">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'setup'">
                    <a-button @click="setup(record)">
                        <template #icon>
                            <FastForwardOutlined />
                       </template>
                        Setup Requsition
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination :datarecords="record" class="mt-5"/>
    </a-card>
    <!-- {{ record }} -->
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
export default {
  components: { Pagination },
    layout: AuthenticatedLayout,

    props: {
        record: Object,
        columns: Array,
    },
    data(){
        return {
            receivingIndexSearch: ''
        }
    },
    watch:{
        receivingIndexSearch(search){
            console.log(search);
            this.$inertia.get(route('iad.receiving'),{
                search:search
            },{
                preserveState: true
            })
        }
    },

    methods: {
        setup(data) {
            this.$inertia.get(route('iad.setup.receiving'), {
                requisId: data.req_no,
            }, {
                onSuccess: (res) => {
                    if (res.props.flash.status === 'error') {
                        notification[res.props.flash.status]({
                            message: res.props.flash.title,
                            description: res.props.flash.msg,
                        });
                    }
                }
            });
        }
    }
}
</script>
