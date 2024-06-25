<template>

    <Head title="Store Sales" />
    <a-card>
        <a-card class="mb-2" title="Treasury Sales"></a-card>
        <div class="flex justify-end">
            <a-input-search class="mt-5 mb-5" v-model:value="search" placeholder="input search text here."
                style="width: 300px" @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" :columns="columns" size="small" :pagination="false">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'View'">
                    <a-button @click="viewDetails(record)">
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>

        <a-modal v-model:open="open" width="80%" style="top: 65px" :title="title" :confirm-loading="confirmLoading"  @ok="handleOk">
            <p>{{ selectedData }}</p>
        </a-modal>

        <pagination class="mt-5" :datarecords="data" />
    </a-card>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import debounce from "lodash/debounce";
import { PlusOutlined } from '@ant-design/icons-vue';

export default {
    layout: AuthenticatedLayout,
    PlusOutlined,
    props: {
        data: Array,
        columns: Array,
    },
    data() {
        return {
            search: '',
            open: false,
            selectedData: [],
        }
    },
    methods: {
        showModal() {
            this.open = true;
        },
        handleOk() {
            this.open = false;
        },
        handleCancel() {
            this.open = false;
        },
        viewDetails(data) {
            axios.get(route('view.treasury.sales'), {
                params: {
                    id: data.insp_trid,
                }
            }).then(response => {
                this.open = true;
                this.selectedData=response.data;
            })
        }

    },
    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                this.$inertia.get(route("sales.treasury.sales"), {
                    search: this.search
                }, {
                    preserveState: true,
                });
            }, 600),
        },
    },
}
</script>
